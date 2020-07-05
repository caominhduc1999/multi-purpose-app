<template>
    <div class="container">
        <div class="row" v-if="$gate.isAdminOrAuthor()">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Responsive Hover Table</h3>

                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <button class="btn btn-success" @click="newModal">Add New <i class="fa fa-user-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Registed at</th>
                                <th>Modify</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr  v-for="user in users.data" :key="user.id">
                                <th>{{user.id}}</th>
                                <th>{{user.name}}</th>
                                <th>{{user.email}}</th>
                                <th>{{user.type | upText}}</th>
                                <th>{{user.created_at | myDate}}</th>
                                <td>
                                    <button class="btn btn-primary" v-on:click="editModal(user)"><i class="fa fa-edit"></i>Edit</button>
                                    <button class="btn btn-danger" v-on:click="deleteUser(user.id)"><i class="fa fa-trash"></i>Delete</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <pagination :data="users" @pagination-change-page="getResults"></pagination>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
        <div v-if="!$gate.isAdminOrAuthor()">
            <not-found></not-found>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{editmode ? 'Update User' : 'Add New User'}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editmode ? updateUser() : createUser()">
                        <div class="modal-body">
                            <div class="form-group">
                                <input v-model="form.name" type="text" name="name"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" placeholder="Name">
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <input v-model="form.password" type="text" name="password"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('password') }" placeholder="Password">
                                <has-error :form="form" field="password"></has-error>
                            </div>
                            <div class="form-group">
                                <input v-model="form.email" type="text" name="email"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" placeholder="Email">
                                <has-error :form="form" field="email"></has-error>
                            </div>
                            <div class="form-group">
                                <textarea v-model="form.bio" type="text" name="bio"
                                          class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }" placeholder="Bio">

                                </textarea>
                                <has-error :form="form" field="bio"></has-error>
                            </div>
                            <div class="form-group">
                                <select v-model="form.type" type="text" name="type"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                    <option value="" selected>Type</option>
                                    <option value="admin">Admin</option>
                                    <option value="standard">Standard</option>
                                    <option value="author">Author</option>
                                </select>
                                <has-error :form="form" field="type"></has-error>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                            <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Users",
        data(){
            return {
                editmode: false,
                users: {},
                form : new Form({
                    id: '',
                    name: '',
                    password: '',
                    email: '',
                    type: '',
                    bio: '',
                    photo: ''
                })
            }
        },
        methods:{
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            editModal(user){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(user);
            },
            loadUsers(){
                if (this.$gate.isAdminOrAuthor()){
                    axios.get('api/user').then(({data}) => (this.users = data));
                }
            },

            createUser(){
                this.$Progress.start()
                this.form.post('/api/user')
                .then(()=>{
                    $('#addNew').modal('hide');
                    Fire.$emit('AfterCreate')
                    this.$Progress.finish()
                    Toast.fire({
                        icon: 'success',
                        title: 'Signed in successfully'
                    })
                })
                .catch((error)=>{
                    //do something
                })
            },

            updateUser(){
                this.$Progress.start()
                this.form.put('api/user/'+this.form.id)
                .then(()=>{
                    $('#addNew').modal('hide');
                    Swal.fire(
                        'Updated!',
                        'Your file has been updated.',
                        'success'
                    )
                    Fire.$emit('AfterCreate')
                    this.$Progress.finish()
                })
                .catch(()=>{
                    this.$Progress.fail()
                })
            },

            deleteUser(id){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value){
                        this.form.delete('api/user/'+id)
                        .then(()=>{
                            Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            )
                            Fire.$emit('AfterCreate')
                        })
                        .catch((error)=>{

                        })
                    }

                })
            },

            getResults(page = 1) {
                axios.get('api/user?page=' + page)
                    .then(response => {
                        this.users = response.data;
                    });
            }
        },
        created() {
            Fire.$on('searching', () => {
                let query = this.$parent.search;
                axios.get('api/findUser?q=' + query)
                .then((data)=>{
                    this.users = data.data
                })
                .catch(()=>{

                })
            });
            this.loadUsers();
            Fire.$on('AfterCreate', () => {
                this.loadUsers()
            });

        }
    }
</script>

<style scoped>

</style>
