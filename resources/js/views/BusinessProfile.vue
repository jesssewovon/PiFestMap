<template>
    <div>
        <Header v-if="prevRoute" :withAppName="true" :route="prevRoute.path" ref="header"/>
        <div class="page-content app-background-color" style="">
            <div style="padding-top: 30px;padding-bottom: 20px;">
                <h3 style="text-align: center;" class="app-color">Contact Information</h3>
            </div>
            <div class="card card-style app-background-color" style="box-shadow: none;">
                <div class="content mb-0" id="produit-form">
                    <label for="libelle" class="app-color">Business name</label>
                    <div class="input-style has-borders validate-field mb-4">
                        <input v-on:input="hide_error_field('libelle')" type="text" class="form-control validate-name app-color app-border" id="libelle" placeholder="" v-model="business_profile.name" maxlength="40" style="">
                        
                        <i class="fa fa-times disabled invalid color-red-dark"></i>
                        <i class="fa fa-check disabled valid color-green-dark"></i>
                        <div id="required-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                        <div id="length-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                    </div>
                    <label for="libelle" class="app-color">Business type</label>
                    <div data-menu="select-business-type" style="background-color: #fff;padding: 15px;border-radius: 10px;margin-bottom: 20px;" >
                        <img v-if="business_profile.business_type==null" id="category_selected_img" src="/images/unknown.png" style="width: 20px;height: 20px;">
                        <img v-else id="category_selected_img" :src="'/images/'+business_profile.business_type.img" style="width: 20px;">
                        <label v-if="business_profile.business_type==null" id="category_selected_label" for="form5" class="color-piketplace" style="margin-left: 22px;">{{ $t('message.select_business_type') }}</label>
                        <label v-else id="category_selected_label" for="form5" class="color-piketplace" style="margin-left: 22px;">{{$t('message.business_type.'+business_profile.business_type.code)}}</label>
                        <span><i class="fa fa-chevron-down" style="float: right;"></i></span>
                        <input type="hidden" id="category_selected_id">
                        <div id="required-categories_id" style="color: red;display: none;">{{ $t('message.required.categories_id') }}</div>
                    </div>
                    <label for="libelle" class="app-color">Business Map Point</label>
                    <div id="map-container" style="height: 300px;"></div>
                    <div class="input-style has-borders validate-field mb-4">
                        <input type="text" class="form-control validate-name app-color app-border" id="libelle" placeholder="" v-model="lat_lng" maxlength="40" style="" disabled>
                        
                        <i class="fa fa-times disabled invalid color-red-dark"></i>
                        <i class="fa fa-check disabled valid color-green-dark"></i>
                        <div id="required-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                        <div id="length-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                    </div>
                    <label for="libelle" class="app-color">Location</label>
                    <div class="input-style has-borders validate-field mb-4">
                        <input type="text" class="form-control validate-name app-color app-border" id="libelle" placeholder="" v-model="business_profile.location" maxlength="40" style="">
                        
                        <i class="fa fa-times disabled invalid color-red-dark"></i>
                        <i class="fa fa-check disabled valid color-green-dark"></i>
                        <div id="required-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                        <div id="length-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                    </div>
                    <div @click="go_to('/menu')" style="background-color: #FAD09E;padding: 15px;border-radius: 10px;margin-bottom: 20px;" >
                        <label for="form5" class="font-600 app-color" style="margin-left: 22px;">Menu</label>
                        <span><i class="fa fa-arrow-right app-color font-600" style="float: right;"></i></span>
                        <input type="hidden" id="category_selected_id">
                    </div>
                    <div @click="go_to('/loyalty-card')" style="background-color: #FAD09E;padding: 15px;border-radius: 10px;margin-bottom: 20px;" >
                        <label for="form5" class="font-600 app-color" style="margin-left: 22px;">Loyalty stamps</label>
                        <span><i class="fa fa-arrow-right app-color font-600" style="float: right;"></i></span>
                        <input type="hidden" id="category_selected_id">
                    </div>
                    <div @click="go_to('business-profile-photos')" style="background-color: #FAD09E;padding: 15px;border-radius: 10px;margin-bottom: 20px;" >
                        <label for="form5" class="font-600 app-color" style="margin-left: 22px;">Business photos</label>
                        <span><i class="fa fa-arrow-right app-color font-600" style="float: right;"></i></span>
                        <input type="hidden" id="category_selected_id">
                    </div>
                    <div @click="go_to('/business-profile-qr-code')" style="background-color: #FAD09E;padding: 15px;border-radius: 10px;margin-bottom: 20px;" >
                        <label for="form5" class="font-600 app-color" style="margin-left: 22px;">Get QR code</label>
                        <span><i class="fa fa-arrow-right app-color font-600" style="float: right;"></i></span>
                        <input type="hidden" id="category_selected_id">
                    </div>
                    <button @click="save"
                    class="font-900 app-background-color" style="margin-top: 20px;background-color: #090C49!important;color: #fff;border-radius: 10px;width: 100%;height: 50px;">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
        <!-- <div v-if="isLoading" style="text-align: center;">
            <img src="/site_images/Eclipse-1s-200px.gif" style="height: 100px;">
        </div> -->
        <div v-if="!isPiBrowser" class="loader-background" style="">
            <div style="width: 100%;text-align: center;padding-top: 100px;">
                <button class="btn btn-xxs mb-3 rounded-s font-900 shadow-s app-background-color app-color" style="min-width: 100px;margin: auto;">
                    {{$t('message.please_use_pi_browser')}}
                </button>
            </div>
        </div>
        <div v-else-if="!isLoggedIn" class="loader-background" style="">
            <div style="width: 100%;text-align: center;padding-top: 100px;">
                <button @click="connecting=true;$store.dispatch('signInPiNetwork', {confirm: $confirm, self: this})" class="btn btn-xxs mb-3 rounded-s font-900 shadow-s  app-background-color app-color" style="min-width: 100px;margin: auto;">
                    {{$t('message.log_in_first')}}
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
    @import url("https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
    @import url("https://unpkg.com/leaflet-geosearch@3.11.1/assets/css/leaflet.css");
</style>

<script>
    //import UseDropzoneDemo from '../components/UseDropzoneDemo.vue'
    //import { Dropzone } from "dropzone";
    //import VueLoadingButton from "vue-loading-button";
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
                lat_lng: '',
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
        business_profile:{
            get(){
                return this.$store.state.business_profile
            },
            set(val){
                this.$store.state.business_profile = val
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
            if (this.user.business_profile!==null) {
                this.business_profile = this.user.business_profile
            }

            const provider = new OpenStreetMapProvider();

            const searchControl = new GeoSearchControl({
              provider: provider,
              style: 'bar', // optional: bar|button  - default button
              searchLabel: 'Enter your address', // optional: string      - default 'Enter address'
            });
            /*const map = new L.Map('map-container');
            map.addControl(searchControl);*/

            let center = [45.46, -122.739]
            let zoom = 4
            if (this.business_profile.latitude) {
                center = [this.business_profile.latitude, this.business_profile.longitude]
                zoom = 15
                this.lat_lng = this.business_profile.latitude +' / '+ this.business_profile.longitude
            }

            var tileLayer = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {maxZoom: 20, attribution: 'PiFestMap &copy 2024'});

            var map = new L.map('map-container', {
                layers: tileLayer
            }).setView(center, zoom);
            if (this.business_profile.latitude) {
                L.marker(center).addTo(map);
            }
            map.addControl(searchControl)
            let self = this
            map.on('click', function(e){
                console.log(e.latlng)
                //Remove all the last markers
                map.eachLayer((layer) => {
                  if (layer instanceof L.Marker) {
                     layer.remove();
                  }
                });
                //Add the new marker
                let marker = L.marker([e.latlng.lat, e.latlng.lng])
                marker.addTo(map);
                self.lat_lng = e.latlng.lat+" / "+e.latlng.lng
                self.business_profile.latitude = e.latlng.lat
                self.business_profile.longitude = e.latlng.lng
            });
            map.on('geosearch/showlocation', function (e) {
                //Remove all the last markers when choosing location
                map.eachLayer((layer) => {
                    if (layer instanceof L.Marker) {
                        layer.remove();
                    }
                });
            });

            console.log('Component mounted.')
            require('../../../public/template/scripts/bootstrap.min.js');
            
            var _0x77b9x35 = document[_0x962d[24]](_0x962d[99]);
            var _0x77b9x36 = document[_0x962d[24]](_0x962d[100]);
            _0x77b9x35[_0x962d[22]]((_0x77b9xc) => {
                return _0x77b9xc[_0x962d[37]](_0x962d[64], (_0x77b9xb) => {
                    const _0x77b9x37 = document[_0x962d[24]](_0x962d[101]);
                    for (let _0x77b9xa = 0; _0x77b9xa < _0x77b9x37[_0x962d[12]]; _0x77b9xa++) {
                        _0x77b9x37[_0x77b9xa][_0x962d[4]][_0x962d[18]](_0x962d[16]);
                    }
                    var _0x77b9x38 = _0x77b9xc[_0x962d[47]](_0x962d[102]);
                    document[_0x962d[1]](_0x77b9x38)[_0x962d[4]][_0x962d[3]](_0x962d[16]);
                    document[_0x962d[11]](_0x962d[10])[0][_0x962d[4]][_0x962d[3]](_0x962d[16]);
                    var _0x77b9x39 = document[_0x962d[1]](_0x77b9x38);
                    var _0x77b9x3a = _0x77b9x39[_0x962d[47]](_0x962d[103]);
                    var _0x77b9x3b = _0x77b9x39[_0x962d[4]][_0x962d[17]](_0x962d[104]);
                    var _0x77b9x3c = _0x77b9x39[_0x962d[4]][_0x962d[17]](_0x962d[105]);
                    var _0x77b9x3d = _0x77b9x39[_0x962d[4]][_0x962d[17]](_0x962d[106]);
                    var _0x77b9x3e = _0x77b9x39[_0x962d[4]][_0x962d[17]](_0x962d[107]);
                    var _0x77b9x3f = _0x77b9x39[_0x962d[108]];
                    var _0x77b9x40 = _0x77b9x39[_0x962d[109]];
                    var _0x77b9x41 = _0x77b9x39[_0x962d[47]](_0x962d[110]);
                    if (_0x77b9x41) {
                        setTimeout(function () {
                            document[_0x962d[1]](_0x77b9x38)[_0x962d[4]][_0x962d[18]](_0x962d[16]);
                            document[_0x962d[11]](_0x962d[10])[0][_0x962d[4]][_0x962d[18]](_0x962d[16]);
                        }, _0x77b9x41);
                    }
                    if (_0x77b9x3a === _0x962d[111]) {
                        var _0x77b9x3f = document[_0x962d[1]](_0x77b9x38)[_0x962d[47]](_0x962d[91]);
                        if (_0x77b9x3b) {
                            for (let _0x77b9xa = 0; _0x77b9xa < _0x77b9x36[_0x962d[12]]; _0x77b9xa++) {
                                _0x77b9x36[_0x77b9xa][_0x962d[20]][_0x962d[112]] = _0x962d[113] + _0x77b9x3f + _0x962d[114];
                            }
                        }
                        if (_0x77b9x3c) {
                            for (let _0x77b9xa = 0; _0x77b9xa < _0x77b9x36[_0x962d[12]]; _0x77b9xa++) {
                                _0x77b9x36[_0x77b9xa][_0x962d[20]][_0x962d[112]] = _0x962d[115] + _0x77b9x3f + _0x962d[114];
                            }
                        }
                        if (_0x77b9x3e) {
                            for (let _0x77b9xa = 0; _0x77b9xa < _0x77b9x36[_0x962d[12]]; _0x77b9xa++) {
                                _0x77b9x36[_0x77b9xa][_0x962d[20]][_0x962d[112]] = _0x962d[116] + _0x77b9x40 + _0x962d[114];
                            }
                        }
                        if (_0x77b9x3d) {
                            for (let _0x77b9xa = 0; _0x77b9xa < _0x77b9x36[_0x962d[12]]; _0x77b9xa++) {
                                _0x77b9x36[_0x77b9xa][_0x962d[20]][_0x962d[112]] = _0x962d[117] + _0x77b9x40 + _0x962d[114];
                            }
                        }
                    }
                    if (_0x77b9x3a === _0x962d[118]) {
                        var _0x77b9x3f = document[_0x962d[1]](_0x77b9x38)[_0x962d[47]](_0x962d[91]);
                        if (_0x77b9x3b) {
                            for (let _0x77b9xa = 0; _0x77b9xa < _0x77b9x36[_0x962d[12]]; _0x77b9xa++) {
                                _0x77b9x36[_0x77b9xa][_0x962d[20]][_0x962d[112]] = _0x962d[113] + _0x77b9x3f / 10 + _0x962d[114];
                            }
                        }
                        if (_0x77b9x3c) {
                            for (let _0x77b9xa = 0; _0x77b9xa < _0x77b9x36[_0x962d[12]]; _0x77b9xa++) {
                                _0x77b9x36[_0x77b9xa][_0x962d[20]][_0x962d[112]] = _0x962d[115] + _0x77b9x3f / 10 + _0x962d[114];
                            }
                        }
                        if (_0x77b9x3e) {
                            for (let _0x77b9xa = 0; _0x77b9xa < _0x77b9x36[_0x962d[12]]; _0x77b9xa++) {
                                _0x77b9x36[_0x77b9xa][_0x962d[20]][_0x962d[112]] = _0x962d[116] + _0x77b9x40 / 5 + _0x962d[114];
                            }
                        }
                        if (_0x77b9x3d) {
                            for (let _0x77b9xa = 0; _0x77b9xa < _0x77b9x36[_0x962d[12]]; _0x77b9xa++) {
                                _0x77b9x36[_0x77b9xa][_0x962d[20]][_0x962d[112]] = _0x962d[117] + _0x77b9x40 / 5 + _0x962d[114];
                            }
                        }
                    }
                });
            });
            const _0x77b9x42 = document[_0x962d[24]](_0x962d[119]);
            _0x77b9x42[_0x962d[22]]((_0x77b9xc) => {
                return _0x77b9xc[_0x962d[37]](_0x962d[64], (_0x77b9xb) => {
                    const _0x77b9x37 = document[_0x962d[24]](_0x962d[101]);
                    for (let _0x77b9xa = 0; _0x77b9xa < _0x77b9x37[_0x962d[12]]; _0x77b9xa++) {
                        _0x77b9x37[_0x77b9xa][_0x962d[4]][_0x962d[18]](_0x962d[16]);
                    }
                    for (let _0x77b9xa = 0; _0x77b9xa < _0x77b9x36[_0x962d[12]]; _0x77b9xa++) {
                        _0x77b9x36[_0x77b9xa][_0x962d[20]][_0x962d[112]] = _0x962d[115] + 0 + _0x962d[114];
                    }
                });
            });
            /*var _0x77b9x19 = document[_0x962d[24]](_0x962d[31]);
            _0x77b9x19[_0x962d[22]]((_0x77b9xc) => {
                return _0x77b9xc[_0x962d[37]](_0x962d[32], (_0x77b9xb) => {
                    if (!_0x77b9xc[_0x962d[33]] == _0x962d[34]) {
                        _0x77b9xc[_0x962d[28]][_0x962d[4]][_0x962d[3]](_0x962d[35]);
                        _0x77b9xc[_0x962d[28]][_0x962d[36]](_0x962d[30])[_0x962d[4]][_0x962d[3]](_0x962d[26]);
                    } else {
                        _0x77b9xc[_0x962d[28]][_0x962d[24]](_0x962d[27])[0][_0x962d[4]][_0x962d[3]](_0x962d[26]);
                        _0x77b9xc[_0x962d[28]][_0x962d[24]](_0x962d[29])[0][_0x962d[4]][_0x962d[3]](_0x962d[26]);
                        _0x77b9xc[_0x962d[28]][_0x962d[4]][_0x962d[18]](_0x962d[35]);
                        _0x77b9xc[_0x962d[28]][_0x962d[36]](_0x962d[30])[_0x962d[4]][_0x962d[18]](_0x962d[26]);
                    }
                });
            });*/
            var _0x77b9x1a = document[_0x962d[24]](_0x962d[38]);
            _0x77b9x1a[_0x962d[22]]((_0x77b9xc) => {
                return _0x77b9xc[_0x962d[37]](_0x962d[32], (_0x77b9xb) => {
                    if (!_0x77b9xc[_0x962d[33]] == _0x962d[34]) {
                        _0x77b9xc[_0x962d[28]][_0x962d[4]][_0x962d[3]](_0x962d[35]);
                        _0x77b9xc[_0x962d[28]][_0x962d[36]](_0x962d[30])[_0x962d[4]][_0x962d[3]](_0x962d[26]);
                    } else {
                        _0x77b9xc[_0x962d[28]][_0x962d[4]][_0x962d[18]](_0x962d[35]);
                        _0x77b9xc[_0x962d[28]][_0x962d[36]](_0x962d[30])[_0x962d[4]][_0x962d[18]](_0x962d[26]);
                    }
                });
            });
            var _0x77b9x1b = document[_0x962d[24]](_0x962d[39]);
            _0x77b9x1b[_0x962d[22]]((_0x77b9xc) => {
                return _0x77b9xc[_0x962d[37]](_0x962d[40], (_0x77b9xb) => {
                    if (_0x77b9xc[_0x962d[33]] !== _0x962d[41]) {
                        _0x77b9xc[_0x962d[28]][_0x962d[4]][_0x962d[3]](_0x962d[35]);
                        _0x77b9xc[_0x962d[28]][_0x962d[24]](_0x962d[27])[0][_0x962d[4]][_0x962d[18]](_0x962d[26]);
                        _0x77b9xc[_0x962d[28]][_0x962d[24]](_0x962d[42])[0][_0x962d[4]][_0x962d[3]](_0x962d[26]);
                    }
                    if (_0x77b9xc[_0x962d[33]] == _0x962d[41]) {
                        _0x77b9xc[_0x962d[28]][_0x962d[24]](_0x962d[43])[0][_0x962d[4]][_0x962d[3]](_0x962d[26]);
                        _0x77b9xc[_0x962d[28]][_0x962d[24]](_0x962d[29])[0][_0x962d[4]][_0x962d[18]](_0x962d[26]);
                        _0x77b9xc[_0x962d[28]][_0x962d[4]][_0x962d[3]](_0x962d[35]);
                    }
                });
            });
            //MENU
            console.log('End.')

            ///////////////////////////////////////////////////////////
            $( document ).ready(function() {
                $('.results.active').css('color', 'red!important')
                $('.leaflet-control-geosearch').css('background-color', 'red!important')
                $('.leaflet-left .leaflet-control').css('background-color', 'red!important')
            });
            this.$store.dispatch('scrollToTop')
        },
        watch: {
        },
        methods: {
            onMapClick(e) {
                console.log("You clicked the map at " + e.latlng);
            },
            go_to(link){
                if (this.business_profile===null || this.business_profile.id==undefined) {
                    this.$show_modal.show_modal({id: 'error', title: "Error", message: "Save business profile to continue", btn_text: 'OK'})
                    return
                }
                this.$router.push(link)
            },
            save(){
                if (this.business_profile && (this.business_profile.name==null || this.business_profile.name==undefined || this.business_profile.name=='')) {
                    this.$show_modal.show_modal({id: 'error', title: "Error", message: "Set business profile name to continue", btn_text: 'OK'})
                    return 
                }
                if (this.business_profile && (this.business_profile.business_types_id==null || this.business_profile.business_types_id==undefined)) {
                    this.$show_modal.show_modal({id: 'error', title: "Error", message: "Select business profile type to continue", btn_text: 'OK'})
                    return 
                }
                if (this.business_profile && (this.business_profile.latitude==null || this.business_profile.latitude==undefined || this.business_profile.latitude=='')) {
                    this.$show_modal.show_modal({id: 'error', title: "Error", message: "Select business profile map point to continue", btn_text: 'OK'})
                    return 
                }
                if (this.business_profile && (this.business_profile.longitude==null || this.business_profile.longitude==undefined || this.business_profile.longitude=='')) {
                    this.$show_modal.show_modal({id: 'error', title: "Error", message: "Select business profile map point to continue", btn_text: 'OK'})
                    return 
                }
                if (this.business_profile && (this.business_profile.location==null || this.business_profile.location==undefined || this.business_profile.location=='')) {
                    this.$show_modal.show_modal({id: 'error', title: "Error", message: "Set business profile location to continue", btn_text: 'OK'})
                    return 
                }
                this.saving = true;
                console.log('this.business_profile', this.business_profile)
                this.business_profile.pi_users_id = this.user.id;
                axios.post('/api/v1/business-profiles', this.business_profile, {
                    headers: {
                        "Content-type": "application/json"
                    }
                })
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
