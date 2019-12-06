<template>
    <div class="container">
        <div class="modal fade" id="CreateWishlistModal" tabindex="-1" role="dialog" aria-labelledby="CreateWishlistModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form @submit.prevent="createWishlist">
                        <div class="modal-header">
                            <h5 class="modal-title" id="CreateWishlistModalLabel">Create Wishlist</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <label>Wishlist Label</label>
                                <input type="text" name="wishlist_label" placeholder="Wishlist Label" class="form-control" 
                                    v-model="form.wishlist_label" :class="{'is-invalid': form.errors.has('wishlist_label')}
                                ">
                                <has-error :form="form" field="name"></has-error>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create Wishlist</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Form from 'vform';

    export default{
        data(){
            return{
                formReset: 0,
                form: new Form({
                    act: 'create_wishlist',
                    wishlist_label: '',
                }),
            }
        },
        methods:{
            resetModal(){
                this.formReset = 1;
            },
            createWishlist(){
                this.form.post(window.location.origin+'/api/wishlist').then(({ data }) => {
                    console.log(data);
                    $('#modifyModal').modal('hide');
                    this.$alert('Wishlist Created', '', 'success');
                    this.$emit('wishlistCreated');
                });
            },
        },
    }
</script>

<style lang="css">
    .playlist_container{
        max-height: 500px;
        overflow: auto;
    }
</style>