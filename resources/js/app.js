import './bootstrap';

import {createApp} from 'vue'

import App from './App.vue'

import store from './store';

import 'leaflet/dist/leaflet.css';
import { LMap, LTileLayer, LMarker, LPopup } from 'vue3-leaflet';

import router from './router'
import { createRouter, createWebHistory, createWebHashHistory } from 'vue-router'

router.beforeEach((to, from, next) => {
  //Stop payment verifier when moving to another page
  store.dispatch('clearPaymentVerifier')
  next()
})
/*var router = createRouter({
  // 4. Provide the history implementation to use. We are using the hash history for simplicity here.
  history: createWebHistory(),
  routes, // short for `routes: routes`
})*/

import VueQrcode from '@chenfengyuan/vue-qrcode';

import i18n from './i18n'

///////////////////MANSORY
//import {VueMasonryPlugin} from 'vue-masonry';
//import VueSession from "vue-session";
import VueObserveVisibility from 'vue3-observe-visibility'

import Vue3ConfirmDialog from 'vue3-confirm-dialog';
import 'vue3-confirm-dialog/style';

///////////////////END MANSORY
import {show_modal} from './functions.js'
import {hide_modal} from './functions.js'
import {delivery_period} from './functions.js'
import {functions} from './functions.js'

const app = createApp(App)
// Make sure to _use_ the router instance to make the
// whole app router-aware.
app.use(router)
app.use(i18n)
app.use(store)
//Laflet map
.component('l-map', LMap)
.component('l-tile-layer', LTileLayer)
.component('l-marker', LMarker)
.component('l-popup', LPopup)
//app.use(VueMasonryPlugin)
//app.use(VueSession)
.component(VueQrcode.name, VueQrcode)
app.use(VueObserveVisibility)
app.use(Vue3ConfirmDialog);
app.component('vue3-confirm-dialog', Vue3ConfirmDialog.default)
///////////ADSENSE//////////////////
import ScriptX from 'vue-scriptx'
app.use(ScriptX)
import Ads from 'vue-google-adsense'
//app.use(Ads.AutoAdsense, { adClient: 'ca-pub-3962442438023665', isNewAdsCode: true })
app.use(Ads.Adsense)
app.use(Ads.InArticleAdsense)
app.use(Ads.InFeedAdsense)
///////////ADSENSE//////////////////
import VueCountdown from '@chenfengyuan/vue-countdown';
app.component(VueCountdown.name, VueCountdown);

import "vue-progressive-image/dist/style.css"; // <--- very important!
import VueProgressiveImage from 'vue-progressive-image'

app.use(VueProgressiveImage);

import vueCountryRegionSelect from 'vue3-country-region-select'
app.use(vueCountryRegionSelect);
app.component('vue-country-region-select', vueCountryRegionSelect.default)

app.config.globalProperties.$currency = "Test-π";
app.config.globalProperties.$currencyFunction = {
  setCurrency(value) {
    app.config.globalProperties.$currency = value;
  }
}

app.config.globalProperties.$functions = functions;
app.config.globalProperties.$show_modal = show_modal;
app.config.globalProperties.$hide_modal = hide_modal;
app.config.globalProperties.$delivery_period = delivery_period;

app.directive('observe-visibility', VueObserveVisibility.ObserveVisibility)

app.directive('can', (el, binding, vnode) => {
  var permissions = store.state.permissions;
  if(permissions.includes(binding.value))
  {
    return vnode.el.hidden = false;
  }else{           
    return vnode.el.hidden = true;
  }
})

import moment from 'moment';
app.config.globalProperties.$filters = {
  formatDate(value, locale = 'en') {
    if (locale == 'fr' || locale == 'es') {
        return moment(String(value)).format('DD/MM/YYYY HH:mm:ss');
    }
    return moment(String(value)).format('MM/DD/YYYY hh:mm:ss a');
  }
}

import axios from 'axios'
axios.interceptors.request.use(request => {
  store.state.maintenance_mode = false
  console.log('this.$store.state.token', store.state.token)
    request.headers.Authorization = `Bearer ${ store.state.token }`;
    request.headers.Accept = `application/json`;
    request.headers.useruid = store.state.user?store.state.user.uid:'';
    return request;
});

