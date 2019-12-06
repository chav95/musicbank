<template>
  <div class="container">
    <section class="content-header">
      <h1>
        {{($route.params.wishlist_id ? this.$options.filters.capitalize($route.params.wishlist_title) : 'Wishlist')}} <br>
        <small>{{($route.params.wishlist_id ? 'Music List' : 'Your Wishlist')}}</small>
      </h1>
      <router-link v-if="$route.params.wishlist_id" class="btn btn-primary" to="/wishlist">Back To Wishlist</router-link>
    </section>

    <section class="content container-fluid">
      <div class="row justify-content-center mt-4 mb-4 h-100">
        <div class="col-12">
          <div class="card my-auto">
            <div class="card-header">
              <pagination :data="wishlist" @pagination-change-page="getResults"></pagination>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0" style="height: 100%;">
              <div class="card-tools">
                <audio-player v-if="$route.params.wishlist_id"
                  :file="file"
                  :file_id="file_id"
                  :judul="judul"
                  :autoPlay="autoPlay" 
                  v-on:playNext="nextMusic">
                </audio-player>
              </div>
              
              <table v-if="$route.params.wishlist_id" class="table table-head-fixed">
                <thead>
                  <tr>
                    <th>Title</th>
                    <th></th>
                    <th>Uploaded At</th>
                    <th>Modify</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="wishlist.data.length > 0">
                    <tr v-for="(wishlist,index) in wishlist.data" :key="index" hover:bg-blue px-4 py2>
                      <td>{{wishlist.music.judul}}</td>
                      <td>
                        <button 
                          type="button" class="btn btn-primary playlist-play-btn"
                          @click="playAudio(wishlist.music.judul, '/storage/'+wishlist.music.path, wishlist.music.id, index)"
                        >
                          Play
                          <i class="fas fa-play-circle nav-icon"></i>
                        </button>
                      </td>
                      <td>{{formatDatetime(wishlist.created_at)}}</td>
                      <td>
                        <div class="modify-btn-container">
                          <a class="modify-btn" title="Download" 
                            v-on:click="download(wishlist.music.judul, '/storage/'+wishlist.music.path, wishlist.music.id)"
                          >
                            <i class="fa fa-download color-green fa-fw fa-lg"></i>
                          </a>
                          <a class="modify-btn" title="Delete"
                            v-on:click="deleteFromWishlist(wishlist.id, wishlist.music.judul, wishlist.wishlist_label)"
                          >
                            <i class="fa fa-trash color-red fa-fw fa-lg"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr><td colspan="100%"><h3 class="text-center">Your Wishlist Is Empty</h3></td></tr>
                  </template>
                </tbody>
              </table>

              <table v-else class="table table-head-fixed">
                <thead>
                  <tr>
                    <th colspan="2">Wishlist Name</th>
                    <th>Created At</th>
                    <th>Status</th>
                    <th>Modify</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="wishlist.data.length > 0">
                    <tr v-for="(item,index) in wishlist.data" :key="index" hover:bg-blue px-4 py2>
                      <td>{{item.wishlist_label}}</td>
                      <td>
                        <router-link class="btn btn-primary"
                          :to="{
                            name: 'wishlist',
                            params: {
                              'wishlist_id': item.id,
                              'wishlist_name': item.wishlist_label.replace(' ', '-'),
                              'wishlist_title': item.wishlist_label
                            }
                          }"
                        >Open</router-link>
                      </td>
                      <td>{{formatDatetime(item.created_at)}}</td>
                      <td><span :class="'status_'+item.status">
                        {{(item.status == 0 ? 'Pending' : (item.status == 1 ? 'Finished' : 'Canceled'))}}
                      </span></td>
                      <td>
                        <div v-if="item.status == 0">
                          <a class="modify-btn" title="Mark Wishlist As Finished"
                            v-on:click="openConfirmModal(item.id, item.wishlist_label, 1)"
                          >
                            <i class="fas fa-check-square color-green fa-fw fa-lg"></i>
                          </a>
                          <a class="modify-btn" title="Cancel Wishlist"
                            v-on:click="openConfirmModal(item.id, item.wishlist_label, -1)"
                          >
                            <i class="fas fa-window-close color-red fa-fw fa-lg"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr><td colspan="100%"><h3 class="text-center">You Have No Wishlist</h3></td></tr>
                  </template>
                </tbody>
              </table>
            </div>
            <div class="card-footer">
              <pagination :data="wishlist" @pagination-change-page="getResults"></pagination>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>        
      </div>
    </section>
  </div>
