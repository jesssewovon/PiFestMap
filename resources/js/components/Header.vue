<template>
    <div class="header header-fixed header-logo-center" style="opacity: 1; border: none;" :style="colorWhite===true?'background-color: #fff':'background-color: #F1F7FF'">
        <!-- <a href="index.html" class="header-title">Piketplace</a> -->
        <!-- <form class="header-title">
            <input type="text" v-model="search" style="display: inline;width: 100%;height: 35px;background-color: rgba(255,255,255,0.7);border-radius: 21px;padding-right: 25px;padding-left: 10px;"><i class="fas fa-search" style="margin-left: -20px;" @click="searching"></i>
            <button @click.prevent="searching"></button>
        </form> -->

        <a class="header-icon header-icon-1" style="width: 100%;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
            <div @click="back" style="width: 30%;display: inline-block;text-align: left;" class="">
                <span v-if="route!=''" style="">
                    <i class="fas fa-arrow-left font-18" style="margin: auto;padding: 10px;border-radius: 3px;"></i>
                </span>
            </div>
            <div style="width: 39%;display: inline-block;">
                <img v-show="withAppName==true" src="/site_images/logo.png" alt="Logo" style="width: 30px;height: 30px;border-radius: 15%;object-fit: cover;">&nbsp;
                <strong v-show="withAppName==true" :data-translate="title" id="titre" class="font-18 app-color">
                    Pi Fest Map
                </strong>
            </div>
            <div style="width: 30%;display: inline-flex;line-height: normal;margin-top: 5px;vertical-align: middle;justify-content: end;">
                <div @click="$router.push('/shopping-cart')" v-if="show_cart===true" style="">
                    <i class="fa fa-shopping-cart app-color font-20"></i>
                    <span class="fa-stack" style="border-radius: 50%;margin: auto 5px;background-color: red;color: #fff;width: 15px;height: 15px;line-height: 15px;margin-top: -17px;">
                        {{shopping.shopping_cart?shopping.shopping_cart.length:0}}
                    </span>
                    <div>
                        <label class="app-color">Shopping cart</label>
                    </div>
                </div>
            </div>
        </a>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    export default {
        data: function () {
            return {
                search: '',
                img: null,
                prevRoute: null,
            }
        },
        props: {
            route: {
                type: String,
                defaut: '',
            },
            withAppName: {
                type: Boolean,
                defaut: true,
            },
            show_cart: {
                type: Boolean,
                defaut: false,
            },
            colorWhite: {
                type: Boolean,
                defaut: false,
            }
        },
        beforeRouteEnter(to, from, next) {
          next(vm => {
            vm.prevRoute = from
          })
        },
        computed: {
            ...mapState(['test', 'isLoading', 'isOpenLoading', 'categories', 'title', 'shopping']),
            user:{
                get(){
                    return this.$store.state.user
                },
                set(val){
                    this.$store.state.user = val
                }
            }
        },
        mounted() {
            console.log(this.user)
        },
        methods: {
            async searching(){
                this.$session.set('search', this.search);
                this.$session.set('sub', this.search);
                if (this.$router.currentRoute.path != '/') {
                    this.$router.push('/');
                }
                
            },
            setData(data){
                if (data.title!=undefined) {
                    this.title = data.title
                }
                if (data.route!=undefined) {this.route = data.route}
                if (data.img!=undefined) {this.img = data.img}
            },
            linkLoad(link){
                this.$router.push(link)
            },
            back(){
                this.$router.back()
                //this.$router.go(-1);
                //this.$router.push(this.route);
                //this.$router.push(this.route?this.route.path:"");
                /*let session = this.$session;
                let r = session.get("list_routes");
                let path = r[r.length-2];
                console.log("rrr", r, path)
                session.set("list_routes", r.splice(-1));
                this.$router.push(path!=undefined?path:"");*/
            },
        }
    }

    
</script>

<style scoped>
    .badge {
      position: relative;
      top: -65px;
      right: -16px;
      border-radius: 50%;
      background: red;
      color: white;
    }
</style>
