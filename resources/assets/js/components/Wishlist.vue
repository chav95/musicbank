<template>
  <div class="container">
    <section class="content-header">
      <h1>
        Wishlist <br>
        <small>Songs Added To Your Wishlist</small>
      </h1>
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
                <audio-player 
                  :file="file"
                  :file_id="file_id"
                  :judul="judul"
                  :autoPlay="autoPlay" 
                  v-on:playNext="nextMusic">
                </audio-player>
              </div>
              
              <table class="table table-head-fixed">
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
                    <tr  v-for="(wishlist,index) in wishlist.data" :key="index" hover:bg-blue px-4 py2>
                      <td>{{wishlist.judul}}</td>
                      <td>
                        <button 
                          type="button" class="btn btn-primary playlist-play-btn"
                          @click="playAudio(wishlist.judul, '/storage/'+wishlist.path, wishlist.id, index)"
                        >
                          Play
                          <i class="fas fa-play-circle nav-icon"></i>
                        </button>
                      </td>
                      <td>{{formatDatetime(wishlist.created_at)}}</td>
                      <td>
                        <div class="modify-btn-container">
                          <a class="modify-btn" title="Download" v-on:click="download(wishlist.judul, '/storage/'+wishlist.path, wishlist.id)"><i class="fa fa-download color-green fa-fw fa-lg"></i></a>
                          <a class="modify-btn" title="Delete"><i class="fa fa-trash color-red fa-fw fa-lg"></i></a>
                        </div>
                      </td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr><td colspan="100%"><h3 class="text-center">Your Wishlist Is Empty</h3></td></tr>
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
  import AudioPlayer from './reusables/PlayAudio.vue';
  import moment from 'moment';
  import PlayAudioVue from './reusables/PlayAudio.vue';

  export default {
    components: {
      AudioPlayer,
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
            axios.get(window.location.origin+'/api/wishlist').then(({data}) => {
                this.wishlist = data;
                for(let item of data.data){
                    this.fileArr.push('/storage/'+item.path);
                    this.judulArr.push(item.judul);
                }
            });
        },
        getResults(page = 1){
            axios.get(window.location.origin+'/api/wishlist?page=' + page)
                then(({response}) => {
                    this.wishlist = response.data; console.log(response.data)
                    for(let item of response.data){
                        this.fileArr.push('/storage/'+item.path);
                        this.judulArr.push(item.judul);
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
    },
    watch: {
      '$route.params.playlist_id': function(playlist_id){
        this.loadWishlist();
      }
    },
    mounted() {
      this.loadWishlist();
    }
  }
</script>

<style scoped>
  .card-tools{
    text-align: right;
  }
</style>