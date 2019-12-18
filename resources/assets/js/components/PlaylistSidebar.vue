<template>
    <ul class="treeview-menu">
        <li v-for="item in playlist" :key="item.id" class="treeview">
            <router-link
                :to="{
                    name: 'playlist',
                    params: {
                        'playlist_id': item.id,
                        'playlist_name': item.nama_playlist.replace(' ', '-'),
                        'playlist_title': item.nama_playlist
                    }
                }"
            >
                <i title="Delete Playlist"
                    class="far fa-file-audio nav-icon color-blue" 
                ></i>
                <span class="text-capitalize">{{item.nama_playlist}}</span>
                <span class="pull-right-container" v-if="item.children.length > 0">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </router-link>

            <playlist-sidebar 
                v-if="item.children.length > 0" 
                :playlist="item.children" 
                :addParentID="item.id" 
                :level="(selfLevel+1)"
            ></playlist-sidebar>
            <!--<ul v-else class="treeview-menu">
                <add-playlist :parent_id="item.id"></add-playlist>
            </ul>-->
        </li>
        <!--<add-playlist :parent_id="addParentID"></add-playlist>-->
    </ul>
</template>

<script>
    import createPlaylist from './CreatePlaylist.vue'

    export default {
        components: {
            createPlaylist
        },

        props: {
            playlist: Array,
            addParentID: {
                type: Number,
                default: 0,
            },
            level: {
                type: Number,
                default: 0,
            }
        },
        data(){
            return{
                selfLevel: 0,
                fullPlaylistt: null,
            }
        },
        methods: {
            deletePlaylist(id, nama_playlist){
                this.$confirm('Confirm Delete Playlist '+nama_playlist+' and all the content?', '', 'error')
                    .then(res => {
                        axios.delete(window.location.origin+'/api/playlist/'+id).then(result => {
                            this.$alert('Delete Success', '', 'success');
                            location.reload();
                        });
                    }
                );
            }
        },
        mounted() { //console.log(this.playlist);
            this.selfLevel += this.level;
            
            if(this.selfLevel == 0){
                axios.get(window.location.origin+'/api/music').then(({data}) => (this.fullPlaylistt = data));
            }
        }
    }
</script>