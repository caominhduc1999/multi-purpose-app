<template>
    <div>
        <h1>This is profile component</h1>
        <div class="row">
            <div class="col-md-12">
                <!-- Widget: user widget style 1 -->
                <div class="box box-widget widget-user">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header bg-aqua-active">
                        <h3 class="widget-user-username">Alexander Pierce</h3>
                        <h5 class="widget-user-desc">Founder &amp; CEO</h5>
                    </div>
                    <div class="widget-user-image">
                        <img class="img-circle" :src="getProfilePhoto()" alt="User Avatar">
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">3,200</h5>
                                    <span class="description-text">SALES</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">13,000</h5>
                                    <span class="description-text">FOLLOWERS</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4">
                                <div class="description-block">
                                    <h5 class="description-header">35</h5>
                                    <span class="description-text">PRODUCTS</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
                <!-- /.widget-user -->
            </div>
        </div>
        <div>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Quick Example</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form role="form">
                    <div class="box-body">
                        <div class="form-group">
                            <label >Name</label>
                            <input v-model="form.name" type="text" class="form-control"  placeholder="Name" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                        <div class="form-group">
                            <label >Email </label>
                            <input v-model="form.email" type="email" class="form-control"  placeholder="Enter email" :class="{ 'is-invalid': form.errors.has('email') }">
                            <has-error :form="form" field="email"></has-error>
                        </div>
                        <div class="form-group">
                            <label >Password</label>
                            <input type="password" v-model="form.password" class="form-control"  placeholder="Password" :class="{ 'is-invalid': form.errors.has('password') }">
                            <has-error :form="form" field="password"></has-error>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputFile">Photo</label>
                            <input type="file" @change="updateProfile" id="exampleInputFile">
                        </div>
                        <div class="form-group">
                            <label >Bio</label>
                            <textarea v-model="form.bio" type="text" class="form-control"  placeholder="Bio" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                            <has-error :form="form" field="bio"></has-error>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" @click.prevent="updateInfo" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Profile",
        data(){
            return{
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
            getProfilePhoto(){
                let photo = (this.form.photo.length > 200) ? this.form.photo : '/img/profile/'+this.form.photo;
                return photo;
            },

            updateProfile(e){
                let file = e.target.files[0];
                let reader = new FileReader();
                if (file['size'] < 200000){
                    reader.onloadend = (file) => {
                        this.form.photo = reader.result;
                    }
                    reader.readAsDataURL(file);
                } else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'File is too large',
                    })
                }

            },

            updateInfo(){
                this.$Progress.start()
                this.form.put('api/profile')
                .then(()=>{
                    this.init()
                    this.$Progress.finish()
                })
                .catch(()=>{
                    this.$Progress.fail()
                })
            },

            init(){
                axios.get('api/profile')
                    .then(({data}) => {
                        this.form.fill(data)
                    });
            }
        },

        created(){
            this.init()
        }
    }

</script>

<style scoped>

</style>
