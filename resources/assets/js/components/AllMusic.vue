<template>
  <div class="container">
    <add-to-playlist
      :selectedMusicID="modal_music_id"
      :selectedMusicJudul="modal_music_judul"
      v-on:success="successAlert('Playlist')"
    ></add-to-playlist>
    <add-to-wishlist
      :selectedMusicID="modal_music_id"
      :selectedMusicJudul="modal_music_judul"
      v-on:success="successAlert('Wishlist')"
    ></add-to-wishlist>
    
    <!--<section class="content container-fluid">-->
      <div class="row justify-content-center mt-4 mb-4 h-100">
        <div class="col-12">
          <div class="card my-auto">
            <div class="container fixed-content">
              <h2 v-if="$route.params.playlist_id">
                {{($route.params.playlist_name ? $route.params.playlist_name.replace('-', ' ') : '')}} <br>
                <small>{{totalMusics}} musics</small>
              </h2>
              <h2 v-else>
                All Music <br>
                <small>{{totalMusics}} musics</small>
              </h2>
              <div class="card-header">
                <div class="button-container">
                  <button class="btn btn-primary" data-toggle="modal" data-target="#CreatePlaylist"
                    v-if="userLogin.hak_akses == 1 || userLogin.user_type == 1"
                    :id="($route.params.playlist_id ? $route.params.playlist_id : 0)"
                  >Create Child Playlist</button>
                  <button class="btn btn-primary" 
                    v-if="$route.params.playlist_id && (userLogin.hak_akses == 1 || userLogin.user_type == 1)"
                    @click="renamePlaylist($route.params.playlist_id, $route.params.playlist_name)"
                  >Rename</button>
                  <button class="btn btn-danger" 
                    v-if="$route.params.playlist_id && (userLogin.hak_akses == 1 || userLogin.user_type == 1)"
                    @click="deletePlaylist($route.params.playlist_id, $route.params.playlist_name)"
                  >Delete</button>
                </div>
                <div class="search-container form-group has-feedback has-search">
                  <span class="glyphicon glyphicon-search form-control-feedback"></span>
                  <input type="text" class="form-control" placeholder="Search" v-model="searchContent">
                  <!--<vue-fuse :keys="keys" :list="bikes" :defaultAll="false" :eventName="bikesChanged"></vue-fuse>-->
                </div>
              </div>
              <div class="card-tools">
                <audio-player 
                  :file="file"
                  :file_id="file_id"
                  :judul="judul"
                  :autoPlay="autoPlay" 
                  v-on:playNext="nextMusic"
                ></audio-player>
              </div>
              <table class="table table-head-fixed" id="header-table">
                <colgroup>
                  <col width="652px">
                  <col width="113px">
                  <col width="253px">
                  <col width="152px">
                </colgroup>
                <thead>
                  <tr>
                    <th @click="loadMusics(1, 'judul@ASC')" title="Sort By Title" colspan="2" class="headerButton">Title</th>
                    <th v-if="$route.params.playlist_id">Playlist</th>
                    <th @click="loadMusics(1, 'created_at@DESC')" title="Sort By Time Of Upload" class="headerButton">
                      {{$route.params.playlist_id ? 'Added At' : 'Uploaded At'}}
                    </th>
                    <th>Modify</th>
                  </tr>
                </thead>
              </table>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0" style="height: 100%;">
              <!--<pagination :data="musics" @pagination-change-page="getResults"></pagination>-->
              <table class="table table-head-fixed" id="content-table" :num="searchMusic.length">
                <!--<draggable v-model="musics" :options="{group:{judul:musics.judul}, pull:'clone'}" @start="drag=true" :element="'tbody'">-->
                <tbody>
                  <template v-if="searchMusic !== '' && searchMusic != 0">
                    <tr v-for="(music,index) in searchMusic" :key="index" hover:bg-blue px-4 py2>
                      <td>{{music.judul}}</td>
                      <td>
                        <button 
                          type="button" class="btn btn-primary playlist-play-btn"
                          @click="playAudio(music.judul, music.filename, music.id, index)"
                        >
                          Play
                          <i class="fas fa-play-circle nav-icon"></i>
                        </button>
                      </td>
                      <td v-if="$route.params.playlist_id" class="text-capitalize">{{music.folder_path}}</td>
                      <td>{{formatDatetime(music.created_at)}}</td>
                      <td>
                        <div v-if="userLogin !== null" class="modify-btn-container">
                          <a v-if="userLogin.hak_akses == 1 || userLogin.user_type == 1" class="modify-btn" title="Add To Playlist"
                            data-toggle="modal" data-target="#PlaylistModal"
                            v-on:click="openExtraModal(music.id, music.judul)"
                          >
                            <i class="fas fa-compact-disc color-cyan fa-fw fa-lg"></i>
                          </a>
                          <a class="modify-btn" title="Add To Wishlist"
                            data-toggle="modal" data-target="#AddWishlistModal"
                            v-on:click="openExtraModal(music.id, music.judul)"
                          >
                            <i class="fa fa-folder-plus color-purple fa-fw fa-lg"></i>
                          </a>
                          <a v-if="userLogin.hak_akses == 1 || userLogin.user_type == 1" class="modify-btn" title="Download" 
                            v-on:click="download(music.judul, music.filename, music.id)"
                          >
                            <i class="fa fa-download color-green fa-fw fa-lg"></i>
                          </a>
                          <a v-if="userLogin.hak_akses == 1 || userLogin.user_type == 1" class="modify-btn" title="Delete" 
                            v-on:click="deleteMusic(music.id, music.judul)"
                          >
                            <i class="fa fa-trash color-red fa-fw fa-lg"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr>
                      <td colspan="100%">
                        <h3 class="text-center" v-if="$route.params.playlist_id && musics.length == 0">Playlist Is Empty</h3>
                        <h3 class="text-center" v-else-if="!$route.params.playlist_id && musics.length == 0">Music Bank Is Empty</h3>
                        <h3 class="text-center" v-else>No Match Found</h3>
                      </td>
                    </tr>
                  </template>
                </tbody>
                <observer v-on:intersect="intersectEvent"/>
                <!--</draggable>-->
              </table>
            </div>
            <div class="card-footer">
              <!--<pagination :data="musics" @pagination-change-page="getResults"></pagination>-->
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>        
      </div>
    <!--</section>-->
  </div>
