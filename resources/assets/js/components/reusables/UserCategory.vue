<template>
    <div class="container">
        <div class="modal fade" id="ManageUserCategoryModal" tabindex="-1" role="dialog" aria-labelledby="ManageUserCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form @submit.prevent="ManageUserCategory">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ManageUserCategoryModalLabel">Manage {{titleText}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <table class="table">
                                    <thead>
                                        <th>No</th>
                                        <th colspan="2">{{titleText}}</th>

                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in content" v-bind:key="item.id">
                                            <td>{{(index+1)}}</td>
                                            <td>{{item.type}}</td>
                                            <td class="text-right">
                                                <div>
                                                    <a class="modify-btn" title="Edit" v-on:click="editCategory(item.id, item.type)">
                                                        <i class="fa fa-edit color-blue fa-fw fa-lg"></i>
                                                    </a>
                                                    <a class="modify-btn" title="Delete" v-on:click="deleteCategory(item.id, item.type)">
                                                        <i class="fa fa-trash color-red fa-fw fa-lg"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger float-left" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" @click="createCategory">Create {{titleText}}</button>
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
        props:{
            category: String,
        },
        data(){
            return{
                content: null,
            }
        },
        computed:{
            titleText: function(){
                if(this.category == 'user_type'){
                    return 'User Type';
                }else if(this.category == 'hak_akses'){
                    return 'Dept/Division';
                }
                return '';
            }
        },
        methods:{
            loadContent(){ //console.log('triggered');
                axios.get(window.location.origin+'/api/'+this.category).then(({data}) => (this.content = data));
            },
            ManageUserCategory(){
                this.form.post(window.location.origin+'/api/wishlist').then(({ data }) => {
                    console.log(data);
                    $('#modifyModal').modal('hide');
                    this.$alert('Wishlist Created', '', 'success');
                    this.$emit('wishlistCreated');
                });
            },
            createCategory(){
                this.$prompt("Create New "+this.titleText).then((text) => { //console.log(text);
                //.then(res => {
                    let createCategory = {
                        'act' : 'create_'+this.category,
                        'type' : text,
                    }
                    axios.post(window.location.origin+'/api/user_type', createCategory).then(result => {
                        this.$alert(this.titleText+' Created', '', 'success');
                        this.loadContent();
                    });
                });
            },
            editCategory(id, type){
                this.$prompt("Edit "+this.titleText, type).then((text) => { //console.log(text);
                //.then(res => {
                    let editCategory = {
                        'act' : 'create_'+this.category,
                        'type' : text,
                    }
                    axios.post(window.location.origin+'/api/user_type', editCategory).then(result => {
                        this.$alert(this.titleText+' Created', '', 'success');
                        this.loadContent();
                    });
                });
            },
            deleteCategory(id, type){
                this.$confirm('Confirm Delete '+this.titleText+' '+type+'?', '', 'question')
                    .then( () => {
                        this.$confirm('This delete action cannot be undone!', '', 'warning')
                            .then( () => {
                                axios.delete(window.location.origin+'/api/'+this.category+'/'+id)
                                    .then(({response}) => {
                                        this.$alert('Delete Successful', '', 'success');
                                        this.loadContent();
                                    })
                                    .catch(({error}) => this.$alert(error.message, '', 'error'));
                                });
                    })
                    .catch(error => console.error(error));
            }
        },
        watch:{
            category: function(){ //console.log('changed');
                this.loadContent();
            }
        }
    }
</script>

<style lang="css">
    .playlist_container{
        max-height: 500px;
        overflow: auto;
    }
</style>