<template>
  <div class="container">
    <section class="content-header">
      <h1>
        Users <br>
        <small>Manage Users</small>
      </h1>
      <button class="btn btn-primary" id="createUserBtn" @click="creeateUser">Create New User</button>
    </section>

    <section class="content container-fluid">
      <div class="row justify-content-center mt-4 mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <pagination :data="allUser" @pagination-change-page="getResults"></pagination>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0" style="height: 100%;">
              <table class="table table-head-fixed">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>User Type</th>
                    <th>Access Type</th>
                    <th>Registered At</th>
                    <th>Modify</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="allUser.data.length > 0">
                    <tr  v-for="user in allUser.data" :key="user.id" hover:bg-blue px-4 py2>
                      <td>{{user.name}}</td>
                      <td>{{user.email}}</td>
                      <td>{{user.privilege}}</td>
                      <td>{{user.hak_akses}}</td>
                      <td>{{formatDatetime(user.created_at)}}</td>
                      <td>
                        <div class="modify-btn-container">
                          <a class="modify-btn" title="Edit" v-on:click="editUser(user)">
                            <i class="fa fa-edit color-blue fa-fw fa-lg"></i>
                          </a>
                          <a class="modify-btn" title="Delete" v-on:click="deleteUser(user.id, user.name)">
                            <i class="fa fa-trash color-red fa-fw fa-lg"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr><td colspan="100%"><h3 class="text-center">User Table Is Empty</h3></td></tr>
                  </template>
                </tbody>
              </table>
            </div>
            <div class="card-footer">
              <pagination :data="allUser" @pagination-change-page="getResults"></pagination>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>

        <div class="modal fade" id="modifyModal" tabindex="-1" role="dialog" aria-labelledby="modifyModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <form @submit.prevent="submitUser">
                <div class="modal-header">
                  <h5 class="modal-title" id="modifyModalLabel">{{modalTitle}}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>

                <div class="modal-body">
                  <div class="form-group">
                    <label>User Name</label>
                    <input v-model="form.name" type="text" name="name" placeholder="User Name"
                      class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                    <has-error :form="form" field="name"></has-error>
                  </div>
                  <div class="form-group">
                    <label>User Email</label>
                    <input v-model="form.email" type="text" name="email" placeholder="User Email"
                      class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                    <has-error :form="form" field="email"></has-error>
                  </div>
                  <div class="form-group">
                    <label>User Type</label>
                    <input v-model="form.privilege" type="text" name="privilege" placeholder="User Type"
                      class="form-control" :class="{ 'is-invalid': form.errors.has('privilege') }">
                    <has-error :form="form" field="privilege"></has-error>
                  </div>
                  <div class="form-group">
                    <label>Hak Akses</label>
                    <input v-model="form.hak_akses" type="text" name="hak_akses" placeholder="Hak Akses"
                      class="form-control" :class="{ 'is-invalid': form.errors.has('hak_akses') }">
                    <has-error :form="form" field="hak_akses"></has-error>
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Submit Edit</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
  import Form from 'vform';
  import moment from 'moment';

  export default {
    data(){
      return{
        modalTitle: String,
        form: new Form({
          act: String,
          selectedUserId: Number,
          name: String,
          email: String,
          privilege: String,
          hak_akses: String,
        }),
        allUser: {data: {}},
      }
    },
    methods: {
      loadAllUser(){
        axios.get(window.location.origin+'/api/user').then(({data}) => (this.allUser = data));
      },
      formatDatetime(datetime){
        return moment(String(datetime)).format('llll');
      },
      getResults(page = 1) {
        axios.get(window.location.origin+'/api/log?page=' + page)
          .then(response => {
            this.allUser = response.data;
          });
      },
      creeateUser(){
        this.modalTitle = 'Create New User';
        this.form.selectedUserId = 0;
        this.form.act = 'new_user';
        this.form.name = '';
        this.form.email = '';
        this.form.privilege = '';
        this.form.hak_akses = '';
        $('#modifyModal').modal('show');
      },
      editUser(selectedUser){
        this.modalTitle = 'Edit User';
        this.form.selectedUserId = selectedUser.id;
        this.form.act = 'edit_user';
        this.form.name = selectedUser.name;
        this.form.email = selectedUser.email;
        this.form.privilege = selectedUser.privilege;
        this.form.hak_akses = selectedUser.hak_akses;
        $('#modifyModal').modal('show');
      },
      submitUser(){
        this.form.post(window.location.origin+'/api/user').then(({ data }) => {
          console.log(data);
          $('#modifyModal').modal('hide');
          if(this.form.selectedUserId == 0){
            this.$alert('User'+this.form.name+' Created, Default Password Is "123456"', '', 'success');
          }else{
            this.$alert('Edit Successful', '', 'success');
          }
          this.loadAllUser();
        });

        /*let userInfo = {
          'act': (this.form.selectedUserId == 0 ? 'new_user' : 'edit_user'),
          'id': this.form.selectedUserId,
          'name': this.form.name,
          'email': this.form.email,
          'privilege': this.form.privilege,
          'hak_akses': this.form.hak_akses,
        }; console.log(userInfo);*/

        /*axios.post(window.location.origin+'/api/user', form)
          .then(({response}) => {
            $('#modifyModal').modal('hide');
            if(this.form.selectedUserId == 0){
              this.$alert(this.form.name+' User Created, Default Password Is "123456"', '', 'success');
            }else{
              this.$alert('Edit Successful', '', 'success');
            }
            this.loadAllUser();
          });
          //.catch(({error}) => this.$alert(error.message, '', 'error'));*/
      },
      deleteUser(user_id, user_name){
        this.$confirm('Permanently delete user '+user_name+'?', '', 'question')
          .then( () => {
              this.$confirm('This delete action cannot be undone!', '', 'warning')
                .then( () => {
                  axios.delete(window.location.origin+'/api/user/'+user_id)
                    .then(({response}) => {
                      this.$alert('Delete Successful', '', 'success');
                      this.loadAllUser();
                    })
                    .catch(({error}) => this.$alert(error.message, '', 'error'));
                });
          })
          .catch(error => console.error(error));
      }
    },
    mounted(){
        this.loadAllUser();
        //console.log('Component mounted.')
    }
  }
</script>

<style scoped>
  #createUserBtn{
    margin-top: 4px;
  }
</style>

<style scoped>
  .help-block.invalid-feedback{
    color: red;
  }
</style>