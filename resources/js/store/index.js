import { createStore } from "vuex";
import Vue3ConfirmDialog from 'vue3-confirm-dialog';
import i18n from '../i18n'
import {functions} from '../functions'
import {show_modal} from '../functions'
import axios from 'axios'

//axios.defaults.baseURL = 'http://localhost:8000/'
//axios.defaults.baseURL = 'https://lara10.piketplace.com/'
axios.defaults.headers.post["Content-Type"] = "application/json";
axios.defaults.headers.post["Accept"] = "application/json";
axios.defaults.headers.post["Access-Control-Allow-Origin"] = "*";
axios.defaults.headers.post["Access-Control-Allow-Methods"] = "GET, PUT, POST, DELETE, OPTIONS";

//var { t } = i18n.global

import VuexPersistence from 'vuex-persist'
const vuexLocal = new VuexPersistence({
  storage: window.localStorage,
  supportCircular: true
})

export default createStore({
    plugins: [vuexLocal.plugin],
    state:{
        test: "test",
        title: "",
        isLoading: false,
        maintenance_mode: false,
        error: "",
        isOpenLoading: true,
        isLoggedIn: false,
        connecting: false,
        disconnecting: false,
        saving: false,
        deleting: false,
        isPiBrowser: false,
        user: null,
        email: '',
        locale: '',
        token: '',
        prevRoute: null,
        products: [],
        categories: [],
        cart: null,
        flag: '',
        confDialog: null,
        permissions: [
            //'browse users',
        ],
        verifierPayment: null,
        payment_from: '',
        uniqueId: null,
        product: null,
        product_id: 0,
        uploadDone: true,
        selected_category: null,
        line_order: null,
        countries_db: [],
        country_cities: [],
        languages: [],
        agreements: [],
        reasons: [],
        seller_agreements: [
            "Piketplace does not participate in pricing and will not interfere with sellers' pricing. The amount of pi to be exchanged for the goods is solely set by sellers.",
            "Please note that the transaction fee for the pi wallet is 0.01π. When seller withdraws π from PCM, his PCM wallet balance must be greater than 0.01π.",
            "The π earned from selling goods cannot be exchanged to fiat during the enclosed mainnet. Sellers should plan carefully according to their financial affordability. After the open mainnet, the Pi price may not be the seller's current barter price. PCM users need to be aware that cryptocurrency prices are volatile and should bear their own risks.",
            "Please upload the stock quantity and description correctly. If sellers cannot deliver the goods accroding to the submitted item quantity and description, his PCM account will be warned or blacklisted.",
            "Piketplace will strictly follow the rules from PCT. We only accept Pi, but not fiat currency. We will not provide any channels for buyers or sellers to exchange pi into fiat currency.",
            "Piketplace allow the exchange of idle items between pioneers.",
            "Piketplace will highlight some products from time to time for promotion in the Piketplace official Twitter and community.",
            "Piketplace will provide users with 7/24 customer service. If you encounter bugs or want to make suggestions when using the app, you can communicate with the customer service directly.",
        ],
        show_welcome: true,
        show_country: false,
        purchase_activation: true,
        mining_activation: true,
        pibrowser_verification: true,
        http_request_status: '',//'' at beginning,'success' and 'error'
        transfer_fee_pi_network: 0,
        transfer_fee_piket: 0,
        transfer_fee_piket_activation: false,
        productBuyNow: null,
        at_least_one_product_qty_insufficient: false,
        paid: false,
        delivery_penalties_limit: 0,



        userLocation: null,
        //center: [6.171446325386116, 1.2958717346191408],//Lomé
        center: [44.402391829094, -122.607421875],//Pi core team
        business_types: [],
        business_profile: {
            name: "",
            pi_users_id: null,
            business_types_id: null,
            business_type: null,
            business_profile_photos: null,
            location: "",
            latitude: "",
            longitude: "",
            menu_status: true,
            orders_status: false,
            payments_status: true,
            loyalty_card_status: false,
            menu: [
                {
                    name: "",
                    description: "",
                    price: 0.0,
                    image: "",
                },
            ],
            loyalty_card: {

            }
        },
        shopping: {
            business_profile: null,
            shopping_cart: []
        }
    },
    getters:{
        products: state => {
            return state.products
        },
        isLoading: state => {
            return state.isLoading
        },
        user: state => {
            return state.user
        },
    },
    mutations:{
        deleteAttribute(state, attributeName) {
            // Use JavaScript's delete operator to remove the attribute
            //if (state[attributeName]!=undefined && state[attributeName]!=null) {
                delete state[attributeName];
            //}
        },
        SET_PRODUCT(state, products){
            state.products.push(...products);
        },
        SET_CATEGORIES(state, categories){
            state.categories = categories;
        },
        SET_LOADING(state, status){
            state.isLoading = status;
        },
        SET_OPEN_LOADING(state, status){
            state.isOpenLoading = status;
        },
        SET_LOGGED_IN(state, status){
            state.isLoggedIn = status;
        },
        SET_CONNECTING(state, status){
            state.connecting = status;
        },
        SET_TOKEN(state, token){
            state.token = token;
            axios.defaults.headers.common['Authorization'] = token;
        },
        SET_USER(state, user){
            state.user = user;
        },
        SET_BUSINESS_PROFILE(state, profile){
            console.log('profile', profile)
            if (profile !== null) {
                state.business_profile = profile;
            }
        },
        SET_ERROR(state, status){
            state.error = status;
        },
        SET_LOCALE(state, locale){
            state.locale = locale;
            //i18n.global.locale = 'cn'
            i18n.global.locale = locale
        },
        SET_CART(state, cart){
            state.cart = cart;
        },
        SET_PERMISSIONS(state, permissions){
            let perm = permissions!=null?permissions:[]
            state.permissions = perm;
        },
        SET_CONF_DIALOG(state, val){
            state.confDialog = val;
        },
        SET_UNIQUE_ID(state, val){
            state.uniqueId = val;
        },
        SET_IS_PI_BROWSER(state, val){
            state.isPiBrowser = val;
        },
        SET_COUNTRIES(state, val){
            state.countries_db = val;
        },
        SET_LANGUAGES(state, val){
            state.languages = val;
        },
        SET_WELCOME(state, val){
            state.show_welcome = val;
        },
        SET_AGREEMENTS(state, val){
            state.agreements = val;
        },
        SET_REASONS(state, val){
            state.reasons = val;
        },
        SET_PURCHASE_ACTIVATION(state, val){
            state.purchase_activation = val;
        },
        SET_MINING_ACTIVATION(state, val){
            state.mining_activation = val;
        },
        SET_DELIVERY_PENALTIES_LIMIT(state, val){
            state.delivery_penalties_limit = val;
        },
        SET_PIBROWSER_VERIFICATION(state, val){
            state.pibrowser_verification = val;
        },
        SET_TRANSFER_FEE_PINETWORK(state, val){
            state.transfer_fee_pi_network = val;
        },
        SET_TRANSFER_FEE_PIKET(state, val){
            state.transfer_fee_piket = val;
        },
        SET_TRANSFER_FEE_PIKET_ACTIVATION(state, val){
            state.transfer_fee_piket_activation = val;
        },
        SET_EMAIL(state, val){
            state.email = val;
        },
        SET_BUSINESS_TYPES(state, val){
            state.business_types = val;
        },
        SET_USER_LOCATION(state, val){
            state.userLocation = val;
        },
        SET_CENTER_LOCATION(state, val){
            state.center = val;
        },
        CLEAR_PAYMENT_VERIFIER(state) {
            // If interval exists, clear it
            if (state.verifierPayment) {
                clearInterval(state.verifierPayment)
            }
        },
        SET_PAYMENT_VERIFIER(state, verifierPayment) {
            // If interval exists, clear it
            if (state.verifierPayment) {
                clearInterval(state.verifierPayment)
            }
            // Set verifierPayment
            state.verifierPayment = verifierPayment
        }
    },
    actions:{
        getProducts({commit}, data){
            var queryString = Object.keys(data).map(key => key + '=' + data[key]).join('&');
            //console.log('zzzzzzzz data', queryString)
            axios.get('/api/v1/products?'+queryString)
            .then(res => {
                //console.log('zzzzzzzz', res.data.products)
                commit('SET_USER', res.data.user)
                commit('SET_LOADING', false)
                commit('SET_PRODUCT', res.data.products)
            })
        },
        signOut({commit, state}, self) {
            //commit('SET_OPEN_LOADING', true)
            state.disconnecting = true
            //console.log('this.state.token', this.state.token)
            axios.post('/api/v1/signout')
            .then(res => {
                //commit('SET_OPEN_LOADING', false)
                state.isLoading = false
                state.disconnecting = false
                if (res.status==200 || res.data.status=='success') {
                    //this.dispatch('erasing')
                    commit('SET_LOGGED_IN', false)
                    //commit('SET_LOADING', false)
                    commit('SET_USER', null)
                    commit('SET_TOKEN', '')
                    commit('SET_PERMISSIONS', [])
                    commit('SET_EMAIL', '')
                    commit('SET_UNIQUE_ID', '')
                    commit('CLEAR_PAYMENT_VERIFIER')
                    //this.state.isOpenLoading = false

                    self.$functions.msg_box(self, 'success', i18n.global.t('message.deconnection'), i18n.global.t('message.logout_successfull'))
                }
            })
            .catch(error => {
                //commit('SET_LOADING', false)
                state.isLoading = false
                state.disconnecting = false
                commit('SET_LOGGED_IN', false)
                //commit('SET_LOADING', false)
                commit('SET_USER', null)
                commit('SET_TOKEN', '')
                commit('SET_PERMISSIONS', [])
                commit('SET_EMAIL', '')
                commit('SET_UNIQUE_ID', '')
                commit('CLEAR_PAYMENT_VERIFIER')
                //this.isLoading = false
                //console.log(error);
                if (error.response.status != 403) {
                    self.$functions.msg_box(self, 'error', i18n.global.t('message.deconnection'), i18n.global.t('message.an_error_occured'))
                }
            });
        },
        erasing({commit, state}){
            commit('SET_LOGGED_IN', false)
            //commit('SET_LOADING', false)
            //commit('SET_OPEN_LOADING', false)
            commit('SET_USER', null)
            commit('SET_CART', [])
            commit('SET_TOKEN', '')
            state.saving = false
            state.deleting = false
            state.connecting = false
            state.disconnecting = false
            commit('SET_UNIQUE_ID', '')
            commit('CLEAR_PAYMENT_VERIFIER')

        },
        initParam({commit, state}, confirm_func){
            if (navigator.userAgent.toLowerCase().includes('pibrowser')) {
                //console.log('is Pi browser')
                //alert('No pi browser')
                state.isPiBrowser = true
            }
            commit('SET_IS_PI_BROWSER', state.isPiBrowser)
            state.connecting = false
            state.disconnecting = false
            state.saving = false
            state.deleting = false
        },
        setSettings({commit, state}, data){
            if (data.languages) {
                commit('SET_LANGUAGES', data.languages)
            }
            if (data.agreements!==null) {
                commit('SET_AGREEMENTS', data.agreements)
            }
            if (data.purchase_activation!==null) {
                commit('SET_PURCHASE_ACTIVATION', data.purchase_activation)
            }
            if (data.mining_activation!==null) {
                commit('SET_MINING_ACTIVATION', data.mining_activation)
            }
            if (data.pibrowser_verification!==null) {
                state.isPiBrowser = false
                if (navigator.userAgent.toLowerCase().includes('pibrowser')) {
                    state.isPiBrowser = true
                }
                commit('SET_IS_PI_BROWSER', state.isPiBrowser)
                commit('SET_PIBROWSER_VERIFICATION', data.pibrowser_verification)
                if (data.pibrowser_verification===true && state.isPiBrowser===false) {
                    commit('SET_IS_PI_BROWSER', false)
                }else{
                    commit('SET_IS_PI_BROWSER', true)
                }
            }
            if (data.transfer_fee_pi_network!==null) {
                commit('SET_TRANSFER_FEE_PINETWORK', data.transfer_fee_pi_network)
            }
            if (data.transfer_fee_piket!==null) {
                commit('SET_TRANSFER_FEE_PIKET', data.transfer_fee_piket)
            }
            if (data.transfer_fee_piket_activation!==null) {
                commit('SET_TRANSFER_FEE_PIKET_ACTIVATION', data.transfer_fee_piket_activation)
            }
            if (data.business_types!==null) {
                commit('SET_BUSINESS_TYPES', data.business_types)
            }
        },
        setConfDialog({commit}, confirm_func){
            commit('SET_CONF_DIALOG', confirm_func)
        },
        setLang({commit, state}, locale){
            commit('SET_LOCALE', locale)
        },
        set_line_order({commit, state}, line_order){
            //commit('SET_LOCALE', line_order)
            state.line_order = line_order
        },
        set_countries({commit, state}, countries){
            commit('SET_COUNTRIES', countries)
        },
        set_languages({commit, state}, languages){
            //commit('SET_LANGUAGES', languages)
            state.languages = languages
        },
        set_agreements({commit, state}, agreements){
            commit('SET_AGREEMENTS', agreements)
        },
        set_reasons({commit, state}, reasons){
            commit('SET_REASONS', reasons)
        },
        set_purchase_activation({commit, state}, val){
            commit('SET_PURCHASE_ACTIVATION', val)
        },
        set_mining_activation({commit, state}, val){
            commit('SET_MINING_ACTIVATION', val)
        },
        set_pibrowser_verification({commit, state}, val){
            console.log('set_pibrowser_verification', val)
            commit('SET_PIBROWSER_VERIFICATION', val)
        },
        set_transfer_fee_pi_network({commit, state}, val){
            commit('SET_TRANSFER_FEE_PINETWORK', val)
        },
        set_transfer_fee_piket({commit, state}, val){
            commit('SET_TRANSFER_FEE_PIKET', val)
        },
        disable_welcome({commit, state}){
            state.show_welcome = false
        },
        clearPaymentVerifier({commit, state}){
            commit('CLEAR_PAYMENT_VERIFIER')
        },
        async signInPiNetwork({commit, state, dispatch }, data) {
            let isLoggedIn = data.isLoggedIn
            state.error = ""
            if (isLoggedIn == undefined || !isLoggedIn) {//If not logged in, we logged him in in piketplace
                state.connecting = true
            }
            console.log('in signInPiNetwork')
            //const scopes = ["username", "payments", "wallet_address", "preferred_language", "roles"];
            const scopes = ["username", "payments", "wallet_address", "preferred_language"];
            const onIncompletePaymentFound = (payment) =>{
                //console.log('signin onIncompletePaymentFound', payment)
                let txid = payment.transaction.txid;
                let txUrl = payment.transaction._link;
                let paymentId = payment.identifier;
                let data = {
                    paymentId:paymentId,
                    txid:txid,
                }
                dispatch('executePaymentCompletion', data)
                //We're not allowed to cancel a payment after approve
                //this.dispatch('cancelPayment', data)
            };

            Pi.authenticate(scopes, onIncompletePaymentFound).then(function(auth) {
                //console.log(auth.user.username);
                //alert('in sign pi '+auth.user.username)
                /* if the user is already logged in we reload
                 his pi network connection session
                 */
                 console.log('in signInPiNetwork Pi.authenticate')
                if (isLoggedIn == undefined || !isLoggedIn) {//If not logged in, we logged him in in piketplace
                    console.log('in signInPiNetwork, not isLoggedIn')
                    let dd = {
                        authResponse: auth,
                        isLoggedIn: isLoggedIn,
                    }
                    dispatch('signInPiketplace', dd);//Piketplace login
                }else{
                    state.connecting = false
                    console.log('in signInPiNetwork, isLoggedIn')
                }
                
            }).catch(function(error) {
                //alert('in sign pi error')
                state.connecting = false
                state.isLoading = false
                state.isOpenLoading = false
                state.error = i18n.global.t('message.an_error_occured')
                //console.log('ht guyt_g', error);
            });
            
            //const authResponse = await Pi.authenticate(scopes, onIncompletePaymentFound);
            //console.log('authResponse', authResponse);
            /* pass obtained data to backend */
            /*await this.dispatch('signInPiketplace', {authResponse: authResponse,
                confirm_func: confirm_func});*/

            /* use the obtained data however you want */
            //setUser(authResponse.user);
            //console.log(authResponse.user);
        },
        async signInPiketplace({commit, state}, data) {
            console.log('in signInPiketplace')
            //state.isLoading = true
            //state.isOpenLoading = true
            let authResult = data.authResponse
            let referred_by = document.getElementById('referred_by')
            if (referred_by) {
                authResult.referred_by = referred_by.value
            }
            //alert(authResult.referred_by)
            const config = {headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Access-Control-Allow-Origin': '*',
                'Access-Control-Allow-Methods': 'GET, PUT, POST, DELETE, OPTIONS',
            }};
            await axios.post(`/api/v1/signin`, { authResult }, config)
            .then(resp => {
                //console.log("signin", resp.data);
                state.isLoading = false
                state.isOpenLoading = false
                state.connecting = false
                if (resp.data.status == 'success') {
                    let user = resp.data.user
                    let cart = resp.data.data_cart.cart
                    let state = resp.data.state
                    user.state = state
                    //commit('SET_USER', user)
                    console.log('new resp.data.token', resp.data.token)
                    commit('SET_CART', cart)
                    commit('SET_LOCALE', user.locale?user.locale:'en')
                    commit('SET_TOKEN', resp.data.token)
                    commit('SET_PERMISSIONS', user.permissions?user.permissions:[])
                    commit('SET_PURCHASE_ACTIVATION', resp.data.purchase_activation)
                    commit('SET_MINING_ACTIVATION', resp.data.mining_activation)
                    commit('SET_AGREEMENTS', resp.data.agreements)
                    commit('SET_DELIVERY_PENALTIES_LIMIT', resp.data.delivery_penalties_limit)
                    //let message_login = 'message.you_still_logged_in'
                    let message_login = 'message.login_successfull'
                    commit('SET_LOGGED_IN', true)
                    message_login = 'message.login_successfull'

                    show_modal.show_modal({id: 'success', title: "Success", message: "Session refreshed successfully", btn_text: 'OK'})

                    /*let nb = this.user.cart?this.user.cart.length:0
                    this.$refs.header.setData({nb:nb, nb_notif:this.user.nbNotification, title: 'message.log_in', route: this.prevRoute});*/
                }else{
                    //this.isLoading = false
                }
            })
            .catch(error => {
                //alert('in signInPiketplace error '+JSON.stringify(error))
                state.isLoading = false
                state.isOpenLoading = false
                state.connecting = false
                state.error = i18n.global.t('message.an_error_occured')
                let msg = i18n.global.t('message.an_error_occured')
                //this.isLoading = false
                console.log(error);
                if (error.response.data.status == 'deleted') {
                    msg = i18n.global.t('message.deleted')
                }
                if (error.response.data.status == 'deactivated') {
                    msg = i18n.global.t('message.account_deactivated')
                }
                show_modal.show_modal({id: 'error', title: "Error", message: msg, btn_text: 'OK'})
            });

            //console.log('authResult', authResult)
            //return setShowModal(false);
        },
        update_user({commit}, user) {
            commit('SET_USER', user)
            //commit('SET_BUSINESS_PROFILE', user.business_profile)
        },
        setUniqueId({commit}, uniqueId) {
            commit('SET_UNIQUE_ID', uniqueId)
        },
        showMenu(){
            let el = document.getElementById('menu-main')
            if (el!=undefined) {
                el.classList.add('menu-active');
                document.getElementsByClassName('menu-hider')[0].classList.add('menu-active');
            }
        },
        scrollToTop() {
            setTimeout(function () {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'  // 👈
                })
            }, 50)
        },
        getUserLocation({commit, state}) {
          if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(position => {
              let userLocation = [position.coords.latitude, position.coords.longitude];
              commit('SET_USER_LOCATION', userLocation)
              //commit('SET_CENTER_LOCATION', userLocation)
              //state.center = userLocation
              console.log('this.userLocation', position, userLocation)
            }, error => {
              console.error("Error getting user location:", error);
            });
            console.log('this.userLocation out')
          } else {
            console.error("Geolocation is not supported by this browser.");
          }
        },
        showMessage({commit}, payload) {
            let message = payload.message
            let confirm_func = payload.confirm_func
            confirm_func(
                {
                  title: 'Info',
                  message: message,
                  button: {
                    yes: 'OK'
                  },
                  html: true
                    ,
                    /**
                    * Callback Function
                    * @param {Boolean} confirm
                    */
                    callback: confirm => {
                      if (confirm) {
                      }
                    }
                }
            )
        },
        async getCities({commit, state}, data) {
            let country_code = data.code
            let self = data.self
            //let locale = this.user.locale;
            await axios.get('/api/v1/get-cities-by-country/'+country_code)
                .then(res => {
                    state.country_cities = res.data.cities
                    state.country_cities.forEach((val, index) => {
                        val.sortname = functions.removeAccentCharacter(val.name.toLowerCase())
                    })
                })
                .catch(error => {
                    functions.msg_box(self, 'error', i18n.global.t('message.info'), i18n.global.t('message.error_loading_cities'))
                });
        },
        payment({commit, state}, data){
            //alert(data.id);
            //alert(data.userId);
            //console.log('payment data', data);
            
            const scopes = ['username', 'payments'];
            const axiosClient = axios.create({
                //baseURL: 'http://localhost:8000',
                timeout: 20000,
                headers: {
                   Authorization: 'Bearer ' + state.token, //the token is a variable which holds the token
                   useruid: state.user.uid
                }
            });
            //const axiosClient = axios.create({baseURL: 'https://piketplace.com', timeout: 20000});
            const config = {headers: {'Content-Type': 'application/json', 'Content-Type': 'application/json', 'Access-Control-Allow_Origin': '*'}};

            let self = this
            const onIncompletePaymentFound = (payment) =>{
                //console.log('onIncompletePaymentFound jn', payment);
                let txid = payment.transaction.txid;
                let paymentId = payment.identifier;
                let data = {
                    paymentId:paymentId,
                    txid:txid,
                }
                self.dispatch('executePaymentCompletion', data)
            };

            ///////////////////////////////////////////////////////////////////////////////
            const onReadyForServerApproval = (paymentId) =>{
                //console.log('onReadyForServerApproval', paymentId);
                let res = axiosClient.post("/api/v1/approve", { paymentId:paymentId }, config);
                //console.log('approve res', res);
                return res;
            };
            const onReadyForServerCompletion = (paymentId, txid) =>{
                //console.log('onReadyForServerCompletion', paymentId, txid);
                return axiosClient.post('/api/v1/complete', { paymentId:paymentId, txid:txid }, config);
            };
            const onCancel = (paymentId) =>{
                //commit('CLEAR_PAYMENT_VERIFIER')
                //console.log('onCancel', paymentId);
                //return axiosClient.get('/cancel', { paymentId:paymentId }, config);
            };
            const onError = (error, payment) =>{
                //console.log('onError', error);
                if (payment) {
                    //console.log(payment);
                }
            };

            /*Pi.authenticate(['payments'], onIncompletePaymentFound).then(function(auth) {
                console.log(auth.user.username);
                
            }).catch(function(error) {
              console.error(error);
            });*/

            Pi.createPayment({
              amount: data.amount,
              memo: data.memo, // e.g: "Digital kitten #1234",
              //metadata: { orderId: data.orderId, userId: data.userId, type: data.type }, // e.g: { kittenId: 1234 }
              metadata: data, // e.g: { kittenId: 1234 }
            }, {
              onReadyForServerApproval: onReadyForServerApproval,
              onReadyForServerCompletion: onReadyForServerCompletion,
              onCancel: onCancel,
              //onCancel: function(paymentId) { console.log('canc') },
              onError: onError,
              //onError: function(error, payment) { /* ... */ },
            });
        },
        executePaymentCompletion({commit, state}, data){
            //console.log('in incomplete function')
            let paymentId = data.paymentId
            let txid = data.txid
            const axiosClient = axios.create({
                //baseURL: 'http://localhost:8000',
                timeout: 20000,
                headers: {
                   Authorization: 'Bearer ' + state.token, //the token is a variable which holds the token
                   useruid: state.user.uid
                }
            });
            //const axiosClient = axios.create({baseURL: 'https://piketplace.com', timeout: 20000});
            const config = {headers: {'Content-Type': 'application/json', 'Content-Type': 'application/json', 'Access-Control-Allow_Origin': '*'}};

            axiosClient.post('/api/v1/incomplete', { paymentId:paymentId, txid:txid }, config);

        },
        cancelPayment({commit, state}, data){
            let paymentId = data.paymentId
            let txid = data.txid
            const axiosClient = axios.create({
                //baseURL: 'http://localhost:8000',
                timeout: 20000,
                headers: {
                   Authorization: 'Bearer ' + state.token, //the token is a variable which holds the token
                   useruid: state.user.uid
                }
            });
            //const axiosClient = axios.create({baseURL: 'https://piketplace.com', timeout: 20000});
            const config = {headers: {'Content-Type': 'application/json', 'Content-Type': 'application/json', 'Access-Control-Allow_Origin': '*'}};

            axiosClient.post('/api/v1/cancel', { paymentId:paymentId, txid:txid }, config);

        },
        validate_product({ commit, state }, data){
            let id = data.id
            let self = data.self
            let status = data.status
            data = {
                status: status,
                reasons: data.reasons,
            }
            if (status == "validated") {
                data = {
                    status: status
                }
            }
            state.saving = true
            state.error = ''
            state.http_request_status = ''
            axios.post('/api/v1/validate-product/'+id, data)
              .then(res => {
                  state.saving = false
                  state.http_request_status = 'success'
                  if (res.data.status) {
                      if (res.data.message=='already_validated') {
                          functions.msg_box(self, 'success', i18n.global.t('message.info'), i18n.global.t('message.already_validated'))
                      }else if(res.data.message=='already_rejected'){
                          functions.msg_box(self, 'success', i18n.global.t('message.info'), i18n.global.t('message.already_rejected'))
                      }else{
                          functions.msg_box(self, 'success', i18n.global.t('message.info'), i18n.global.t('message.saved'))
                      }
                  }else{
                      state.http_request_status = 'error'
                      functions.msg_box(self, 'error', i18n.global.t('message.info'), i18n.global.t('message.an_error_occured'))
                  }
                  //console.log('balance res.data', res.data);
              })
              .catch(err => {
                  state.saving = false
                  state.http_request_status = 'error'
                  functions.msg_box(self, 'error', i18n.global.t('message.info'), i18n.global.t('message.an_error_occured'))
                  state.error = i18n.global.t('message.an_error_occured')
              })
        },
        submit_for_review({ commit, state }, data){
            let self = data.self
            state.saving = true
            state.http_request_status = ''
            axios.post('/api/v1/submit-for-review/'+data.id)
              .then(res => {
                  state.saving = false
                  state.http_request_status = 'success'
                  if (res.data.status) {
                      functions.msg_box(self, 'success', i18n.global.t('message.info'), i18n.global.t('message.submitted_for_review'))
                  }else{
                      state.http_request_status = 'error'
                      functions.msg_box(self, 'error', i18n.global.t('message.info'), i18n.global.t('message.an_error_occured'))
                  }
                  //console.log('balance res.data', res.data);
              })
              .catch(err => {
                  state.saving = false
                  state.http_request_status = 'error'
                  functions.msg_box(self, 'error', i18n.global.t('message.info'), i18n.global.t('message.an_error_occured'))
                  state.error = i18n.global.t('message.an_error_occured')
              })
        },
        setPaymentVerifier({ commit, state }, data){
            commit('CLEAR_PAYMENT_VERIFIER')
            console.log('in setPaymentVerifier', data)
            state.uniqueId = data.uniqueId;
            self = data.self;
            const verifier = () => {
                return new Promise((resolve, reject) => {
                    //console.log("verifier in pay resolve", state.uniqueId);
                    if (state.uniqueId == '' || state.uniqueId == null) {
                        return 0;
                    }
                    //console.log("verifier in pay resolve after");
                    let uniqueId = state.uniqueId;
                    let userId = state.user?state.user.id:0;
                    let req_data = {uniqueId:uniqueId, userId:userId}
                    if (data.productId) {
                        req_data.productId=data.productId
                    }
                    axios.post(`/api/v1/payment-verifier`, req_data)
                    .then(res => {
                        //this.order = res.data.order;
                        //console.log("verifier", res.data.payment);
                        if (res.data.payment != null) {
                            state.paid = true
                            commit('SET_UNIQUE_ID', '')
                            clearInterval(state.verifierPayment);//Break the setInterval
                        }
                    })
                    .catch(error => {
                        //console.log(error);
                        //this.$show_modal.show_modal('comment_sent');
                    });
                })
            }
            const verifierPayment = setInterval(() => { verifier() }, 3000)
            commit('SET_PAYMENT_VERIFIER', verifierPayment)
        },
        isoToEmoji({ commit }, code){
          this.state.flag = code
            .split('')
            .map(letter => letter.charCodeAt(0) % 32 + 0x1F1E5)
            .map(n => String.fromCodePoint(n))
            .join('')
        },
    }
})