axios.interceptors.response.use(
  response => {
    //Update user data
    if (response.data.user!=undefined && response.data.user!=null) {
      console.log('responseresponse response', response.data.user, store.state.user)
      if (store.state.user && store.state.user.id == response.data.user.id) {
        store.dispatch('update_user', response.data.user)
      }
      //When logging in, store.state.user is null
      if (store.state.user == null) {
        store.dispatch('update_user', response.data.user)
      }
    }
    if (response.data.agreements!=undefined && response.data.agreements!=null) {
      //console.log('responseresponse response', response.data)
      store.dispatch('set_agreements', response.data.agreements)
    }
    if (response.data.reasons!=undefined && response.data.reasons!=null) {
      store.dispatch('set_reasons', response.data.reasons)
    }
    if (response.data.languages!=undefined && response.data.languages!=null) {
      store.dispatch('set_languages', response.data.languages)
    }
    return response;
  }, 
  error => {
    store.state.isOpenLoading = false
    if (error.response && (error.response.status === 422 || error.response.status === 401 || error.response.status === 403)) {
        //this.$swal('Erreur', "Votre session à expiré, veuillez vous reconnecter", "error")
        //window.location.href = '/auth/login';
        let img = '/site_images/error.png'
        console.log('reject auth')
        if (error.response.data.status === 'deleted') {
          console.log('deleted')
          let msg = i18n.global.t('message.account_deleted')
          msg = '<br><img src="'+img+'"><br><strong style="font-size:20px;">'+msg+'<strong>'
          store.state.confDialog(
              {
                title: i18n.global.t('message.info'),
                message: msg,
                button: {
                  yes: 'OK',
                },
                html: true,
                  callback: confirm => {
                      if (confirm) {
                          //this.$router.push('connexion')
                      }
                      store.dispatch('erasing')
                      this.$router.push('/')
                  }
              }
          )
        }else if (error.response.data.status === 'deactivated') {
          console.log('deactivated')
          let msg = i18n.global.t('message.account_deactivated')
          msg = '<br><img src="'+img+'"><br><strong style="font-size:20px;">'+msg+'<strong>'
          store.state.confDialog(
              {
                title: i18n.global.t('message.info'),
                message: msg,
                button: {
                  yes: 'OK',
                },
                html: true,
                  callback: confirm => {
                      if (confirm) {
                          //this.$router.push('connexion')
                      }
                      store.dispatch('erasing')
                      this.$router.push('/')
                  }
              }
          )
        }else if (error.response.data.status === 'maintenance_mode'){
          console.log('reject auth maintenance_mode')
          store.state.maintenance_mode = true
          //router.push('/maintenance')
          let msg = i18n.global.t('message.maintenance_mode')
          msg = '<br><img src="'+img+'"><br><strong style="font-size:20px;">'+msg+'<strong>'
          store.state.confDialog(
              {
                title: i18n.global.t('message.info'),
                message: msg,
                button: {
                  yes: 'OK',
                },
                html: true,
                  callback: confirm => {
                      if (confirm) {
                          //this.$router.push('connexion')
                      }
                      router.push('/maintenance')
                  }
              }
          )
        }else if (error.response.data.status === 'purchase_deactivated'){
          console.log('reject auth maintenance_mode')
          store.state.maintenance_mode = true
          //router.push('/maintenance')
          let msg = i18n.global.t('message.purchase_deactivated')
          msg = '<br><img src="'+img+'"><br><strong style="font-size:20px;">'+msg+'<strong>'
          store.state.confDialog(
              {
                title: i18n.global.t('message.info'),
                message: msg,
                button: {
                  yes: 'OK',
                },
                html: true,
                  callback: confirm => {
                      if (confirm) {
                          //this.$router.push('connexion')
                      }
                  }
              }
          )
        }else if (error.response.status === 403){
          //console.log('Forbidden', error.response)
          //router.go(-1)
          //store.state.maintenance_mode = true
          let msg = i18n.global.t('message.access_denied')
          /*if (error.response.data.status=="deleted") {
            msg = i18n.global.t('message.deleted')
          }
          if (error.response.data.status=="deactivated") {
            msg = i18n.global.t('message.deactivated')
          }*/
          
          msg = '<br><img src="'+img+'"><br><strong style="font-size:20px;">'+msg+'<strong>'
          store.state.confDialog(
              {
                title: i18n.global.t('message.info'),
                message: msg,
                button: {
                  yes: 'OK',
                },
                html: true,
                  callback: confirm => {
                      router.go(-1)
                  }
              }
          )
        }else{
          let msg = i18n.global.t('message.session_expired')
          msg = '<br><img src="'+img+'"><br><strong style="font-size:20px;">'+msg+'<strong>'
          store.state.confDialog(
              {
                title: i18n.global.t('message.info'),
                message: msg,
                button: {
                  yes: 'OK',
                },
                html: true,
                  callback: confirm => {
                      //router.push('/')
                      console.log('here erasing')
                      store.dispatch('erasing')
                      store.state.connecting = true
                      store.dispatch('signInPiNetwork', {
                      })
                      
                  }
              }
          )
        }
    }
    return Promise.reject(error);
  }
);

app.mount('#app')