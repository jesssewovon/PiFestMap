<template>
    <div>
        <Header v-if="prevRoute" :withAppName="true" :route="prevRoute.path" ref="header" :show_cart="true"/>
        <QuestionOption @continue="continue_option"/>
        <div v-if="!isLoading && business_profile!==null" class="page-content app-background-color" style="padding-top: 30px;">
            <div class="card app-background-color" style="box-shadow: none;margin: 22px;">
                <label style="" class="app-color font-600 font-18">{{business_profile.name}}</label>
                <div style="padding-top: 11px;">
                    <img style="width: 20px;" :src="'/images/'+business_profile.business_type.img">&nbsp;&nbsp;
                    <label style="" class="app-color font-400 font-16">{{business_profile.business_type.libelle}}</label>
                </div>
                <div style="padding-top: 11px;">
                    <img style="width: 20px;" src="/site_images/marker.png">&nbsp;&nbsp;
                    <label style="" class="app-color font-100 font-16">{{business_profile.location}}</label>
                </div>
                <div v-if="business_profile" style="width: 100%;" class="app-color font-600">
                    <div style="position: relative;width: 95%;margin: 11px auto;">
                        <img v-for="photo in business_profile.business_profile_photos" :src="photo.url" style="width: 32%;height: 150px;object-fit: cover;border-radius: 10%;padding: 5px;">
                    </div>
                </div>
            </div>
            <div v-if="business_profile.menu_status===true && business_profile.loyalty_card_status===true" style="width: 95%; margin: auto;">            
                <div style="text-align: center;width: 100%;padding: 5px;margin: auto;vertical-align: top;display: inline-flex;">
                    <button @click="stamp_tab_active=true;menu_tab_active=false" class="app-border font-600" :class="stamp_tab_active===false?'app-background-color app-color':'app-dark-background app-color-light'" style="display: inline-block;width: 50%;height: 45px;border-right: none;border-top-left-radius: 10px;border-bottom-left-radius: 10px;">
                        {{business_profile.loyalty_card.stamp_free_item}} stamps
                        <div v-if="stamp_tab_active===true" style="margin-top: -15px;">
                            <i class="fa fa-circle" style="color: #FAD09E;font-size: 5px;"></i>
                        </div>
                    </button>
                    <button @click="menu_tab_active=true;stamp_tab_active=false" class="app-border" :class="menu_tab_active===true?'app-dark-background app-color-light':'app-background-color app-color'" style="display: inline-block;width: 50%;height: 45px;border-top-right-radius: 10px;border-bottom-right-radius: 10px;">
                        Menu
                        <div v-if="menu_tab_active===true" style="margin-top: -15px;">
                            <i class="fa fa-circle" style="color: #FAD09E;font-size: 5px;"></i>
                        </div>
                    </button>
                </div>
            </div>
            <div style="background-color: #FAD09E;text-align: center;height: 35px;padding: 5px;margin-top: 12px;">
                <label class="app-color font-600">Visit the store to explore services offered</label>
            </div>
            <div v-if="stamp_tab_active && business_profile.loyalty_card_status===true" class="content mb-0" id="">
                <div style="width: 100%;text-align: center;">
                    <div class="app-dark-background app-color" style="width: 100px;height: 100px;margin: auto;border-radius: 10px;text-align: center;">
                        <span style="color: #FAD09E;line-height: 70px;font-size: 64px;">{{business_profile.loyalty_card.stamp_free_item}}</span>
                        <div style="color: #fff;">Stamps</div>
                    </div>
                </div>
                <div class="mt-3 mb-2" style="width: 100%;text-align: center;">
                    <strong class="font-14 app-color">Collect  {{business_profile.loyalty_card.number_free_item}} more to get a free {{business_profile.loyalty_card.name_free_item}}</strong>
                </div>
                <div class="content mb-0" id="produit-form">
                    <div class="mt-3 mb-2" style="width: 100%;text-align: center;">
                        <strong class="font-20 app-color">Show us this code to collect stamps.</strong>
                    </div>
                    <div style="text-align: center;">
                        <vue-qrcode :value="qrCodeData" :options="{ width: 200 }"></vue-qrcode>
                    </div>
                    
                </div>
            </div>
            <div v-if="menu_tab_active && business_profile.menu_status===true" class="content mb-0" id="">
                <div v-if="items && items.length>0" style="width: 100%;text-align: center;">
                    <div v-for="(item, index) in items" style="margin-bottom: 10px;">
                        <div style="width: 100%; display: inline-block;vertical-align: top;">
                            <div style="width: 70%; display: inline-block;text-align: left;">
                                <strong class="app-color">{{item.name}}</strong>
                            </div>
                            <div style="width: 28%; display: inline-block;text-align: right;">
                                <strong class="app-color">{{item.price}}</strong>
                                <img src="/site_images/pi_v.png" style="height: 18px;">
                            </div>
                        </div>
                        <div style="width: 100%; display: inline-block;vertical-align: top;">
                            <div style="width: 28%; display: inline-block;text-align: left;">
                                <img :src="item.images[0]" style="width: 80px;height: 80px;border-radius: 10px;object-fit: cover;">
                            </div>
                            <div style="width: 70%; display: inline-block;text-align: left;">
                                <div style="width: 100%;">
                                    <strong class="app-color">{{item.description}}</strong>
                                </div>
                                <div style="width: 100%;text-align: center;vertical-align: bottom;">
                                    <span v-if="item.already_added!==true" @click="decrease(index)" class="fa-stack app-border" style="border-radius: 50%;">
                                        <span class="fa-stack" style="width: 1em;height: 1em;line-height: 1em;vertical-align: text-top;">
                                            <i class="fa fa-circle-thin fa-stack-2x" style="opacity: 1;font-size: 1em;"></i>
                                            <i class="fa fa-minus fa-stack-1x app-color" style="opacity: 1;font-size: 6px;line-height: 15px;"></i>
                                        </span>
                                    </span>
                                    <span class="fa-stack app-border" style="border-radius: 20%;margin: auto 10px;" :style="is_in_cart(index)?'background-color: #FAD09E;':'background-color: #fff;'">
                                        {{!item.qty?item.qty=1:item.qty}}
                                    </span>
                                    <span v-if="item.already_added!==true" @click="increase(index)" class="fa-stack app-border" style="border-radius: 50%;">
                                        <span class="fa-stack" style="width: 1em;height: 1em;line-height: 1em;vertical-align: text-top;">
                                            <i class="fa fa-circle-thin fa-stack-2x" style="opacity: 1;font-size: 1em;"></i>
                                            <i class="fa fa-plus fa-stack-1x app-color" style="opacity: 1;font-size: 6px;line-height: 15px;"></i>
                                        </span>
                                    </span>
                                    <span v-if="item.already_added!==true" @click="add_to_cart(index)" class="app-dark-background app-color-light" style="border-radius: 20%;background-color: #fff; padding: 7px 15px;display: inline-block;margin-left: 15px;">
                                        Add
                                    </span>
                                    <i @click="delete_from_cart(index)" style="vertical-align: bottom;" v-if="item.already_added===true" class="fa fa-trash-o app-color font-26"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="isLoading" style="text-align: center;padding-top: 30px;">
            <img src="/site_images/Eclipse-1s-200px.gif" style="height: 100px;">
        </div>
        <div v-else-if="!isPiBrowser" class="loader-background" style="">
            <div style="width: 100%;text-align: center;padding-top: 100px;">
                <button class="btn btn-xxs mb-3 rounded-s font-900 shadow-s app-background-color app-color" style="min-width: 100px;margin: auto;">
                    {{$t('message.please_use_pi_browser')}}
                </button>
            </div>
        </div>
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
    import QuestionOption from '../components/QuestionOption.vue'

    import L from 'leaflet';
    import { GeoSearchControl, OpenStreetMapProvider } from 'leaflet-geosearch';

    export default{
        components: {
            Header, QuestionOption,
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
                business_profile: null,
                stamp_tab_active: true,
                menu_tab_active: false,
                items: [],
                index_to_add: null,
                index_to_delete: null,
                qrCodeData: '',
            }
        },
      computed: {
        ...mapState(['isLoggedIn','default_business_profile', 'isPiBrowser']),
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
        connecting:{
            get(){
                return this.$store.state.connecting
            },
            set(val){
                this.$store.state.connecting = val
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
        shopping:{
            get(){
                return this.$store.state.shopping
            },
            set(val){
                this.$store.state.shopping = val
            }
        }
      },
      beforeRouteEnter(to, from, next) {
          next(vm => {
            vm.prevRoute = from
          })
        },
        mounted() {
            this.getBusinessProfile(this.$route.params.id)
            this.$store.dispatch('scrollToTop')
            let nb_stamps = 0
            if (this.user.user_stamp) {
                nb_stamps = this.user.user_stamp.nb_stamps
            }
            let data = {
                app_id: "pi_fest_map_2024",
                user_id: this.user.id,
                username: this.user.username,
                nb_stamps: nb_stamps
            }
            this.qrCodeData = JSON.stringify(data)
        },
        watch: {
        },
        methods: {
            onMapClick(e) {
                console.log("You clicked the map at " + e.latlng);
            },
            getBusinessProfile(id){
                this.isLoading = true;
                axios.get(`/api/v1/business-profiles/${id}`)
                .then(res => {
                    console.log('getBusinessProfile', res.data)
                    this.isLoading = false;
                    if (res.data.status === true) {
                        /*if (this.business_profile && res.data.business_profile.id!=this.business_profile.id) {
                            this.shopping.shopping_cart = []
                        }*/
                        this.shopping.shopping_cart = []
                        this.business_profile = res.data.business_profile
                        this.items = this.business_profile.items
                        this.shopping.business_profile = this.business_profile

                        if (this.business_profile.loyalty_card_status===false) {
                            this.menu_tab_active = true
                        }
                    } else {
                        this.$show_modal.show_modal({id: 'error', title: "Error", message: "Une erreur s'est produite", btn_text: 'OK'})
                    }
                })
                .catch(error => {
                    console.log(error)
                    this.isLoading = false
                    if (error.response.status !== 401) {
                        this.$show_modal.show_modal({id: 'error', title: "Error", message: "Une erreur s'est produite", btn_text: 'OK'})
                    }
                })
            },
            decrease(index){
                if (this.items[index].qty == undefined || this.items[index].qty == null || this.items[index].qty <= 1) {
                    this.items[index].qty = 1
                    return
                }
                this.items[index].qty = this.items[index].qty - 1
            },
            increase(index){
                if (this.items[index].qty == undefined || this.items[index].qty == null) {
                    this.items[index].qty = 1
                }
                this.items[index].qty = this.items[index].qty + 1
            },
            is_in_cart(index){
                let item = this.items[index]
                let found = false
                if (this.shopping.shopping_cart && this.shopping.shopping_cart.length>0) {
                    this.shopping.shopping_cart.forEach(val=>{
                        if (val.id == item.id) {found =true}
                    })
                }
                this.items[index].already_added = found
                return found
            },
            add_to_cart(index){
                let item = this.items[index]
                let found = false
                if (this.shopping.shopping_cart && this.shopping.shopping_cart.length>0) {
                    this.shopping.shopping_cart.forEach(val=>{
                        if (val.id == item.id) {found =true}
                    })
                }
                if (found === true) {
                    this.$show_modal.show_modal({id: 'error', title: 'Error', message: "Already exists in cart", btn_text: "OK"})
                    return
                }
                this.shopping.shopping_cart.push(this.items[index])
                this.$show_modal.show_modal({id: 'info', title: 'Info', message: "Added successfully", btn_text: "OK"})
            },
            delete_from_cart(index){
                let item = this.items[index]
                //this.shopping.shopping_cart.splice(index, 1);
                let new_cart = []
                this.shopping.shopping_cart.forEach(val=>{
                    if (val.id != item.id) {new_cart.push(val)}
                })
                this.shopping.shopping_cart = new_cart
                this.$show_modal.show_modal({id: 'info', title: 'Info', message: "Deleted successfully", btn_text: "OK"})
            },
            add_to_cart_server_call(index){
                this.index_to_add = index
                this.index_to_delete = null
                this.$show_modal.show_modal({id: 'option', message: "You really want to add to cart ?"})
            },
            delete_from_cart_server_call(index){
                this.index_to_delete = index
                this.index_to_add = null
                this.$show_modal.show_modal({id: 'option', message: "You really want to delete from cart ?"})
            },
            continue_option_server_call(){
                this.$hide_modal.hide_modal('option')
                if (this.index_to_delete !==null) {
                    this.deleting_from_cart()
                }else{
                    this.adding_to_cart()
                }
            },
            adding_to_cart_server_call(){
                let item = this.items[this.index_to_add]
                let found = false
                if (this.user.cart && this.user.cart.length>0) {
                    this.user.cart.forEach(val=>{
                        if (val.id == item.id) {found =true}
                    })
                }
                if (found === true) {
                    this.$show_modal.show_modal({id: 'info', title: 'info', message: "Already exists in cart", btn_text: 'OK'})
                    return
                }
                this.saving = true;
                axios.post('/api/v1/add-to-cart', item)
                .then(res => {
                    console.log(res.data)
                    this.index_to_add = null
                    this.saving = false;
                    if (res.data.status === true) {
                        this.items[this.index_to_add].already_added = true
                        this.$show_modal.show_modal({id: 'success', title: "Success", message: "Added successfully", btn_text: 'OK'})
                    } else if(res.data.message === 'item_exists') {
                        //this.$functions.msg_box(this, 'error', '', this.$t('message.an_error_occured'))
                        this.$show_modal.show_modal({id: 'error', title: "Error", message: "Item already exists", btn_text: 'OK'})
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
            deleting_from_cart_server_call(){
                let item = this.items[this.index_to_delete]
                this.saving = true;
                axios.post('/api/v1/delete-from-cart', {id: item.id})
                .then(res => {
                    console.log(res.data)
                    this.index_to_delete = null
                    this.saving = false;
                    if (res.data.status === true) {
                        this.$show_modal.show_modal({id: 'success', title: "Success", message: "Deleted successfully", btn_text: 'OK'})
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
            }
        }
    }
</script>

<style scoped>
    .active-tab{
        background-color: 
    }
</style>
