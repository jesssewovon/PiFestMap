<template>
    <div>
        <Header v-if="prevRoute" :withAppName="true" :route="prevRoute.path" ref="header"/>
        <div v-if="!isLoading" class="page-content app-background-color" style="">
            <div style="padding-top: 30px;padding-bottom: 20px;">
                <h3 style="text-align: center;" class="app-color">
                    Recent orders
                </h3>
            </div>
            <div style="width: 100%;height: 2px;border: 2px solid #090C49;" class="app-color font-600"></div>
            <div style="width: 95%; margin: auto;margin-top: 20px;">            
                <div style="text-align: center;width: 100%;padding: 5px;margin: auto;vertical-align: top;display: inline-flex;">
                    <button @click="pending_tab_active=true;history_tab_active=false" class="app-border font-600" :class="pending_tab_active===false?'app-background-color app-color':'app-dark-background app-color-light'" style="display: inline-block;width: 50%;height: 45px;border-right: none;border-top-left-radius: 10px;border-bottom-left-radius: 10px;">
                        Pending orders
                        <div v-if="pending_tab_active===true" style="margin-top: -15px;">
                            <i class="fa fa-circle" style="color: #FAD09E;font-size: 5px;"></i>
                        </div>
                    </button>
                    <button @click="history_tab_active=true;pending_tab_active=false" class="app-border" :class="history_tab_active===true?'app-dark-background app-color-light':'app-background-color app-color'" style="display: inline-block;width: 50%;height: 45px;border-top-right-radius: 10px;border-bottom-right-radius: 10px;">
                        History
                        <div v-if="history_tab_active===true" style="margin-top: -15px;">
                            <i class="fa fa-circle" style="color: #FAD09E;font-size: 5px;"></i>
                        </div>
                    </button>
                </div>
            </div>
            <div v-if="history_tab_active" class="content mb-0" id="">
                <!-- <div v-if="business_profile.items && business_profile.items.length>0" style="width: 100%;text-align: center;">
                    <div v-for="item in business_profile.items" style="margin-bottom: 10px;">
                        <div style="width: 100%; display: inline-block;vertical-align: top;">
                            <div style="width: 70%; display: inline-block;text-align: left;">
                                <strong class="app-color">{{item.name}}</strong>
                            </div>
                            <div style="width: 28%; display: inline-block;text-align: right;">
                                <strong class="app-color">{{item.price}}</strong>
                                <img src="/site_images/pi_v.png">
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
                                <div style="width: 100%;text-align: center;">
                                    <span class="fa-stack app-border" style="border-radius: 50%;">
                                        <span class="fa-stack" style="width: 1em;height: 1em;line-height: 1em;vertical-align: text-top;">
                                            <i class="fa fa-circle-thin fa-stack-2x" style="opacity: 1;font-size: 1em;"></i>
                                            <i class="fa fa-minus fa-stack-1x app-color" style="opacity: 1;font-size: 6px;line-height: 15px;"></i>
                                        </span>
                                    </span>
                                    <span class="fa-stack app-border" style="border-radius: 20%;margin: auto 10px;background-color: #fff;">
                                        2
                                    </span>
                                    <span class="fa-stack app-border" style="border-radius: 50%;">
                                        <span class="fa-stack" style="width: 1em;height: 1em;line-height: 1em;vertical-align: text-top;">
                                            <i class="fa fa-circle-thin fa-stack-2x" style="opacity: 1;font-size: 1em;"></i>
                                            <i class="fa fa-plus fa-stack-1x app-color" style="opacity: 1;font-size: 6px;line-height: 15px;"></i>
                                        </span>
                                    </span>
                                    <span class="app-dark-background app-color-light" style="border-radius: 20%;background-color: #fff; padding: 7px 15px;display: inline-block;margin-left: 15px;">
                                        Add
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
        <div v-else-if="isLoading" style="text-align: center;">
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
    import axios from 'axios';
    import { mapState } from 'vuex';
    import { v4 as uuidv4 } from 'uuid';

    import Header from '../components/Header.vue'

    import L from 'leaflet';
    import { GeoSearchControl, OpenStreetMapProvider } from 'leaflet-geosearch';

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
                business_profile: null,
                pending_tab_active: true,
                history_tab_active: false,
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
        selected_category:{
            get(){
                return this.$store.state.selected_category
            },
            set(val){
                this.$store.state.selected_category = val
            }
        }
      },
      beforeRouteEnter(to, from, next) {
          next(vm => {
            vm.prevRoute = from
          })
        },
        mounted() {
            this.isLoading = false
            this.$store.dispatch('scrollToTop')
        },
        watch: {
        },
        methods: {
        }
    }
</script>

<style scoped>
    .active-tab{
        background-color: 
    }
</style>