</template>

<script>
  import moment from 'moment';
  import AudioPlayer from './reusables/PlayAudio.vue';
  import AddToPlaylist from './AddToPlaylist.vue';
  import AddToWishlist from './AddToWishlist.vue';
  import draggable from 'vuedraggable'
  import observer from './reusables/Observer.vue';
  import ModalIdle from './reusables/ModalIdle.vue';

  export default {
    components: {
      AudioPlayer,
      AddToPlaylist,
      AddToWishlist,
      draggable,
      observer,
      ModalIdle,
    },
    data(){
      return{
        userLogin: {
          hak_akses: 0,
        },

        page: 1,
        last_page: 1,

        file: null,
        file_id: null,
        judul: null,

        autoPlay: false,
        totalMusics: 0,
        musics:[],
        fileArr: [],
        judulArr: [],
        dataLoaded: 0,

        modal_music_id: 0,
        modal_music_judul: '',

        sortParam: 'created_at@DESC',

        searchContent: '',

        dragMusic: [],
      }
    },
    computed:{
      searchMusic: function(){
        if(this.dataLoaded == 1){
          /*if(typeof(this.musics) === 'object' && this.musics !== null){
            this.musics = Object.keys(this.musics).map(function(key){
              return [Number(key), this.musics[key]];
            });
          }*/
          return this.musics.filter((item) => {
            return item.judul.toLowerCase().match(this.searchContent.toLowerCase());
          });
        }else{
          return [];
        }
      },
      isIdle(){
        return this.$store.state.idleVue.isIdle;
      }
    },
    methods:{
      successAlert(type){
        $('#PlaylistModal').modal('hide');
        this.$alert(this.modal_music_judul+' added to selected '+type, '', 'success');
      },
      loadTotalMusics(sortingParam){
        if(this.$route.params.playlist_id){ //console.log('total playlist');
          axios.get(window.location.origin+'/api/music/totalPlaylist_'+this.$route.params.playlist_id)
            .then(({data}) => { //console.log(data);
              if(data.status == 403){
                location.reload();
              }else{
                this.totalMusics = data;
              }
            });
        }else{ //console.log('total music');
          axios.get(window.location.origin+'/api/music/totalMusicList')
            .then(({data}) => { //console.log(data);
              if(data.error){
                location.reload();
              }else{
                this.totalMusics = data;
              }
            });
        }
      },
      loadMusics(page, sortingParam){
        this.page = page;
        if(sortingParam == undefined){
          sortingParam = 'created_at@DESC';
        }
        this.sortParam = sortingParam;

        if(this.$route.params.playlist_id){
          axios.get(window.location.origin+'/api/music/playlist_'+sortingParam+'-'+this.$route.params.playlist_id+'?page='+this.page)
            .then(({data}) => { 
              if(data.error){
                location.reload();
              }else{
                this.musics = [...this.musics, ...data.data];
                this.last_page = data.last_page;
                //this.file = data.data[0].filename;
                for(let item of data.data){ //console.log(item.filepath);
                  this.fileArr.push(item.filename);
                  this.judulArr.push(item.judul);
                }
                this.dataLoaded = 1;
              }
            });
        }else{
          let index = sortingParam.indexOf('@'); //console.log(index);
          sortingParam = sortingParam.slice(0, index); //console.log(sortingParam);

          if(sortingParam == 'judul'){
            axios.get(window.location.origin+'/api/music/getMusicListByTitle?page='+this.page).then(({data}) => {
              if(data.error){
                location.reload();
              }else{ 
                this.musics = [...this.musics, ...data.data];
                this.last_page = data.last_page;
                for(let item of data.data){
                  this.fileArr.push(item.filename);
                  this.judulArr.push(item.judul);
                }
                this.dataLoaded = 1;
              }
            });
          }else if(sortingParam == 'created_at'){
            axios.get(window.location.origin+'/api/music/getMusicListByUploadDate?page='+this.page).then(({data}) => { 
              if(data.error){
                location.reload();
              }else{
                this.musics = [...this.musics, ...data.data];
                this.last_page = data.last_page;
                //this.musics = [].concat(this.musics, data.data);
                for(let item of data.data){
                  this.fileArr.push(item.filename);
                  this.judulArr.push(item.judul);
                }
                this.dataLoaded = 1;
              }
            });
          }
        }
      },
      async intersectEvent(){
        if(this.page < this.last_page){
          console.log('intersect');
          this.loadMusics(++this.page);
        }
      },
      formatDatetime(datetime){
        return moment(String(datetime)).format('llll');
      },
      playAudio(judul, path, id, index){ console.log('play');
        if(path){
          this.judul = judul;
          this.file = path;
          this.file_id = id;
          this.autoPlay = true;
          this.playingIndex = index;

          let postToLog = {
            'judul' : judul,
            'music_id' : id,
            'action': 'play',
            'filename' : path.split('/')[path.split('/').length - 1],
          }
          axios.post(window.location.origin+'/api/log', postToLog)
          .then(({ data }) => {})
          .catch(({error}) => {
            console.error(error);
            if(error.error){
              location.reload();
            }
          });
        }else{
          this.$alert('No file to play', '', 'error')
        }
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
              'action': 'download',
              'filename' : path.split('/')[path.split('/').length - 1], //path.replace('/storage/uploadedMusic/', ''),
            }
            axios.post(window.location.origin+'/api/log', postToLog)
            .then(({ data }) => {})
            .catch(({error}) => {
              console.error(error);
              if(error.error){
                location.reload();
              }
            });
        })
        .catch(({error}) => {
          console.log(error);
          if(error.error){
            location.reload();
          }
        });
      },
      addToWishlist(music_id, music_judul){
        this.$confirm('Add '+music_judul+' to your wishlist?', '', 'question')
        .then( () => {
          let postToWishlist = {
            'music_id' : music_id,
          }
          axios.post(window.location.origin+'/api/wishlist', postToWishlist)
            .then(({ response }) => {
              this.$alert(music_judul+' added to your wishlist', '', 'success')
            })
            .catch(({error}) => {
              console.error(error);
              if(error.error){
                location.reload();
              }
            });
        })
        .catch(({error}) => {
          console.error(error);
          if(error.error){
            location.reload();
          }
        });
      },
      openExtraModal(music_id, music_judul){
        this.modal_music_id = music_id;
        this.modal_music_judul = music_judul;
      },
      deleteMusic(music_id, music_judul){
        let playlist = 'Music Bank permanently';
        let confirm_type = 'warning';

        if(this.$route.params.playlist_id){
          playlist = this.$options.filters.capitalize(this.$route.params.playlist_title)+' playlist';
          confirm_type = 'question';
        }

        this.$confirm('Delete '+music_judul+' from '+playlist+'?', '', confirm_type)
          .then( () => {
            if(this.$route.params.playlist_id){
              let patchMusic = {
                'act': 'remove_from_playlist',
                'playlist_detail_id': music_id,
                'playlist_id': this.$route.params.playlist_id
              }
              axios.post(window.location.origin+'/api/music', patchMusic)
                .then(({response}) => {
                  this.$alert(music_judul+' removed from '+playlist+' playlist', '', 'success');
                  this.loadMusics(1);
                })
                .catch(({error}) => {
                  this.$alert(error.message, '', 'error');
                  if(error.error){
                    location.reload();
                  }
                });
            }else{
              this.$confirm('This delete action cannot be undone!', '', 'warning')
                .then( () => {
                  axios.delete(window.location.origin+'/api/music/'+music_id)
                    .then(({response}) => {
                      this.$alert('Delete Successful', '', 'success');
                      this.loadMusics(1);
                    })
                    .catch(({error}) => {
                      this.$alert(error.message, '', 'error');
                      if(error.error){
                        location.reload();
                      }
                    });
                });
            }
          })
          .catch(({error}) => {
            console.error(error);
            if(error.error){
              location.reload();
            }
          });
      },
      deletePlaylist(id, nama_playlist){
        this.$confirm('Confirm Delete Playlist '+nama_playlist+' and all the content?', '', 'error')
          .then(res => {
            axios.delete(window.location.origin+'/api/playlist/'+id).then(result => {
              this.$alert('Delete Success', '', 'success');
              location.reload();
            });
          }
        );
      },
      renamePlaylist(id, nama_playlist){
        //this.$confirm('Confirm Delete Playlist '+nama_playlist+' and all the content?', '', 'error')
        this.$prompt("Rename Playlist", nama_playlist).then((text) => { //console.log(text);
          //.then(res => {
            let renamePlaylist = {
              'act' : 'rename_playlist',
              'playlist_id' : id,
              'playlist_new_name' : text,
            }
            axios.post(window.location.origin+'/api/playlist', renamePlaylist).then(result => {
              this.$alert('Rename Success', '', 'success');
              location.reload();
            });
          }
        );
      }
    },
    watch:{
      '$route.params.playlist_id': function(playlist_id){
        this.musics = [];
        this.loadMusics(1);
        this.loadTotalMusics();
      }, 
    },
    mounted(){ //this.$session.start(30000);
      axios.get(window.location.origin+'/api/user/getUserLogin').then(({data}) => {
        this.userLogin = data;
      });
      this.loadMusics(1);
      this.loadTotalMusics(); //console.log(this.$session.getAll());
    }
  }
</script>

<style lang="scss" scoped>
  .fixed-content{
    position: fixed;
    z-index: 99;
    background-color: #ecf0f5;
    padding: 0;

    & > h2{
      margin-bottom: 0;
    }
  }

  .waveform-container{
    display: inline-flex;
  }

  .audio-player{
    width: 500px;
  }

  .card-tools, .card-header{
    text-align: right;
  }

  .button-container{
    float: left;
  }

  #header-table{
    margin-bottom: 0;
  }

  #content-table{
    margin-top: 230px;
  }

  .headerButton:hover{
    background-color: #d5eaf5;
    cursor: pointer;
    transition: all .4s ease;
    -webkit-transition: all .4s ease;
  }

  .search-container{
    display: inline-block;
    width: 250px;
    margin-bottom: 6px;
  }

  .has-search .form-control-feedback{
    right: initial;
    left: 0;
    color: #ccc;
  }
  .has-search .form-control{
      padding-right: 12px;
      padding-left: 34px;
  }
</style>