</template>

<script>
  import moment from 'moment';
  import AudioPlayer from './reusables/PlayAudio.vue';
  import AddToWishlist from './AddToWishlist.vue';

  export default {
    components: {
      AudioPlayer,
      AddToWishlist,
    },
    data(){
      return{
        file: null,
        file_id: null,
        judul: null,

        autoPlay: false,
        wishlist:{data:{}},
        fileArr: [],
        judulArr: [],
        //playingIndex: this.$route.params.playlist_id,
      }
    },
    methods:{
      loadWishlist(){
        this.fileArr = [];
        this.judulArr = [];
        
        if(this.$route.params.wishlist_id){
          axios.get(window.location.origin+'/api/wishlist/get_wishlist_detail-'+this.$route.params.wishlist_id).then(({data}) => {
            this.wishlist = data;
            for(let item of data.data){
              this.fileArr.push('/storage/'+item.music.path);
              this.judulArr.push(item.music.judul);
            }
          });
        }else{
          axios.get(window.location.origin+'/api/wishlist/get_wishlist').then(({data}) => {
            this.wishlist = data;
          });
        }
      },
      getResults(page = 1){
        axios.get(window.location.origin+'/api/wishlist?page=' + page)
          then(({response}) => {
            this.wishlist = response.data; //console.log(response.data)
            for(let item of response.data){
              this.fileArr.push('/storage/'+item.music.path);
              this.judulArr.push(item.music.judul);
            }
          });
      },
      formatDatetime(datetime){
        return moment(String(datetime)).format('llll');
      },
      playAudio(judul, path, id, index){
        this.judul = judul;
        this.file = path;
        this.file_id = id;
        this.autoPlay = true;
        this.playingIndex = index;
      },
      nextMusic(){
        let next = this.playingIndex+1;
        if(this.fileArr[next] == null){
          next = 0;
        }
        this.playingIndex = next;
        this.file = this.fileArr[next];
        this.judul = this.judulArr[next];
      },
      download(judul, path, id){
        axios.get(path, { responseType: 'blob' })
          .then(({ data }) => {
            const blob = new Blob([data], {type: 'audio/mp3'});
            let link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = judul;
            link.click();
            
            let postToLog = {
              'judul' : judul,
              'music_id' : id,
              'filename' : path.replace('/storage/uploadedMusic/', '')
            }
            axios.post(window.location.origin+'/api/log', postToLog)
            .then(({ data }) => {
                
            })
            .catch(error => console.error(error));
        })
        .catch(error => console.error(error));
      },
      deleteFromWishlist(wishlist_detail_id, music_judul, wishlist_label){
        this.$confirm('Delete '+music_judul+' from '+wishlist_label+' wishlist?', '', 'question')
          .then(() => {
            let deleteFromWishlist = {
              'act': 'delete_from_wishlist',
              'id': wishlist_detail_id
            };

            axios.post(window.location.origin+'/api/wishlist', deleteFromWishlist)
              .then(({response}) => {
                this.loadWishlist();
              });
          })
      },
      openConfirmModal(id, label, new_stat){console.log(id+' '+label+' '+new_stat);
        let stat_text = '';
        if(new_stat == 1){
          stat_text = 'Finished';
        }else if(new_stat == 0){
          stat_text = 'Pending';
        }else if(new_stat == -1){
          stat_text = 'Canceled';
        }

        this.$confirm('Mark '+label+' as '+stat_text+'?', '', 'question')
          .then(() => {
            let modifyWishlistStat = {
              'act': 'modify_wishlist_stat',
              'id': id,
              'status': new_stat,
            };

            axios.post(window.location.origin+'/api/wishlist', modifyWishlistStat)
              .then(({response}) => {
                this.loadWishlist();
              });
          })
      },
    },
    watch: {
      '$route.params.wishlist_id': function(wishlist_id){
        this.loadWishlist();
      }
    },
    mounted() {
      this.loadWishlist();
    }
  }
</script>

<style lang="scss" scoped>
  .card-tools{
    text-align: right;
  }

  .status{
    &_1{
      color: #38c172;
    }
    &_0{
      color: #f6993f;
    }
    &_-1{
      color: #e3342f;
    }
  }
</style>