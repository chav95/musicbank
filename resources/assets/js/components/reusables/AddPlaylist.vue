<template>
    <li>
        <a href="" @click="updatePlaylistParentID(parent_id)" :id="parent_id" data-toggle="modal" data-target="#CreatePlaylist">
            <span>+Add New Playlist+</span>
        </a>
    </li>
</template>

<script>
    import {mapMutations} from 'vuex'
    export default {
        name: 'addPlaylist',
        props: {
            parent_id: {
                type: Number,
                default: 0
            },
        },
        data(){
            return{
                
            }
        },
        methods: {
            updatePlaylistParentID(value){// console.log(value);
            this.$store.commit('SET_PLAYLIST_PARENT_ID', value);
      },
        },
        computed: {
            ...mapMutations([
                'SET_PLAYLIST_PARENT_ID'
            ])
        }
    }
</script>