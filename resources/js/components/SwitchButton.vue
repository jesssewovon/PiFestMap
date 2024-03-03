<template>
    <div class="d-flex no-effect" role="button" aria-expanded="false">
        <div class="me-4 pe-2">
            <div class="custom-control ios-switch">
                <input type="checkbox" class="ios-input" :id="id" @input="(event) => $emit('update:checked',event.target.checked)" :checked="checked">
                <label :for="id" class="custom-control-label"></label>
            </div>
        </div>
        <div class="" style="margin-left: 30px;padding-top: 12px;">
            <label :for="id" class="font-600 app-color font-12" style="cursor: pointer;">{{label}}</label>
        </div>
    </div>
</template>

<style scoped>
    @import url("https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
</style>

<script>
    export default{
        components: {
        },
        props: {
            checked: {
                type: Boolean,
                defaut: true,
            },
            label: {
                type: String,
                defaut: "",
            },
            id: {
                type: String,
                defaut: "",
            },
        },
        data: function () {
        return {
            switched_compute: false,
        }
      },
      computed: {
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
      },
        /*mounted() {
            this.switched_compute = this.checked
        },*/
        watch: {
        },
        methods: {
        }
    }
</script>

<style scoped>

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
      background-color: v-bind(checked===true?'#090C49':'#828282');
      border-color: v-bind(checked===true?'#090C49':'#828282');
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
      border-color: v-bind(checked===true?'#090C49':'#828282');
      border-radius: 50px !important;
      transition: all 250ms ease;
    }

    .ios-input:checked ~ .custom-control-label::before {
      background-color: #F1F7FF !important;
    }
</style>
