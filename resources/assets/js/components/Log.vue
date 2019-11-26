<template>
  <div class="container">
    <section class="content-header">
      <h1>
        Log <br>
        <small>List Of Downloads</small>
      </h1>
    </section>

    <section class="content container-fluid">
      <div class="row justify-content-center mt-4 mb-4">
        <div class="col-12">
          <div class="card">
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0" style="height: 100%;">
              <table class="table table-head-fixed">
                <thead>
                  <tr>
                    <th>Downloaded Music</th>
                    <th>Downloaded By</th>
                    <th>Downloaded At</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="logEntry.data.length > 0">
                    <tr  v-for="(log) in logEntry.data" :key="log.id" hover:bg-blue px-4 py2>
                      <td>{{log.judul}}</td>
                      <td>{{log.name}}</td>
                      <td>{{formatDatetime(log.created_at)}}</td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr><td colspan="100%"><h3 class="text-center">Log Download Is Empty</h3></td></tr>
                  </template>
                </tbody>
              </table>
            </div>
            <div class="card-footer">
              <pagination :data="logEntry" @pagination-change-page="getResults"></pagination>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>        
      </div>
    </section>
  </div>
</template>

<script>
    import moment from 'moment';
    export default {
        data(){
            return{
                logEntry: {data: {}},
            }
        },
        methods: {
            loadLogEntry(){
                axios.get(window.location.origin+'/api/log').then(({data}) => (this.logEntry = data));
            },
            formatDatetime(datetime){
                return moment(String(datetime)).format('llll');
            },
            getResults(page = 1) {
                axios.get(window.location.origin+'/api/log?page=' + page)
                    .then(response => {
                        this.logEntry = response.data;
                    });
            }
        },
        mounted(){
            this.loadLogEntry();
            //console.log('Component mounted.')
        }
    }
</script>
