<template>
    <div>
        <Header v-if="prevRoute" :withAppName="true" :route="prevRoute.path" ref="header"/>
        <div id="delete-item-conf" class="menu menu-box-modal app-background-color rounded-m" data-menu-height="335" data-menu-effect="menu-over" style="display: block; min-height: 25%;width: 90%;padding: 22px;">
            <div class="menu-title">
                <a href="#" class="close-menu" @click="$hide_modal.all" style="margin-top: 0;"><i class="fa fa-times app-color"></i></a>
            </div>
            <h3 class="mt-5" style="text-align: center;">Are you sure you want to delete the item ?</h3>
            <div class="content mb-0">
                <h5 class="app-color">ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</h5>
                <button @click="delete_item"
                class="font-900" style="margin-top: 20px;background-color: #DF6161!important;color: #fff;border-radius: 10px;width: 100%;height: 50px;">
                    Delete item
                </button>
            </div>
        </div>
        <div id="add-item" class="menu menu-box-modal app-background-color rounded-m" data-menu-height="335" data-menu-effect="menu-over" style="display: block; height: 95%;width: 90%;">
            <div class="menu-title">
                <a href="#" class="close-menu" @click="$hide_modal.all" style="margin-top: 0;"><i class="fa fa-times app-color"></i></a>
            </div>
            <h3 class="mt-5" style="text-align: center;">Add new item</h3>
            <div class="content mb-0">
                <label for="libelle" class="app-color">Item name</label>
                <div class="input-style has-borders validate-field mb-4">
                    <input type="text" class="form-control validate-name app-color app-border" id="libelle" placeholder="" v-model="item.name" maxlength="40" style="background-color: #fff!important;">
                    
                    <i class="fa fa-times disabled invalid color-red-dark"></i>
                    <i class="fa fa-check disabled valid color-green-dark"></i>
                    <div id="required-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                    <div id="length-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                </div>
                <input v-show="false" id="file" type="file" v-on:change="onFileChange" name="" accept="image/jpeg, image/gif, image/png">
                <button v-show="file===null" @click="choose_file" class="font-900 app-background-color" style="width: 35%;display: inline-block;background-color: #FAD09E!important;color: #fff;border-radius: 10%;width: 100px;height: 100px;margin-right: 15px;">
                    <span class="fa-stack" style="width: 4em;height: 4em;line-height: 4em;">
                        <i class="fa fa-circle-thin fa-stack-2x" style="opacity: 1; color: #090C49;font-size: 4em;"></i>
                        <i class="fa fa-plus fa-stack-1x" style="opacity: 1; color: #090C49;font-size: 30px;"></i>
                    </span>
                </button>
                <div v-show="file!==null" style="width: 100px;height: 100px;margin-right: 15px;position: relative;display: inline-block;">
                    <span class="" style="position: absolute;right: 0;padding:5px;">
                        <span class="app-background-color" style="padding: 5px; border-radius: 5px;">
                            <i @click="choose_file" class="fa fa-edit app-color" style=""></i>
                        </span>
                    </span>
                    <img id="img_file" style="width: 100px;height: 100px;object-fit: cover;display: inline-block;border-radius: 10%;" src="">
                </div>
                
                <div style="width: 64%;display: inline-block;">
                    <div style="width: 89%;display: inline-block;">
                        <label for="price" class="app-color">Price</label>
                        <div class="input-style has-borders validate-field mb-4">
                            <input type="text" inputmode="decimal" class="form-control validate-name app-color app-border" id="price" placeholder="" v-model="item.price" maxlength="40" style="background-color: #fff!important;">
                            
                            <i class="fa fa-times disabled invalid color-red-dark"></i>
                            <i class="fa fa-check disabled valid color-green-dark"></i>
                            <div id="required-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                            <div id="length-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                        </div>
                    </div>
                    <div style="width: 10%;display: inline-block;">
                        <img src="/site_images/pi_v.png">
                    </div>
                </div>
                <div style="width: 69%;display: block;">
                    <label for="libelle" class="app-color">How long to prepare this meal ?</label>
                    <div class="input-style has-borders validate-field mb-4">
                        <input type="text" class="form-control validate-name app-color app-border" id="libelle" placeholder="" v-model="item.time" maxlength="40" style="background-color: #fff!important;">
                        
                        <i class="fa fa-times disabled invalid color-red-dark"></i>
                        <i class="fa fa-check disabled valid color-green-dark"></i>
                        <div id="required-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                        <div id="length-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                    </div>
                </div>
                <label for="libelle" class="app-color" style="display: block;">Description</label>
                <div class="input-style has-borders validate-field mb-4">
                    <textarea type="text" class="form-control validate-name app-color app-border" id="description" placeholder="" v-model="item.description" maxlength="40" style="">
                    </textarea>
                    
                    <i class="fa fa-times disabled invalid color-red-dark"></i>
                    <i class="fa fa-check disabled valid color-green-dark"></i>
                    <div id="required-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                    <div id="length-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                </div>
                <button @click="save_item"
                class="font-900 app-background-color" style="margin-top: 20px;background-color: #090C49!important;color: #fff;border-radius: 10px;width: 100%;height: 50px;">
                    {{$t('message.add_business_profile.confirm')}}
                </button>
            </div>
        </div>
        <div class="page-content app-background-color" style="padding-top: 30px;">
            <h3 style="text-align: center;margin-bottom: 20px;" class="app-color">{{$t('message.add_business_profile.menu')}}</h3>
            <div style="width: 100%;height: 2px;border: 2px solid #090C49;" class="app-color font-600"></div>

            <div class="card card-style app-background-color" style="box-shadow: none;">
                <div class="mb-0" style="margin: 20px 0px;">
                    <SwitchButton id="menu_off" label="Menu off" v-model:checked="business_profile.menu_status" />
                    <SwitchButton v-if="business_profile.menu_status===true" id="orders_off" v-model:checked="business_profile.orders_status" label="Accept orders off" />
                    <SwitchButton v-if="business_profile.menu_status===true" id="payments_off" v-model:checked="business_profile.payments_status" label="Accept payments off" />
                    <div v-if="!isLoading && items.length>0" style="padding-top: 30px;">
                        <div v-for="item in items" style="margin-bottom: 10px;">
                            <div style="width: 25%; display: inline-block;">
                                <img :src="item.images[0]" style="width: 80px;height: 80px;border-radius: 10px;object-fit: cover;">
                            </div>
                            <div style="width: 70%; display: inline-block;vertical-align: top;">
                                <div style="width: 62%; display: inline-block;">
                                    <strong class="app-color">{{item.name}}</strong>
                                </div>
                                <div style="width: 37%; display: inline-block;text-align: right;">
                                    <strong class="app-color">{{item.price}}</strong>
                                    <img src="/site_images/pi_v.png" style="height: 18px;">
                                </div>
                                <div style="width: 100%; display: inline-block;">
                                    <label class="app-color">{{item.description}}</label>
                                </div>
                            </div>
                            <div style="width: 4%; display: inline-block;vertical-align: top;text-align: right;">
                                <i @click="delete_item_confirmation(item)" class="fa fa-trash app-color font-16"></i>
                            </div>
                        </div>
                    </div>
                    <div v-else-if="isLoading" style="text-align: center;">
                        <img src="/site_images/Eclipse-1s-200px.gif" style="height: 100px;">
                    </div>
                    <div style="width: 100%;text-align: center;padding-top: 80px;">
                        <button v-if="items.length==0" @click="open_add_item"
                        class="font-900 app-background-color" style="background-color: #090C49!important;color: #fff;border-radius: 50%;width: 80px;height: 80px;margin: auto;">
                            <span class="fa-stack">
                                <i class="fa fa-circle-thin fa-stack-2x" style="opacity: 1; color: #fff;"></i>
                                <i class="fa fa-plus fa-stack-1x" style="opacity: 1; color: #fff;"></i>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <button v-if="items.length>0" @click="open_add_item" class="font-900 app-background-color" style="background-color: #090C49!important;color: #fff;border-radius: 50%;width: 80px;height: 80px;margin: auto;position: fixed;bottom: 30px;right: 30px;">
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
              item: {
                name: "",
                price: 0.0,
                description: "",
                image: "",
                time: "",
              },
              file: null,
              items: [],
              item_to_delete: null,
            }
        },
      computed: {
        ...mapState(['isLoggedIn', 'maintenance_mode', 'connecting', 'disconnecting', 'countries_db', 'agreements', 'isPiBrowser']),
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
            this.$store.dispatch('scrollToTop')
            this.getItems()
        },
        watch: {
            'item.price': function (newVal, oldVal){
                if (newVal==undefined || newVal==null) {return}

                 // Remove any non-numeric characters except the dot
                this.item.price = this.item.price.replace(/[^\d.]/g, '');

                // Remove additional dots if more than one
                const dots = this.item.price.match(/\./g);
                if (dots && dots.length > 1) {
                    this.item.price = this.item.price.replace(/\.$/, '');
                }
                //Limit number of digits after dot to 7
                let priceTab = newVal.toString().split('.')
                if (priceTab[1] && priceTab[1].length>7) {
                    this.item.price = oldVal
                }
            },
            'business_profile.menu_status': function (newVal, oldVal){
                this.save_business_profile()
            },
            'business_profile.orders_status': function (newVal, oldVal){
                this.save_business_profile()
            },
            'business_profile.payments_status': function (newVal, oldVal){
                this.save_business_profile()
            },
        },
        methods: {
            open_add_item(){
                this.$show_modal.show_modal({id: 'add-item'})
            },
            choose_file(){
                $('#file').trigger('click')
            },
            onFileChange(e) {
                //console.log(e.target.files[0]);
                //this.filename = "Selected File: " + e.target.files[0].name;
                this.file = e.target.files[0];
                $( "#img_file" ).attr("src", URL.createObjectURL(this.file));
                //console.log(this.form);
            },
            getItems(){
                this.isLoading = true;
                axios.get('/api/v1/items')
                .then(res => {
                    console.log('getItems', res.data)
                    this.isLoading = false;
                    if (res.data.status === true) {
                        this.items = res.data.items
                    } else {
                        this.$show_modal.show_modal({id: 'error', title: "Error", message: this.$t('message.an_error_occured'), btn_text: 'OK'})
                    }
                })
                .catch(error => {
                    this.isLoading = false
                    console.log(error)
                    console.log(error.response.status)
                    if (error.response.status !== 401) {
                        this.$show_modal.show_modal({id: 'error', title: "Error", message: this.$t('message.an_error_occured'), btn_text: 'OK'})
                    }
                })
            },
            save_item(){
                this.$hide_modal.hide_modal('add-item')
                this.saving = true;
                let formData = new FormData();
                formData.append('image', this.file);
                formData.append('name', this.item.name);
                formData.append('business_profiles_id', this.business_profile.id);
                formData.append('price', this.item.price);
                formData.append('time', this.item.time);
                formData.append('description', this.item.description);
                axios.post('/api/v1/items', formData, {
                    headers: {
                        "Content-type": "multipart/form-data",
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(res => {
                    console.log(res.data)
                    this.saving = false;
                    if (res.data.status === true) {
                        this.item = {
                            name: "",
                            price: "0.0",
                            description: "",
                            image: "",
                            time: "",
                        }
                        this.file = null
                        this.items = res.data.items
                        this.$show_modal.show_modal({id: 'success', title: "Success", message: "Added successfully", btn_text: 'OK'})
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
            delete_item_confirmation(item){
                this.item_to_delete = item
                this.$show_modal.show_modal({id: 'delete-item-conf'})
            },
            delete_item(){
                this.saving = true
                this.$hide_modal.hide_modal('delete-item-conf')
                axios.delete(`/api/v1/items/${this.item_to_delete.id}`)
                .then(res => {
                    console.log('getItems', res.data)
                    this.saving = false;
                    if (res.data.status === true) {
                        this.items = res.data.items
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
            save_business_profile(){
                this.saving = true;
                axios.put(`/api/v1/business-profiles/${this.business_profile.id}`, this.business_profile)
                .then(res => {
                    console.log(res.data)
                    this.saving = false;
                    if (res.data.status === true) {
                        let msg = this.$t('message.saved')
                        this.business_profile = res.data.business_profile
                        //this.$functions.msg_box(this, 'success', this.$t('message.cart.success'), msg)
                        this.$show_modal.show_modal({id: 'success', title: "Success", message: "Saved successfully", btn_text: 'OK'})
                    } else {
                        //this.$functions.msg_box(this, 'error', '', this.$t('message.an_error_occured'))
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
