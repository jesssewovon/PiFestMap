<template>
    <div style="">
        <HeaderHome ref="header_home"/>
        <FooterHome />
        <SelectCountry @propagation="propagationResult" :show_all="true" />
        <div class="page-content" style="margin-top: 0px;">
            <div class="" style="margin-bottom: 10px;position: absolute;top: 30px;z-index: 11;width: 100%;">
                <div class="" style="width: 90%;margin: auto;border: 2px solid #090C49;border-radius: 10px;padding: 5px;background-color: #fff;">
                    <!-- <i class="fas fa-search" style="font-size: 20px;color: #090C49;padding: auto 10px;width: 10%;" @click="searching"></i> --><input type="text" v-model="search" :placeholder="$t('message.search_business')" style="display: inline-block;height: 40px;font-size: 14px !important;color: #090C49;border: none;width: 76%;margin: auto 5px;">
                    <div style="display: inline-block;text-align: right;width: 20%">
                        <button @click="searching" style="color: #fff;border-radius: 20%;padding: 0 10px!important;" class="app-dark-background">
                            <i class="fas fa-search" style="color: #fff;padding: 10px 5px;"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div style="height:600px; width:100%;">
                <l-map ref="map" v-model:zoom="zoom" :center="center" @move="log('move')" @click="getLocation" style="z-index: 1;" :options="{zoomControl: false}">
                  <l-control-zoom position="bottomleft" :options="{class: 'leaflet-control-zoom'}"></l-control-zoom>
                  <l-tile-layer
                    :url="url"
                    layer-type="base"
                    name="OpenStreetMap"
                    :attribution="attribution"
                  ></l-tile-layer>

                  <!-- <l-control-layers /> -->
                  <l-marker :lat-lng="clicked_marker" v-if="clicked_marker">
                      <!-- <l-popup>Clicked Location</l-popup> -->
                      <l-icon :icon-url="iconUrl" :icon-size="[20, 30]" />
                  </l-marker>
                  <!-- <l-circle-marker :lat-lng="userLocation" :fillOpacity="1" :radius="0.1" >
                    <l-popup>Current Location</l-popup>
                  </l-circle-marker> -->
                  <l-marker :lat-lng="userLocation" v-if="userLocation!==null" :radius="0.1">
                      <!-- <l-popup>Current Location</l-popup> -->
                      <l-icon :icon-url="iconUrlCurrentPos" :icon-size="[25, 25]" />
                  </l-marker>
                  <!-- <l-marker :lat-lng="[47.41322, -1.219482]">
                    <l-icon :icon-url="iconUrl" :icon-size="[20, 30]" />
                  </l-marker> -->
                  <span v-for="bp in business_profiles">
                      <l-marker :lat-lng="[bp.latitude, bp.longitude]" @click="show_business_profile(bp)">
                        <l-icon :icon-url="bp.business_type.map_img" :icon-size="iconSize" />
                      </l-marker>
                  </span>
                </l-map>
            </div>
            <div v-if="connecting || disconnecting" class="loader-background" style="z-index: 97;">
                <div style="text-align: center;width: 100%;margin-top: 150px;">
                    <img src="/site_images/transparent-gif/loading3.gif" style="width: 75px;margin-top: 20px;border-radius: 10%;">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import i18ns from '../i18n';
    import 'vue3-carousel/dist/carousel.css'
    import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'
    import { mapState } from 'vuex';

    import 'vue-masonry-css';
    //import { LMap, LTileLayer, LMarker, LPopup } from 'vue3-leaflet'; // Import necessary components
    import { LMap, LMarker, LCircleMarker, LPopup, LTileLayer, LIcon, LControlLayers, LPolyline, LPolygon, LRectangle, LControlZoom } from "@vue-leaflet/vue-leaflet";
    import L from 'leaflet'; // Import Leaflet

    import { GeoSearchControl, OpenStreetMapProvider } from 'leaflet-geosearch';

    import HeaderHome from '../components/HeaderHome.vue'
    import FooterHome from '../components/FooterHome.vue'

    export default{
        components: {
            Carousel, Slide, Pagination, Navigation, HeaderHome, FooterHome,
            //AdSense,
            //LMap, LTileLayer, LMarker, LPopup, L,
            LMap, LMarker, LCircleMarker, LPopup, LTileLayer, LIcon, LControlLayers, LPolyline, LPolygon, LRectangle, LControlZoom,
        },
        data() {
            return {
                search: "",
                //zoom: 13,
                zoom: 1,
                //center: [51.505, -0.09],
                url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                //attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                attribution: "PiFestMap &copy 2024",
                controlPosition: 'topright', // Position of the control
                iconWidth: 25,
                iconHeight: 35,
                clicked_marker: null,
                business_profiles: [],
            };
        },
        computed: {
            //...mapState(['test', 'user', 'isLoading', 'categories']),
            ...mapState(['prevRoute', 'isPiBrowser', 'isLoggedIn', 'show_welcome']),
            isOpenLoading:{
                get(){
                    return this.$store.state.isOpenLoading
                },
                set(val){
                    this.$store.state.isOpenLoading = val
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
            userLocation:{
                get(){
                    return this.$store.state.userLocation
                },
                set(val){
                    this.$store.state.userLocation = val
                }
            },
            center:{
                get(){
                    return this.$store.state.center
                },
                set(val){
                    this.$store.state.center = val
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
            connecting:{
                get(){
                    return this.$store.state.connecting
                },
                set(val){
                    this.$store.state.connecting = val
                }
            },
            disconnecting:{
                get(){
                    return this.$store.state.disconnecting
                },
                set(val){
                    this.$store.state.disconnecting = val
                }
            },
            deleting:{
                get(){
                    return this.$store.state.deleting
                },
                set(val){
                    this.$store.state.deleting = val
                }
            },
            isSearching:{
                get(){
                    return this.$store.state.isSearching
                },
                set(val){
                    this.$store.state.isSearching = val
                }
            },
            locale:{
                get(){
                    return this.$store.state.locale
                },
                set(val){
                    this.$store.state.locale = val
                }
            },
            iconUrl() {
              return `/site_images/marker.png`;
              //return `https://placekitten.com/${this.iconWidth}/${this.iconHeight}`;
            },
            iconUrlCurrentPos() {
              return `/site_images/current_pos.png`;
            },
            iconSize() {
              return [this.iconWidth, this.iconHeight];
            },
        },
        beforeRouteEnter(to, from, next) {
          next(vm => {
            //vm.prevRoute = from
            vm.$store.state.prevRoute = from
          })
        },
        created(){
            //this.getCategories();
            //Pi.nativeFeaturesList()
        },
        mounted() {
            //this.$store.dispatch('getUserLocation');
            /*if (this.show_welcome) {
                this.$show_modal.show_modal({id:'welcome'})
                this.$store.dispatch('disable_welcome')
            }*/
            //alert(this.test)
            this.$store.commit('deleteAttribute', "center");
            this.center = [44.402391829094, -122.607421875]
            //console.log('center', this.center)
            //alert(this.center[0])
            this.$store.dispatch('setConfDialog', this.$confirm)
            this.deleting = false
            this.saving = false
            this.connecting = false
            this.disconnecting = false
            this.isSearching = false
            //console.log('usserr', this.user)
            this.index_load_opening()

            let data = {
                confirm: this.$confirm,
                i18n: this.$i18n,
                self: this
            }
            if (this.isLoggedIn) {
                this.$i18n.locale = this.user.locale
                data.isLoggedIn = this.isLoggedIn
                this.$store.dispatch('signInPiNetwork', data)
            }
            //console.log('token', this.$store.state.token)
        },
        watch: {
            user(after, before) {
                this.load_header_and_menu()
            },
        },
        methods: {
            zoomIn() {
              this.zoom++;
            },
            zoomOut() {
              this.zoom--;
            },
            log(a) {
              console.log(a);
            },
            getLocation(event) {
              console.log(event);
              this.clicked_marker = event.latlng;
            },
            reload(){
                this.isLoading = true
                this.index_load_opening()
            },
            searching(){
                this.isSearching = true
                this.index_load_opening()
            },
            async index_load_opening(){
                let res = await axios.get(`/api/v1/index-loading?search=${this.search}`)
                .then(res => {
                    console.log('index data-loading', res.data)
                    this.isLoading = false
                    this.isLoadingMore = false
                    this.isOpenLoading = false;
                    this.isSearching = false;
                    this.business_profiles = res.data.business_profiles;
                    this.$currencyFunction.setCurrency(res.data.currency)
                    let dataSettings = {
                        languages: res.data.languages,
                        agreements: res.data.agreements,
                        purchase_activation: res.data.purchase_activation,
                        mining_activation: res.data.mining_activation,
                        pibrowser_verification: res.data.pibrowser_verification,
                        transfer_fee_pi_network: res.data.transfer_fee_pi_network,
                        transfer_fee_piket: res.data.transfer_fee_piket,
                        transfer_fee_piket_activation: res.data.transfer_fee_piket_activation,
                        business_types: res.data.business_types,
                    }
                    this.$store.dispatch('setSettings', dataSettings)
                })
                .catch(error => {
                    //console.log(error)
                    this.isLoading = false
                    this.isLoadingMore = false
                    this.isOpenLoading = false;
                    this.isSearching = false;
                });
            },
            show_business_profile(bp){
                //alert(bp.id)
                if (this.isLoggedIn===false) {
                  this.$show_modal.show_modal({id: 'error', title: "Error", message: this.$t('message.log_in_first'), btn_text: 'OK'})
                  return
                }
                this.$router.push(`/business-profile-page/${bp.id}`);
            }
        },
        watch: {
            search(newValue, old){
                //this.searching();
                if (newValue == '') {
                    this.searching();
                }
            },
            product_tab_active(a, b){

            }
            /*isLoading(newValue, old){
                $('#footer-bar').css('display', 'none');
                if (!newValue) {
                    $('#footer-bar').css('display', 'flex');
                }
            }*/
        }
    }
</script>

<style scoped>
    @import url("https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
    .map-container {
        height: 100vh; /* Set the height of the container to 100% of the viewport height */
    }
    .no_more_data{
        /*display: none;*/
        margin: 10px;
        padding: 10px;
        text-align: center;
        background-color: #f0f0f0;
        border-radius: 10px;
    }

    .break-word{
        width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
    }
    .break-word-shopname{
        width: 100px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
    }
    .break-word-productname{
        width: 100px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
    }
    .carddd .card__containerrr{
      padding : 2rem;
      width: 100%;
      height: 100%;
      background: white;
      border-radius: 1rem;
    }
    .carddd::before{
      position: absolute;
      content: '';
      background: #283593;
      height: 14px;
      width: 18px;
      top: 22px;
      right: 0px;
      transform : rotate(45deg);
      z-index: -1;
    }
    .carddd::after{
      position: absolute;
      content: 'gfhhf';
      top: 11px;
      right: -2px;
      padding: 0;
      width: 55px;
      font-size: 9px;
      height: 22px;
      border-top-left-radius: 5px;
      border-bottom-left-radius: 5px;
      background: #3949ab;
      color: white;
      text-align: center;
      font-family: 'Roboto', sans-serif;
      box-shadow: 4px 4px 15px rgba(26, 35, 126, 0.2);
    }
/* Apply Masonry layout styles */
.masonry {
  column-count: 2; /* Set the number of columns as needed */
  column-gap: 0px; /* Adjust the gap between columns */
}

 /* Extra small devices (phones, 600px and down) */
@media only screen and (max-width: 600px) {}

/* Small devices (portrait tablets and large phones, 600px and up) */
@media only screen and (min-width: 600px) {
    .masonry {
      column-count: 3; /* Set the number of columns as needed */
      column-gap: 0px; /* Adjust the gap between columns */
    }
}

/* Medium devices (landscape tablets, 768px and up) */
@media only screen and (min-width: 768px) {
    .masonry {
      column-count: 4; /* Set the number of columns as needed */
      column-gap: 0px; /* Adjust the gap between columns */
    }
}

/* Large devices (laptops/desktops, 992px and up) */
@media only screen and (min-width: 992px) {
    .masonry {
      column-count: 5; /* Set the number of columns as needed */
      column-gap: 0px; /* Adjust the gap between columns */
    }
}

/* Extra large devices (large laptops and desktops, 1200px and up) */
@media only screen and (min-width: 1200px) {
    .masonry {
      column-count: 6; /* Set the number of columns as needed */
      column-gap: 0px; /* Adjust the gap between columns */
    }
} 

.masonry-item {
  break-inside: avoid; /* Prevent items from breaking inside columns */
  margin-bottom: 20px; /* Adjust item margin as needed */
}

.masonry-item img {
  width: 100%; /* Ensure images fill their container */
  display: block;
}
</style>