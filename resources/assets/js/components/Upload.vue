<template>
  <div class="container">
      <div class="modal fade" id="UploadMusic" ref="modal" tabindex="-1" role="dialog" aria-labelledby="UploadMusicLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <form @submit.prevent="uploadMusic" enctype='multipart/form-data'>
              <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="UploadMusicLabel">Upload New Music</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>

              <div class="modal-body" :key="formReset">
                <div class="form-group file_container">
                  <input type="file" ref="fileInput" multiple="multiple" @change="onFileSelected" style="display:none">
                  <button @click="$refs.fileInput.click()" type="button" class="btn btn-primary btn-block">{{btnText}}</button>
                  <ul><li v-for="filename in filenameList" :key="filename" accept=".mp3">{{filename}}</li></ul>
                </div>
                <div class="form-group playlist_container">
                  <h4>Choose Playlist(s)</h4>
                  <ul style>
                    <playlist :playlist="playlist" :display="'checkbox'"></playlist>
                  </ul>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" @click="resetModal" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="button" @click="uploadMusic" class="btn btn-primary">Upload</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
  import {mapState, mapGetters} from 'vuex'
  import playlist from './reusables/Playlist.vue'

  export default{
    components: {
      playlist
    },
    data(){
      return{
        formReset: 0,
        toUpload: new FormData(),
        btnText: 'Choose File(s)',
        filenameList: [],
        selectedFile: null,
        playlist: null,
      }
    },
    methods:{
      loadPlaylist(){
        axios.get(window.location.origin+'/api/music').then(({data}) => (this.playlist = data));
        //axios.get(window.location.origin+'/api/music/get_user_data');
      },
      onFileSelected(event){
        this.filenameList = [];
        this.toUpload = new FormData();
        this.selectedFile = event.target.files;

        this.btnText = event.target.files.length+' File(s) Selected';
        if(event.target.files.length == 0){
          this.filenameList = ['No File Selected'];
        }else{
            for(let i=0; i<event.target.files.length; i++){
              this.filenameList.push(event.target.files[i].name);
              this.toUpload.append('file_name['+i+']', event.target.files[i]);
            }
        }
      },
      resetModal(){
        this.formReset = 1;
        this.$store.commit('RESET_SELECTED_PLAYLIST');
        this.toUpload = new FormData();
        this.filenameList = [];
        this.btnText = 'Choose File(s)';
      },
      uploadMusic(){
        let data = JSON.parse(JSON.stringify(this.selectedPlaylist)); //console.log(data);
        if(this.selectedFile === null || this.selectedFile.length === 0){
          this.$alert('No Music Choosen', '', 'warning');
        }else if(this.selectedPlaylist.length == 0){
          this.$alert('Must Pick At Least 1 Playlist', '', 'warning');
        }else{
          this.toUpload.append('playlist', this.selectedPlaylist); console.log(this.toUpload);
          axios.post(window.location.origin+'/api/music', this.toUpload, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }).then(res=>{
            this.resetModal();
            this.$alert('Upload Succesful', '', 'success');
          }).catch(err=>{
            this.$alert('Upload Failed: '+err.message, '', 'error');
          });
        }
      },
    },
    computed: {
      ...mapState([
        'selectedPlaylist'
      ]),
      ...mapGetters([
        'getSelectedPlaylist'
      ]),
      selectedPlaylist(){ //console.log(store.state.selectedPlaylist);
        return this.$store.state.selectedPlaylist;
      }
    },
    mounted(){
      this.loadPlaylist();
    }
  }
</script>

<style lang="css">
  .playlist_container{
    max-height: 500px;
    overflow: auto;
  }

  #filename_container{
    max-height: 150px;
  }
</style>