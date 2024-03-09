<template>
    <div>
        <Header v-if="prevRoute" :withAppName="false" :route="prevRoute.path" ref="header"/>
        <div v-show="!isLoading" class="page-content app-background-color" style="padding-top: 30px;">
            <qrcode-stream @decode="onDecode"></qrcode-stream>
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
            onDecode (decodedString) {
                alert(decodedString)
                console.log("decodedString", decodedString)
            }
        }
    }
</script>

<style scoped>
</style>
