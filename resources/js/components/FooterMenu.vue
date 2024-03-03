<template>
    <div v-show="!isOpenLoading && $route.name !='support' && $route.name !='message'" id="footer-bar" class="footer-bar-6 footer_menu_hide">

        <div style="width: 95%;margin: auto;">
            <div style="width: 78%;display: inline-block;">
                <button style="background-color: #090C49;width: 90%;margin: auto;border-radius: 9px;color: #fff;height: 40px;">
                    <i class="fa fa-plus-circle" style="color: #FAD09E;font-size: 14px;"></i>
                    Add my business
                </button>
            </div>
            <div style="width: 18%;display: inline-block;">
                <button style="background-color: #090C49;width: 40px;margin: auto;border-radius: 50%;color: #fff;height: 40px;">
                    <i class="fa fa-map" style="color: #FAD09E;"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    export default {
    data() {
        return {
            show: true,
        };
    },
    computed: {
        ...mapState(['isLoading', 'isOpenLoading']),
        user:{
            get(){
                return this.$store.state.user
            },
            set(val){
                this.$store.state.user = val
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
