<template>
    <div>
        <Header v-if="prevRoute" :withAppName="true" :route="prevRoute.path" ref="header"/>
        <div class="page-content app-background-color" style="">
            <!-- <h3 style="text-align: center;margin-bottom: 20px;" class="app-color">Loyalty Card</h3> -->
            <div style="padding-top: 30px;padding-bottom: 20px;">
                <h3 style="text-align: center;" class="app-color">Loyalty Card</h3>
            </div>
            <div style="width: 100%;height: 2px;border: 2px solid #090C49;" class="app-color font-600"></div>
            <div class="card card-style app-background-color" style="box-shadow: none;">
                <SwitchButton id="loyalty_card" v-model:checked="business_profile.loyalty_card_status" :label="business_profile.loyalty_card_status===true?'Loyalty card is on':'Turn on Loyalty card'" />
                <div v-if="business_profile.loyalty_card_status===true && save_card===false" class="content mb-0" id="">
                    <label for="stamp_free_item" class="app-color">Stamps needed for free item</label>
                    <div class="input-style has-borders validate-field mb-4">
                        <input type="text" inputmode="numeric" class="form-control validate-name app-color app-border" id="stamp_free_item" placeholder="" v-model="form.stamp_free_item" maxlength="40" style="">
                        
                        <i class="fa fa-times disabled invalid color-red-dark"></i>
                        <i class="fa fa-check disabled valid color-green-dark"></i>
                        <div id="required-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                        <div id="length-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                    </div>
                    <label for="name_free_item" class="app-color">Name of free item</label>
                    <div class="input-style has-borders validate-field mb-4">
                        <input type="text" class="form-control validate-name app-color app-border" id="name_free_item" placeholder="" v-model="form.name_free_item" maxlength="40" style="">
                        
                        <i class="fa fa-times disabled invalid color-red-dark"></i>
                        <i class="fa fa-check disabled valid color-green-dark"></i>
                        <div id="required-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                        <div id="length-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                    </div>
                    <label for="number_free_item" class="app-color">Number of free items</label>
                    <div class="input-style has-borders validate-field mb-4">
                        <input type="text" inputmode="numeric" class="form-control validate-name app-color app-border" id="number_free_item" placeholder="" v-model="form.number_free_item" maxlength="40" style="">
                        
                        <i class="fa fa-times disabled invalid color-red-dark"></i>
                        <i class="fa fa-check disabled valid color-green-dark"></i>
                        <div id="required-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                        <div id="length-libelle" style="color: red;display: none;">{{ $t('message.required.libelle') }}</div>
                    </div>
                    <button @click="save_card=true"
                    class="font-900 app-background-color" style="margin-top: 20px;background-color: #090C49!important;color: #fff;border-radius: 10px;width: 100%;height: 50px;">
                        Save
                    </button>
                </div>
                <div v-if="business_profile.loyalty_card_status===true && save_card===true" class="content mb-0" id="">
                    <div style="width: 100%;text-align: center;">
                        <div class="app-dark-background app-color" style="width: 100px;height: 100px;margin: auto;border-radius: 10px;text-align: center;">
                            <span style="color: #FAD09E;line-height: 70px;font-size: 64px;">{{form.stamp_free_item}}</span>
                            <div style="color: #fff;">Stamps</div>
                        </div>
                    </div>
                    <div class="mt-3 mb-2" style="width: 100%;text-align: center;">
                        <strong class="font-20 app-color">needed for {{form.number_free_item}} {{form.name_free_item}}</strong>
                    </div>
                    <button @click="save_card=false"
                    class="font-900" style="margin-top: 20px;background-color: transparent!important;color: #090C49;border: 1px solid #090C49!important;border-radius: 10px;width: 100%;height: 50px;">
                        Edit
                    </button>
                    <button @click="save_loyalty_card"
                    class="font-900 app-background-color" style="margin-top: 20px;background-color: #090C49!important;color: #fff;border-radius: 10px;width: 100%;height: 50px;">
                        Confirm
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
    //import UseDropzoneDemo from '../components/UseDropzoneDemo.vue'
    //import { Dropzone } from "dropzone";
    //import VueLoadingButton from "vue-loading-button";
    import axios from 'axios';
    import { mapState } from 'vuex';
    import { v4 as uuidv4 } from 'uuid';

    import Header from '../components/Header.vue'
    import SwitchButton from '../components/SwitchButton.vue'

    export default{
        components: {
            Header, SwitchButton,
        },
        props: {
            product: {
                type: Object,
                defaut: null,
            }
        },
        data: function () {
        return {
          form: {
            stamp_free_item: 0,
            name_free_item: "",
            number_free_item: 0,
          },
          prevRoute: null,

          switched: true,
          save_card: false,
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
            if (this.business_profile && this.business_profile.loyalty_card) {
                this.form = this.business_profile.loyalty_card
            }
            
            this.$store.dispatch('scrollToTop')
        },
        watch: {
            
        },
        methods: {
            save_loyalty_card(){
                this.saving = true;
                this.form.business_profiles_id = this.business_profile.id
                axios.post('/api/v1/loyalty-cards', this.form)
                .then(res => {
                    console.log(res.data)
                    this.saving = false;
                    if (res.data.status === true) {
                        this.$show_modal.show_modal({id: 'success', title: "Success", message: "Saved successfully", btn_text: 'OK'})
                    } else {
                        this.$functions.msg_box(this, 'error', '', this.$t('message.an_error_occured'))
                    }
                })
                .catch(error => {
                    console.log(error)
                    this.saving = false
                    if (error.response.status !== 401) {
                        this.$functions.msg_box(this, 'error', '', this.$t('message.an_error_occured'))
                    }
                })
            },
        }
    }
</script>

<style scoped>
    #dropzone{
        border-radius: 10px;
        border-width: 1px;
        display: block;
        width: 900px;
        height: 50px;
    }


    .error-message {
      display: none;
      color: red;
    }
    .border-error{
        border: 1px solid red!important;
    }

    /* ✨ The magic ✨ */
    form.errors {
      :invalid {
        border-color: red;
      }
      .error-message {
        display: block;
      }
    }

    .ios-switch label::after {
      font-family: 'Font Awesome 5 Free';
      content: "\f111";
      font-weight: 900; /* <-- add this */
      color: #fff;
      display: block !important;
      margin-top: -25px;
      z-index: 6;
      width: 25px !important;
      height: 25px !important;
      padding-left: 4px;
      transition: all 250ms ease;
      border-radius: 50px !important;
      background-color: v-bind(switched?'#090C49':'#828282');
      border-color: v-bind(switched?'#090C49':'#828282');
      /*border: solid 1px #090C49;*/
      transition: all 250ms ease;
    }

    .ios-switch label::before {
      content: "";
      display: block !important;
      background-color: #F1F7FF;
      pointer-events: all;
      margin-top: -5px;
      margin-left: -1px;
      width: 58px !important;
      height: 25px !important;
      /*border: solid 1px #090C49;*/
      border-color: v-bind(switched?'#090C49':'#828282');
      border-radius: 50px !important;
      transition: all 250ms ease;
    }

    .ios-input:checked ~ .custom-control-label::before {
      background-color: #F1F7FF !important;
    }
</style>
