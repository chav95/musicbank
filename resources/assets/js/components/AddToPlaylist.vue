<template>
  <div class="container">
      <div class="modal fade" id="PlaylistModal" ref="modal" tabindex="-1" role="dialog" aria-labelledby="PlaylistModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <form @submit.prevent="addToPlaylist" enctype='multipart/form-data'>
              <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="PlaylistModalLabel">Add {{selectedMusicJudul}} To Playlist</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>

              <div class="modal-body" :key="formReset">
                <div class="form-group playlist_container">
                  <h4>Choose Playlist(s)</h4>
                  <ul style>
                    <li v-for="item in playlist" :key="item.id" style="list-style-type: none">
                        <input type="checkbox" class="playlist"
                            v-model="selectedPlaylist"
                            :value="item.id" 
                            :id="'playlistPath_'+item.id"
                            :ref="'playlistPath_'+item.id"
                        />
                        <label :for ="'playlistPath_'+item.id" :ref="'label_'+item.id">
                            {{item.path | capitalize}}
                        </label>
                    </li>
                  </ul>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" @click="resetModal" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="button" @click="addToPlaylist" class="btn btn-primary">OK</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
  import {mapState, mapGetters} from 'vuex'

  export default{
    props: {
        selectedMusicID: Number,
        selectedMusicJudul: String,
    },
    data(){
      return{
        formReset: 0,
        playlist: [],
        selectedPlaylist: [],
      }
    },
    methods:{
        loadPlaylist(){
            axios.get(window.location.origin+'/api/playlist/playlistSelection').then(({data}) => (this.playlist = data));
        },
        resetModal(){
            this.formReset = 1;
        },
        addToPlaylist(){ //console.log(this.selectedMusicID);
            let addToPlaylist = {
                'act': 'add_to_playlist',
                'music_id': this.selectedMusicID,
                'playlist_arr': this.selectedPlaylist,
            }; //console.log(addToPlaylist);

            axios.post(window.location.origin+'/api/playlist', addToPlaylist)
                .then(({response}) => {
                    this.selectedPlaylist = [];
                    this.$emit('success');
                })
                .catch(({error}) => this.$alert(error.act, '', 'error'));
        },
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
</style>