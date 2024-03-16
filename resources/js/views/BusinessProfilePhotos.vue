<template>
    <div style="min-height: 400px;" class="app-background-color">
        <Header v-if="prevRoute" :withAppName="true" :route="prevRoute.path" ref="header"/>
        <div id="delete-photo-conf" class="menu menu-box-modal app-background-color rounded-m" data-menu-height="335" data-menu-effect="menu-over" style="display: block; min-height: 25%;width: 90%;padding: 22px;">
            <div class="menu-title">
                <a href="#" class="close-menu" @click="$hide_modal.all" style="margin-top: 0;"><i class="fa fa-times app-color"></i></a>
            </div>
            <h3 class="mt-5" style="text-align: center;">Are you sure ?</h3>
            <div class="content mb-0">
                <h5 class="app-color" style="text-align: center;">
                    Are you sure you want to delete this photo? this action is irreversible.
                </h5>
                <button @click="delete_photo"
                class="font-900" style="margin-top: 20px;background-color: #DF6161!important;color: #fff;border-radius: 10px;width: 100%;height: 50px;">
                    Delete photo
                </button>
            </div>
        </div>
        <div v-show="!isLoading" class="page-content app-background-color" style="">
            <div style="padding-top: 30px;padding-bottom: 20px;">
                <h3 style="text-align: center;" class="app-color">
                    {{$t('message.add_business_profile.business_photos')}}
                </h3>
            </div>
            <div v-if="business_profile && business_profile.business_profile_photos && business_profile.business_profile_photos.length>0" style="width: 100%;" class="app-color font-600">
                <div v-for="photo in business_profile.business_profile_photos" style="position: relative;width: 95%;margin: 22px auto;">
                    <span class="" style="position: absolute;right: 0;padding: 10px;">
                        <span @click="delete_photo_confirmation(photo)" class="app-background-color" style="padding: 10px; border-radius: 5px;">
                            <i class="fa fa-trash app-color font-16" style=""></i>
                        </span>
                    </span>
                    <img :src="photo.url" style="width: 100%;height: 400px;object-fit: cover;border-radius: 2%;">
                </div>
            </div>
            <div v-else style="width: 100%;text-align: center;">
                <button @click="save"
                    class="font-900 app-background-color" style="margin-top: 20px auto;background-color: #090C49!important;color: #fff;border-radius: 10px;width: 50%;height: 50px;">
                    No business profile photos
                </button>
            </div>
        </div>
        <input v-show="false" type="file" id="file" @change="onFileChange" name="" accept="image/jpeg, image/gif, image/png">
        <button @click="choose_file" class="font-900 app-background-color" style="background-color: #090C49!important;color: #fff;border-radius: 50%;width: 80px;height: 80px;margin: auto;position: fixed;bottom: 30px;right: 30px;">
            <span class="fa-stack">
                <i class="fa fa-circle-thin fa-stack-2x" style="opacity: 1; color: #fff;"></i>
                <i class="fa fa-plus fa-stack-1x" style="opacity: 1; color: #fff;"></i>
            </span>
        </button>
    </div>
</template>

