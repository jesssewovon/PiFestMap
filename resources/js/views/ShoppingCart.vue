<template>
    <div>
        <Header v-if="prevRoute" :withAppName="true" :route="prevRoute.path" ref="header" :colorWhite="true"/>
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
        
        <div class="page-content app-background-color" style="">
            <div style="padding-top: 30px;padding-bottom: 20px;background-color: #fff;">
                <h3 style="text-align: center;" class="app-color">Shopping Cart</h3>
            </div>
            <div style="width: 100%;height: 2px;border: 2px solid #090C49;" class="app-color font-600"></div>
            <div class="card card-style app-background-color" style="box-shadow: none;margin-top: 10px;">
                <div v-if="shopping.shopping_cart.length>0" style="width: 100%;">
                    <div class="card card-style" style="box-shadow: none;margin: 5px;padding: 25px 15px;border-radius: 5px;">
                        <label style="" class="app-color font-600 font-18">{{shopping.business_profile.name}}</label>
                        <label v-if="shopping.business_profile.payment_status===true" class="app-color font-600 font-18">
                            Make payment
                        </label>
                        <div v-for="item in shopping.shopping_cart" style="position: relative;width: 100%;margin: 11px auto;">
                            <img :src="item.images[0]" style="width: 30px;height: 30px;object-fit: cover;border-radius: 10%;">&nbsp;
                            <label class="app-color">{{item.qty}} x {{item.name}}</label>
                        </div>
                        <div style="width: 100%; display: inline-block;vertical-align: top;">
                            <div style="width: 70%; display: inline-block;text-align: left;">
                                <strong class="app-color">Total Price</strong>
                            </div>
                            <div style="width: 28%; display: inline-block;text-align: right;">
                                <strong class="app-color">{{total}}</strong>
                                <img src="/site_images/pi_v.png" style="height: 18px;">
                            </div>
                        </div>
                        <button v-if="shopping.business_profile.orders_status===true && shopping.business_profile.payments_status===false" @click="save_item" class="font-900 app-background-color" style="margin-top: 20px;background-color: #090C49!important;color: #fff;border-radius: 10px;width: 100%;height: 50px;">
                            Order
                        </button>
                        <button v-if="shopping.business_profile.payments_status===true" @click="save_item" class="font-900 app-background-color" style="margin-top: 20px;background-color: #090C49!important;color: #fff;border-radius: 10px;width: 100%;height: 50px;">
                            Pay by Pi
                        </button>
                    </div>
                </div>
                <div v-else-if="shopping.shopping_cart.length==0" style="text-align: center;">
                    <button class="font-900 app-background-color" style="margin-top: 20px;background-color: #090C49!important;color: #fff;border-radius: 5px;width: 100%;height: 50px;">
                        Empty cart
                    </button>
                </div>
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
              cart: [],
              total: 0.00,
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
        shopping:{
            get(){
                return this.$store.state.shopping
            },
            set(val){
                this.$store.state.shopping = val
            }
        },
      },
      beforeRouteEnter(to, from, next) {
          next(vm => {
            vm.prevRoute = from
          })
        },
        mounted() {
            //this.getCart()
            let tot = 0.00
            this.shopping.shopping_cart.forEach(val=>{
                tot += val.price * val.qty
            })
            this.total = tot
        },
        watch: {
        },
        methods: {
            getCart(){
                this.isLoading = true;
                axios.get('/api/v1/get-cart')
                .then(res => {
                    console.log('getCart', res.data)
                    this.isLoading = false;
                    if (res.data.status === true) {
                        this.cart = res.data.cart
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
            decrease(index){
                if (this.cart[index].qty == undefined || this.cart[index].qty == null || this.cart[index].qty <= 1) {
                    this.cart[index].qty = 1
                    return
                }
                this.cart[index].qty = this.cart[index].qty - 1
            },
            increase(index){
                if (this.cart[index].qty == undefined || this.cart[index].qty == null) {
                    this.cart[index].qty = 1
                }
                this.cart[index].qty = this.cart[index].qty + 1
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
