<template>
    <div class="container">
        <div class="modal fade" id="AddWishlistModal" ref="modal" tabindex="-1" role="dialog" aria-labelledby="AddWishlistModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form @submit.prevent="addToPlaylist" enctype='multipart/form-data'>
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold" id="AddWishlistModalLabel">Add {{selectedMusicJudul}} To Wishlist</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                        </div>

                        <div class="modal-body" :key="formReset">
                            <div class="form-group playlist_container">
                                <h4>Choose Wishlist(s)</h4>
                                <ul style>
                                    <li v-for="item in wishlist" :key="item.id" style="list-style-type: none">
                                        <input type="checkbox" class="playlist"
                                            v-model="selectedWishlist"
                                            :value="item.id" 
                                            :id="'wishlistItem'+item.id"
                                            :ref="'wishlistItem'+item.id"
                                        />
                                        <label :for ="'wishlistItem'+item.id" :ref="'label_'+item.id">
                                            {{item.wishlist_label | capitalize}} ({{item.detail_count}} content)
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary create-wishlist" data-toggle="modal" data-target="#CreateWishlistModal">
                                Create New Playlist
                            </button>
                            <button type="button" @click="resetModal" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                            <button type="button" @click="addToPlaylist" class="btn btn-primary">OK</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <add-wishlist v-on:wishlistCreated="loadWishlist"></add-wishlist>
    </div>
</template>

<script>
    import AddWishlist from './reusables/AddWishlist.vue';

    export default{
        components: {
            AddWishlist,
        },
        props: {
            selectedMusicID: Number,
            selectedMusicJudul: String,
        },
        data(){
            return{
                formReset: 0,
                wishlist: [],
                selectedWishlist: [],
            }
        },
        methods:{
            loadWishlist(){
                $('#CreateWishlistModal').modal('hide');
                axios.get(window.location.origin+'/api/wishlist/wishlistSelection').then(({data}) => (this.wishlist = data));
            },
            resetModal(){
                this.formReset = 1;
            },
            addToPlaylist(){ //console.log(this.selectedMusicID);
                let addToWishlist = {
                    'act': 'add_to_wishlist',
                    'music_id': this.selectedMusicID,
                    'selected_wishlist': this.selectedWishlist,
                };

                axios.post(window.location.origin+'/api/wishlist', addToWishlist)
                    .then(({response}) => {
                        this.selectedPlaylist = [];
                        this.$emit('success');
                    })
                    .catch(({error}) => this.$alert(error.message, '', 'error'));
            },
        },
        mounted(){
            this.loadWishlist();
        },
    }
</script>

<style scoped>
    .playlist_container{
        max-height: 500px;
        overflow: auto;
    }
    .create-wishlist{
        float: left;
    }
    h4{
        display: inline;
    }
</style>