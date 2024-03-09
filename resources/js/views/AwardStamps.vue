<template>
    <div>
        <Header v-if="prevRoute" :withAppName="true" :route="prevRoute.path" ref="header" :colorWhite="true" :show_edit="true"/>
        <div v-show="!isLoading" class="page-content app-background-color" style="">
            <div style="padding-top: 30px;padding-bottom: 20px;background-color: #fff;">
                <h3 style="text-align: center;" class="app-color">Award stamps</h3>
            </div>
            <div style="width: 100%;height: 2px;border: 2px solid #090C49;" class="app-color font-600"></div>
            <div class="card card-style app-background-color" style="box-shadow: none;margin-top: 10px;">
                <div v-if="awarded_success===false" style="width: 95%;text-align: center;vertical-align: bottom;margin: auto;">
                    <div style="text-align: center;">
                        <label class="app-color" style="margin-top: 40px;width: 50%;">Pioneer2 needs 5 more stamps to get next free coffee</label>
                    </div>
                    <div style="text-align: center;">
                        <label class="app-color" style="margin-top: 40px;width: 50%;">How many stamps do you want to award ?</label>
                    </div>
                    
                    
                    <div style="text-align: center;">
                        <span @click="decrease" class="fa-stack app-border" style="border-radius: 50%;width: 3em;height: 3em;line-height: 3em;">
                            <span class="fa-stack" style="width: 1em;height: 1em;line-height: 1em;vertical-align: text-top;">
                                <i class="fa fa-circle-thin fa-stack-2x" style="opacity: 1;font-size: 1em;"></i>
                                <i class="fa fa-minus fa-stack-1x app-color" style="opacity: 1;font-size: 6px;line-height: 15px;"></i>
                            </span>
                        </span>
                        <strong class="fa-stack app-border app-color" style="border-radius: 20%;margin: auto 10px;background-color: #fff;font-size: 28px;font-weight: 600;">
                            {{nb_stamps}}
                        </strong>
                        <span @click="increase" class="fa-stack app-border" style="border-radius: 50%;width: 3em;height: 3em;line-height: 3em;">
                            <span class="fa-stack" style="width: 1em;height: 1em;line-height: 1em;vertical-align: text-top;">
                                <i class="fa fa-circle-thin fa-stack-2x" style="opacity: 1;font-size: 1em;"></i>
                                <i class="fa fa-plus fa-stack-1x app-color" style="opacity: 1;font-size: 6px;line-height: 15px;"></i>
                            </span>
                        </span>
                    </div>
                    
                    <button @click="award_stamps"
                    class="font-900 app-background-color" style="margin-top: 20px;background-color: #090C49!important;color: #fff;border-radius: 10px;width: 100%;height: 50px;">
                        Award
                    </button>
                    <button @click="$router.back"
                    class="font-900" style="margin-top: 20px;background-color: transparent!important;color: #090C49;border: 1px solid #090C49!important;border-radius: 10px;width: 100%;height: 50px;">
                        Cancel
                    </button>
                </div>
                <div v-else style="width: 95%;text-align: center;vertical-align: bottom;margin: auto;">
                    <div style="text-align: center;">
                        <h2 class="font-900" style="text-align: center;">Success</h2>
                        <label class="app-color" style="margin-top: 40px;width: 50%;">@Pioneer2 received {{nb_stamps}} stamps from you</label>
                    </div>
                    <button @click="$router.back"
                    class="font-900" style="margin-top: 20px;background-color: transparent!important;color: #090C49;border: 1px solid #090C49!important;border-radius: 10px;width: 100%;height: 50px;">
                        Back
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
    import axios from 'axios';
    import { mapState } from 'vuex';

    import Header from '../components/Header.vue'

    import { QrcodeStream, QrcodeDropZone, QrcodeCapture } from 'vue3-qrcode-reader'

    export default{
        components: {
            Header,
            QrcodeStream,
            QrcodeDropZone,
            QrcodeCapture
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
          nb_stamps: 1,
          user_id: null,
          awarded_success: false,
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
            this.user_id = this.$router.params.user_id
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
            onDecode (decodedString) {
                let data = JSON.parse(decodedString)
                if (data.app_id == "pi_fest_map_2024") {

                }
                alert(decodedString)
                console.log("decodedString", decodedString)
            },
            decrease(){
                if (this.nb_stamps == undefined || this.nb_stamps == null || this.nb_stamps <= 1) {
                    this.nb_stamps = 1
                    return
                }
                this.nb_stamps = this.nb_stamps - 1
            },
            increase(){
                if (this.nb_stamps == undefined || this.nb_stamps == null) {
                    this.nb_stamps = 1
                }
                this.nb_stamps = this.nb_stamps + 1
            },
            award_stamps(){
                this.saving = true;
                axios.post(`/api/v1/award-stamps/${this.business_profile.id}/${this.user_id}`, {nb_stamps: this.nb_stamps})
                .then(res => {
                    console.log(res.data)
                    this.saving = false;
                    if (res.data.status === true) {
                        this.$show_modal.show_modal({id: 'success', title: "Success", message: "Saved successfully", btn_text: 'OK'})
                        this.awarded_success = true
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
</style>
