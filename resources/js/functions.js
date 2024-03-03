import axios from "axios";
//import i18n from "./app";
//import Vue from 'vue'
import Vue3ConfirmDialog from 'vue3-confirm-dialog';

export const auth = {
    authenticate: (dataParam) => {
        let session = dataParam.session;
        //session.start();
        let res = '';
        //const axiosClient = axios.create({baseURL: 'http://localhost:8000', timeout: 20000});
        const axiosClient = axios.create({baseURL: 'https://piketplace.com', timeout: 20000});
        const onIncompletePaymentFound = (payment) =>{
            console.log('onIncompletePaymentFound', payment);
            let txid = payment.transaction.txid;
            let paymentId = payment.identifier;
            return axiosClient.get('/api/v1/incomplete?paymentId='+paymentId+'&txid='+txid, { payment }, config);
            //return res;
        };
        Pi.authenticate(['username', 'payments'], onIncompletePaymentFound).then(function(auth) {
            //console.log(auth.user.username);
            console.log("aaauthhh", auth);
            //alert('auth success')
            $('#user_id').html('@'+auth.user.username);
            
            /*if (auth.user.username == 'djedje00' || auth.user.username == 'Filano') {
                $('.to_hide').css('display', 'block');
            }*/
            
            //this.$session.start();
            let user = {};
            
            let res = axios.get('https://ipapi.co/json').then(function ({data}) {
                //console.log(data);
                user.country = data.country;
                user.country_name = data.country_name;
            })
            .catch(function (error) {
                resolve(error);
            });

            let uid = auth.user.uid;
            let username = auth.user.username;
            let accessToken = auth.accessToken;
            res = axios.get('/api/v1/create_pi_user', {params: {uid, username, accessToken}}).then(function ({data}) {
                user = data.user;
                let locale = data.user.locale!=''?data.user.locale:'en';
                user.locale = locale;
                let nb = 0;
                i18ns.locale = locale
                if (user.isAdmin) {
                    $('.to_hide').css('display', 'block');
                }
                //console.log('jjh', user.cart);
                if (user.cart != null && user.cart != '') {
                    //nb = JSON.parse(user.cart).length
                    nb = user.cart.length
                }
                console.log("fffffffffff", data)
                $('#user_id').html(user.fullnameOrUsername);
                $('.profil').attr('src', user.avatar);
                $('.nb_cart').html(nb);
                $('.nb_notif').html(data.nb_notif!=0?data.nb_notif:'');
                $('.nb_support').html(data.nb_new_messages!=0?data.nb_new_messages:'');
                session.set("user", user);
                console.log("sdfs dfse", data);
                document.getElementById('auth').style.display="none";
                if(data.state == 'new'){
                    document.getElementById('menu-language').classList.add('menu-active');
                    document.getElementsByClassName('menu-hider')[0].classList.add('menu-active');
                }
            })
            .catch(function (error) {
                //resolve(error);
            });
            console.log('country_name')
        }).catch(function(error) {
          //alert('error Pi authentication');
          console.error(error);
        });
    }

}

