<template>
    <div v-show="!isOpenLoading && $route.name !='support' && $route.name !='message'" id="footer-bar" class="footer-bar-6 footer_menu_hide">

        <div style="width: 95%;margin: auto;">
            <div v-if="!isLoggedIn" style="display: inline-block;width: 100%;">
                <button @click="$store.dispatch('signInPiNetwork', {
                    confirm: $confirm,
                    i18n: $i18n,
                    self: this
                })" style="background-color: #090C49;width: 90%;margin: auto;border-radius: 9px;color: #fff;height: 40px;">
                    <i class="fa fa-sign-in" style="opacity: 1; color: #FAD09E;"></i>
                    {{$t('message.log_in')}}
                </button>
            </div>
            <div v-if="isLoggedIn" style="display: inline-block;width: 30%;">
                <button @click="$router.push('/dashboard')" style="background-color: #090C49;width: 90%;margin: auto;border-radius: 9px;color: #fff;height: 40px;">
                    <i class="fa fa-dashboard" style="opacity: 1; color: #FAD09E;"></i>
                    {{$t('message.dashboard')}}
                </button>
            </div>
            <div v-if="isLoggedIn" style="width: 48%;display: inline-block;">
                <button @click="$router.push('/business-profile')" style="background-color: #090C49;width: 90%;margin: auto;border-radius: 9px;color: #fff;height: 40px;">
                    <span class="fa-stack">
                        <i class="fa fa-circle-thin fa-stack-2x" style="opacity: 1; color: #FAD09E;"></i>
                        <i class="fa fa-plus fa-stack-1x" style="opacity: 1; color: #FAD09E;"></i>
                    </span>
                    {{$t('message.add_my_business')}}
                </button>
            </div>
            <div v-if="isLoggedIn" style="width: 18%;display: inline-block;">
                <button @click="getUserLocation" style="background-color: #090C49;width: 40px;margin: auto;border-radius: 50%;color: #fff;height: 40px;">
                    <!-- <i class="fa fa-map" style="color: #FAD09E;"></i> -->
                    <img src="/site_images/navigator.png">
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    export default {
    components: {
    },
    data() {
        return {
            show: true,
        };
    },
    computed: {
        ...mapState(['isLoggedIn', 'isLoading', 'isOpenLoading']),
        user:{
            get(){
                return this.$store.state.user
            },
            set(val){
                this.$store.state.user = val
            }
        },
        userLocation:{
            get(){
                return this.$store.state.userLocation
            },
            set(val){
                this.$store.state.userLocation = val
            }
        },
        business_profile:{
            get(){
                return this.$store.state.business_profile
            },
            set(val){
                this.$store.state.business_profile = val
            }
        }
    },
    methods: {
        visible(value = true){
            this.show = value
        },
        openingHome(){
            this.isOpenLoading = false
            this.$router.push('/')
        },
        getUserLocation(){
            this.$store.dispatch('getUserLocation');
            //this.userLocation = [6.4026, 1.2030]
        },
    }
}
</script>

<style scoped>
    .custom_badge{
    	right: -19px;
		top: -23px;
		background-color: red;
		color: white;
		border-radius: 50%;
		padding: 4px;
    }

    /*.footer-bar-6 .circle-nav strong {
      position: absolute;
      width: 50px;
      height: 50px;
      border-radius: 50px;
      left: 50%;
      top: -23px;
      z-index: 0;
      transform: translateX(-50%);
      box-shadow: 0 5px 15px 0 rgba(0,0,0,.09);
      animation: footerAni 1s infinite;
      background-image: linear-gradient(to bottom,#812a6b 0,#812a6b 100%) !important;
    }*/
</style>