<style scoped>
    @import url("https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
</style>

<script>
    //import UseDropzoneDemo from '../components/UseDropzoneDemo.vue'
    //import { Dropzone } from "dropzone";
    //import VueLoadingButton from "vue-loading-button";
    import axios from 'axios';
    import { mapState } from 'vuex';
    import { v4 as uuidv4 } from 'uuid';

    import Header from '../components/Header.vue'
    import SwitchButton from '../components/SwitchButton.vue'
    export default{
        components: {
            Header, SwitchButton,
        },
        props: {
            product: {
                type: Object,
                defaut: null,
            }
        },
        data: function () {
        return {
          prevRoute: null,
          file: null,
          photo_to_delete: null,
        }
      },
      computed: {
        ...mapState(['isLoggedIn', 'maintenance_mode', 'connecting', 'disconnecting', 'countries_db', 'agreements', 'delivery_penalties_limit', 'isPiBrowser']),
        isLoading:{
            get(){
                return this.$store.state.isLoading
            },
            set(val){
                this.$store.state.isLoading = val
            }
        },
        user:{
            get(){
                return this.$store.state.user
            },
            set(val){
                this.$store.state.user = val
            }
        },
        saving:{
            get(){
                return this.$store.state.saving
            },
            set(val){
                this.$store.state.saving = val
            }
        },
        business_profile:{
            get(){
                return this.$store.state.business_profile
            },
            set(val){
                this.$store.state.business_profile = val
            }
        },
      },
      beforeRouteEnter(to, from, next) {
          next(vm => {
            vm.prevRoute = from
          })
        },
        mounted() {
            
            /*$('.link').on('click', function() {
                if (!$(this).hasClass('active-nav')) {
                    $('.link').removeClass('active-nav')
                    if (!this.isUpdate) {$(this).addClass('active-nav')}
                }
            })*/

            ///////////////////////////////////////////////////////////
            
            //MENU
            console.log('End.')

            ///////////////////////////////////////////////////////////
            let self = this
            $( document ).ready(function() {
                $('.link').removeClass('active-nav')
                if (!self.isUpdate) {
                    $('.link.publish').addClass('active-nav')
                }
            });
            this.$store.dispatch('scrollToTop')
        },
        watch: {
            
        },
        methods: {
            choose_file(){
                if (this.business_profile && this.business_profile.business_profile_photos && this.business_profile.business_profile_photos.length>=3) {
                    let msg = "Limit of 3 max photos reached"
                    this.$show_modal.show_modal({id: 'info', title: "Info", message: msg, btn_text: 'OK'})
                    return
                }
                $('#file').trigger('click')
            },
            onFileChange(e) {
                this.file = e.target.files[0];
                //$( "#img_file" ).attr("src", URL.createObjectURL(this.file));
                //console.log(this.form);
                this.save_file()
            },
            save_file(){
                this.saving = true;
                let formData = new FormData();
                formData.append('photo', this.file);
                axios.post('/api/v1/save-business-profile-photo', formData, {
                    headers: {
                        "Content-type": "multipart/form-data",
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(res => {
                    console.log(res.data)
                    this.saving = false;
                    if (res.data.status === true) {
                        this.file = null
                        this.business_profile = res.data.business_profile
                        this.$show_modal.show_modal({id: 'success', title: "Success", message: "Saved successfully", btn_text: 'OK'})
                    } else {
                        this.$show_modal.show_modal({id: 'error', title: "Error", message: this.$t('message.an_error_occured'), btn_text: 'OK'})
                    }
                })
                .catch(error => {
                    console.log(error)
                    this.saving = false
                    if (error.response.status !== 401) {
                        this.$show_modal.show_modal({id: 'error', title: "Error", message: this.$t('message.an_error_occured'), btn_text: 'OK'})
                    }
                })
            },
            delete_photo_confirmation(photo){
                this.photo_to_delete = photo
                this.$show_modal.show_modal({id: 'delete-photo-conf'})
            },
            delete_photo(){
                this.$hide_modal.hide_modal('delete-photo-conf')
                this.saving = true;
                axios.post('/api/v1/delete-business-profile-photo', {id: this.photo_to_delete.id})
                .then(res => {
                    console.log(res.data)
                    this.saving = false;
                    this.photo_to_delete = null
                    if (res.data.status === true) {
                        this.file = null
                        this.business_profile = res.data.business_profile
                        this.$show_modal.show_modal({id: 'success', title: "Success", message: "Deleted successfully", btn_text: 'OK'})
                    } else {
                        this.$show_modal.show_modal({id: 'error', title: "Error", message: this.$t('message.an_error_occured'), btn_text: 'OK'})
                    }
                })
                .catch(error => {
                    console.log(error)
                    this.saving = false
                    if (error.response.status !== 401) {
                        this.$show_modal.show_modal({id: 'error', title: "Error", message: this.$t('message.an_error_occured'), btn_text: 'OK'})
                    }
                })
            },
        }
    }
</script>

<style scoped>
    #dropzone{
        border-radius: 10px;
        border-width: 1px;
        display: block;
        width: 900px;
        height: 50px;
    }


    .error-message {
      display: none;
      color: red;
    }
    .border-error{
        border: 1px solid red!important;
    }

    /* ✨ The magic ✨ */
    form.errors {
      :invalid {
        border-color: red;
      }
      .error-message {
        display: block;
      }
    }

    .ios-switch label::after {
      font-family: 'Font Awesome 5 Free';
      content: "\f111";
      font-weight: 900; /* <-- add this */
      color: #fff;
      display: block !important;
      margin-top: -25px;
      z-index: 6;
      width: 25px !important;
      height: 25px !important;
      padding-left: 4px;
      transition: all 250ms ease;
      border-radius: 50px !important;
      background-color: v-bind(switched?'#090C49':'#828282');
      border-color: v-bind(switched?'#090C49':'#828282');
      /*border: solid 1px #090C49;*/
      transition: all 250ms ease;
    }

    .ios-switch label::before {
      content: "";
      display: block !important;
      background-color: #F1F7FF;
      pointer-events: all;
      margin-top: -5px;
      margin-left: -1px;
      width: 58px !important;
      height: 25px !important;
      /*border: solid 1px #090C49;*/
      border-color: v-bind(switched?'#090C49':'#828282');
      border-radius: 50px !important;
      transition: all 250ms ease;
    }

    .ios-input:checked ~ .custom-control-label::before {
      background-color: #F1F7FF !important;
    }
</style>