export const pay = {
    payment: (data) => {
        //alert(data.id);
        //alert(data.userId);
        
        const scopes = ['username', 'payments'];
        //const axiosClient = axios.create({baseURL: 'http://localhost:8000', timeout: 20000});
        const axiosClient = axios.create({baseURL: 'https://piketplace.com', timeout: 20000});
        const config = {headers: {'Content-Type': 'application/json', 'Content-Type': 'application/json', 'Access-Control-Allow_Origin': '*'}};

        const onIncompletePaymentFound = (payment) =>{
            console.log('onIncompletePaymentFound', payment);
            let txid = payment.transaction.txid;
            let paymentId = payment.identifier;
            return axiosClient.get('/api/v1/incomplete?paymentId='+paymentId+'&txid='+txid, { payment }, config);
            //return res;
        };

        ///////////////////////////////////////////////////////////////////////////////
        const onReadyForServerApproval = (paymentId) =>{
            console.log('onReadyForServerApproval', paymentId);
            let res = axiosClient.get("/api/v1/approve?paymentId="+paymentId, { paymentId }, config);
            console.log(res);
            return res;
        };
        const onReadyForServerCompletion = (paymentId, txid) =>{
            console.log('onReadyForServerCompletion', paymentId, txid);
            return axiosClient.get('/api/v1/complete?paymentId='+paymentId+'&txid='+txid, { paymentId, txid }, config);
        };
        const onCancel = (paymentId) =>{
            console.log('onCancel', paymentId);
            //location.href = 'localhost:8000/publication'+'3'+'unique';
            //return axiosClient.get('/cancel?paymentId='+paymentId, { paymentId }, config);
        };
        const onError = (error, payment) =>{
            console.log('onError', error);
            if (payment) {
                console.log(payment);
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
    }
}

export const show_modal = {
    show_modal: (data) => {
        var element = document.getElementById(data.id);
        element.classList.add('menu-active');
        if (data.title != undefined) {
            element.getElementsByClassName('title')[0].innerHTML=data.title;
        }
        if (data.message != undefined) {
            element.getElementsByClassName('message')[0].innerHTML=data.message;
        }
        if (data.height != undefined) {
            element.style.height=data.height+"px";
        }
        if (data.btn_text != undefined) {
            var t = element.getElementsByClassName('btn-text')[0]
            if (t!=null) {
                t.innerHTML=data.btn_text;
                t.style.display = "block"
            }
        }else{
            var t = element.getElementsByClassName('btn-text')[0]
            if (t!=null) {t.style.display = "none"}
            
        }
        if (data.hide) {
            setTimeout(function () {
                element.classList.remove('menu-active');
                document.getElementsByClassName('menu-hider')[0].classList.remove('menu-active');
            }, data.time);
        }
        
        document.getElementsByClassName('menu-hider')[0].classList.add('menu-active');
        var _0x77b9x41 = element.getAttribute('data-menu-hide');
        if (_0x77b9x41) {
            setTimeout(function () {
                element.classList.remove('menu-active');
                document.getElementsByClassName('menu-hider')[0].classList.remove('menu-active');
            }, _0x77b9x41);
        }
    }
}

export const hide_modal = {
    hide_modal: (data) => {
        //document.getElementById(data).classList.remove('menu-active');
        let element = document.getElementById(data)
        if (element == null) {return}
        element.classList.remove('menu-active');
        document.getElementsByClassName('menu-hider')[0].classList.remove('menu-active');
    },
    all: () => {
        let elements = document.getElementsByClassName('menu-active')
        if (elements.length == 0) {return}
        elements.forEach(val=>{
            val.classList.remove('menu-active');
        })
        
        document.getElementsByClassName('menu-hider')[0].classList.remove('menu-active');
    }
}

export const payment_verifier = {
    verify: (uniqueId) => {
        axios.get(`/api/v1/payment-verifier`, {params: {uniqueId}})
        .then(res => {
            //this.order = res.data.order;
            console.log("verifier", res.data.order);
        })
        .catch(error => {
            this.$show_modal.show_modal('comment_sent');
        });
    }
}

export const delivery_period = {
    get: (period) => {
        if (period == 1) {
            return 'minute';
        }
        if (period == 2) {
            return 'hour';
        }
        if (period == 3) {
            return 'day';
        }
        if (period == 4) {
            return 'month';
        }
    }
}

export const functions = {
    is_entier_not_zero: (entier) => {
        if(isNaN(parseInt(entier)) || isNaN(entier) || entier==0 || entier==0.0 || parseInt(entier) ==0 || parseInt(entier) !=entier){
            return false;
        }
        return true;
    },
    is_decimal_not_zero: (entier) => {
        if(isNaN(parseInt(entier)) || isNaN(entier) || entier==0 || entier==0.0){
            return false;
        }
        return true;
    },
    is_an_email: (email) => {
        if (email=='') {return false}
        //Email format regex
        return email.toLowerCase().match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
    },
    removeAccentCharacter: (string) => {
        return string.replace('é', 'e').replace('è', 'e').replace('ê', 'e')
        .replace('î', 'i').replace('ï', 'i').replace('ç', 'c').replace('à', 'a')
        .replace('â', 'a').replace('ô', 'o').replace('ö', 'o').replace('ë', 'e')
        .replace('å', 'a').replace('Î', 'i')
    },
    toFixed: (x) => {
      if (Math.abs(x) < 1.0) {
        var e = parseInt(x.toString().split('e-')[1]);
        if (e) {
            x *= Math.pow(10,e-1);
            x = '0.' + (new Array(e)).join('0') + x.toString().substring(2);
        }
      } else {
        var e = parseInt(x.toString().split('+')[1]);
        if (e > 20) {
            e -= 20;
            x /= Math.pow(10,e);
            x += (new Array(e+1)).join('0');
        }
      }
      return x;
    },
    msg_box: (self, type = "info", title = "Info", msg, redirect_url = '') => {
        let img = '/site_images/info.PNG'
        let btn = {
            yes: 'OK'
        }
        if (type=='confirm') {
            img = '/site_images/confirm.PNG'
            let yes = self.$t('message.yes')
            if (redirect_url=='login') {
                yes = self.$t('message.log_in')
            }
            if (redirect_url=='logout') {
                yes = self.$t('message.log_out')
            }
            btn = {
                yes: yes,
                //no: self.$t('message.no'),
                no: self.$t('message.confirmation.no_cancel'),
            }
        }
        if (type=='error') {
            img = '/site_images/error.png'
        }
        if (type=='success') {
            img = '/site_images/success.png'
        }
        msg = '<br><img src="'+img+'"><br><strong style="font-size:16px;">'+msg+'<strong>'
        self.$confirm(
            {
              title: title,
              message: msg,
              button: btn,
              html: true
                ,
                /**
                * Callback Function
                * @param {Boolean} confirm
                */
                callback: confirm => {
                  if (confirm) {
                    if (redirect_url == -1) {
                        self.$router.go(-1)
                    }
                    if (redirect_url == 'login') {
                        self.$store.dispatch('signInPiNetwork', {self: self})
                        return;
                    }
                    if (redirect_url == 'logout') {
                        self.$store.dispatch('signOut', self)
                        return
                    }
                    if (redirect_url != '' && redirect_url != -1) {
                        self.$router.push(redirect_url)
                    }
                  }else if(type == 'info' || type == 'success' || type == 'error'){
                    if (redirect_url == -1) {
                        self.$router.go(-1)
                    }
                    if (redirect_url == 'login') {
                        self.$store.dispatch('signInPiNetwork', {self: self})
                        return;
                    }
                    if (redirect_url == 'logout') {
                        self.$store.dispatch('signOut', self)
                        return
                    }
                    if (redirect_url != '' && redirect_url != -1) {
                        self.$router.push(redirect_url)
                    }
                  }
                }
            }
        )
    },
    agreement: (self, agreements, type) => {
        $( document ).ready(function() {
            $('.vc-container').css('width', '90%')
            $('.vc-container').css('height', '75%')
            $('.vc-text-grid').css('height', '100%')
            $('.vc-text-grid').css('overflow-y', 'scroll')
        });
        let title = "Seller agreement"
        let btn = {
            yes: self.$t('message.confirmation.yes_i_agree'),
            no: self.$t('message.no'),
        }
        let getter = "seller_agreements"
        if (type == 'shipping') {
            title = "Shipping agreement"
            getter = "shipping_agreements"
        }
        if (type == 'user') {
            title = "User agreement"
            getter = "user_agreements"
            btn = {
                yes: self.$t('message.confirmation.yes_i_agree'),
            }
            $( document ).ready(function() {
                $('.vc-container').css('width', '100%')
                $('.vc-container').css('height', '100%')
                $('.vc-text-grid').css('height', '100%')
                $('.vc-text-grid').css('overflow-y', 'scroll')
            });
        }
        //let dir = self.$i18n.locale=='ar'?'dir="rtl"':''
        let dir = ''
        let textAlign = self.$i18n.locale=='ar'?'right':'left'
        let msg = '<div '+dir+' class="vc-text-grid-content" style="text-align:'+textAlign+';">'
        if (agreements && agreements[getter]) {
            if (agreements[getter][self.$i18n.locale]) {
                agreements[getter][self.$i18n.locale].forEach((val)=>{
                    msg += val.value
                })
            }
        }
        
        msg += '</div>'

        self.$confirm(
            {
              title: title,
              message: msg,
              button: btn,
              html: true
                ,
                /**
                * Callback Function
                * @param {Boolean} confirm
                */
                callback: confirm => {
                  if (!confirm) {
                    self.$router.go(-1)
                  }else{
                    self.$store.dispatch('scrollToTop')
                  }
                }
            }
        )
    },
    is_pi_browser: () => {
        if (!navigator.userAgent.toLowerCase().includes('pibrowser')) {
            console.log('No pi browser')
            //alert('No pi browser')
            return false
        }else{
            console.log('Pi browser')
            //alert('Pi browser')
            return true
        }
    },
    amount_format: (amount, currency) => {
        //return amount+'-'+currency
        //amount = this.scientific_annotation_to_decimal(amount)
        amount = parseFloat(amount).toLocaleString('en-US', {
            useGrouping: false,//No thousand separator
            minimumFractionDigits: 0,
            maximumFractionDigits: 15, //15 digits after the comma, Adjust the maximum fraction digits as needed
        });
        if (currency.toLowerCase() == 'piket') {
            return amount+' Piket'
        }
        return currency+''+amount
    },
    scientific_annotation_to_decimal: (number, nb_digits = 15) => {
        number = parseFloat(number).toLocaleString('en-US', {
            useGrouping: false,//No thousand separator
            minimumFractionDigits: 0,
            maximumFractionDigits: nb_digits, //15 digits after the comma, Adjust the maximum fraction digits as needed
        });
        return number
    },
    get_before_dot_part: (number, nb_digits = 15) => {
        number = parseFloat(number).toLocaleString('en-US', {
            useGrouping: false,//No thousand separator
            minimumFractionDigits: 0,
            maximumFractionDigits: nb_digits, //15 digits after the comma, Adjust the maximum fraction digits as needed
        });
        return number.toString().split('.')[0]
    },
    get_after_dot_part: (number, nb_digits = 15) => {
        number = parseFloat(number).toLocaleString('en-US', {
            useGrouping: false,//No thousand separator
            minimumFractionDigits: 0,
            maximumFractionDigits: nb_digits, //15 digits after the comma, Adjust the maximum fraction digits as needed
        });
        return number.toString().split('.')[1]
    },
    remove_last_zeros: (number) => {
        return Number(parseFloat(number).toFixed(15)).toString()
    },
    dateToUTC: (date) => {
        const year = date.getUTCFullYear();
        const month = date.getUTCMonth();
        const day = date.getUTCDate();
        const hours = date.getUTCHours();
        const minutes = date.getUTCMinutes();
        const seconds = date.getUTCSeconds();
        const milliseconds = date.getUTCMilliseconds();
        return new Date(Date.UTC(year, month, day, hours, minutes, seconds))
    },
    getFullStringAddress(addressObject) {
        let fullAddr = addressObject.country_name+", "+addressObject.city+","+addressObject.address
        if (addressObject.phone_number!=undefined && addressObject.phone_number!=null) {
            fullAddr = fullAddr +", "+addressObject.phone_number
        }
        if (addressObject.email!=undefined && addressObject.email!=null) {
            fullAddr = fullAddr +", "+addressObject.email
        }
        return fullAddr
    },
    isoToEmoji(code){
        return code
        .split('')
        .map(letter => letter.charCodeAt(0) % 32 + 0x1F1E5)
        .map(n => String.fromCodePoint(n))
        .join('')
    },
    isLessThan2MB(file){
        return file.size / 1024 / 1024 < 2
    },
    isLessThan1MB(file){
        return file.size / 1024 / 1024 < 1
    },
    isLessThan0_5MB(file){
        return file.size / 1024 / 1024 < 0.5
    },
}