<template>
    <div>
        <Header v-if="prevRoute" :withAppName="false" :route="prevRoute.path" ref="header"/>
        <div v-show="!isLoading" class="page-content app-background-color" style="padding-top: 30px;">
            <div style="width: 100%;text-align: center;">
                <img src="/site_images/logo.png" alt="avatar" style="width: 40px;height: 40px;border-radius: 10%;object-fit: cover;">&nbsp;
                <strong class="font-26 app-color">
                    Pi Fest Map
                </strong>
            </div>
            <div class="card card-style app-background-color" style="box-shadow: none;">
                <div class="content mb-0" id="produit-form">
                    <div class="mt-3 mb-2" style="width: 100%;text-align: center;">
                        <strong class="font-14 app-color">For use with the PiFestMap Pi App.</strong>
                    </div>
                    <div style="text-align: center;">
                        <vue-qrcode value="Hello, World!" :options="{ width: 200 }"></vue-qrcode>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    @import url("https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
</style>

<script>
    import axios from 'axios';
    import { mapState } from 'vuex';

    import Header from '../components/Header.vue'

    export default{
        components: {
            Header,
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
            isLoggedIn(newVal, oldVal){
                
            },
        },
        methods: {
        }
    }
</script>

<style scoped>
</style>
