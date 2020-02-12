<template>
    <div class="container">
      <div class="modal fade" id="CreatePlaylist" ref="modal" tabindex="-1" role="dialog" aria-labelledby="CreatePlaylistLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <form @submit.prevent="createPlaylist">
              <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="CreatePlaylistLabel">Create New Playlist</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>

              <div class="modal-body">
                <div class="form-group file_container">
                    <div class="form-group">
                        <input type="text" name="nama_playlist" class="form-control" placeholder="Playlist Name" autofocus="autofocus"
                            v-model="form.nama_playlist"
                            :class="{ 'is-invalid': form.errors.has('nama_playlist') }
                        ">
                        <has-error :form="form" field="nama_playlist"></has-error>
                    </div>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" @click="resetModal" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="button" @click="createPlaylist" class="btn btn-primary">Create</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
  import Form from 'vform';
  import {mapState} from 'vuex'

  export default{
    data(){
      return{
        form: new Form({
            nama_playlist: '',
            parent_id: 0,
        })
      }
    },
    methods:{
      resetModal(){
        this.form.nama_playlist = '';
      },
      createPlaylist(){ console.log(this.form.nama_playlist);
        this.form.post(window.location.origin+'/api/playlist')
          .then(res=>{ console.log(this.form.nama_playlist);
            this.resetModal();
            this.$alert('Playlist Created', '', 'success');
                            
            let postToLog = {
              'item_id': 0,
              'action': 'create playlist',
              'item_name': this.form.nama_playlist
            }
            axios.post(window.location.origin+'/api/log', postToLog)
            .then(res => location.reload())
            .catch(({error}) => {
              console.error(error);
              location.reload();
            });
          }).catch(err=>{
            this.$alert('Failed: '+err.message, '', 'error');
          });;
      },
    }, 
    watch: {
      '$route.params.playlist_id': function(playlist_id){
        this.form.parent_id = (this.$route.params.playlist_id ? this.$route.params.playlist_id : 0)
      },
    },
  }
</script>