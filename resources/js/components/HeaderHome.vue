<template>
    <div v-show="!isOpenLoading && $route.name !='support' && $route.name !='message'" class="header header-fixed header-logo-center" style="opacity: 1; background-color: #fff;">
        <!-- <a href="index.html" class="header-title">Piketplace</a> -->
        <!-- <form class="header-title">
            <input type="text" v-model="search" style="display: inline;width: 100%;height: 35px;background-color: rgba(255,255,255,0.7);border-radius: 21px;padding-right: 25px;padding-left: 10px;"><i class="fas fa-search" style="margin-left: -20px;" @click="searching"></i>
            <button @click.prevent="searching"></button>
        </form> -->

        <a class="header-icon header-icon-1" style="width: 250px;margin-left: 20px;text-align: left;white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;">
            <!-- <span v-if="route.path!=undefined && route.path!=null" @click="back" style="padding: 10px;">
                <i class="fas fa-arrow-left font-18" style="margin: auto;padding: 10px;background-color: #f0f0f0;border-radius: 3px;"></i>
            </span>&nbsp;&nbsp; -->
            <!-- <span style="padding: 10px;">
                <i class="fa fa-bars font-18" style="margin: auto;padding: 10px;background-color: #090C49;border-radius: 3px;color: #000;"></i>
            </span> -->&nbsp;&nbsp;
            <img src="/site_images/logo.png" alt="Logo" style="width: 30px;height: 30px;border-radius: 15%;object-fit: cover;">&nbsp;
            <strong :data-translate="title" id="titre" class="font-18">
                Pi Fest Map
            </strong>
        </a>
        <!-- <router-link :to="route.path">
            <i v-if="route!=null && route!='' && route!='/'"  class="fa fa-angle-left"></i>&nbsp;{{title}}
        </router-link> -->
        <!-- <a href="#" data-toggle-theme class="header-icon header-icon-4 show-on-theme-dark">
            <i class="fas fa-sun"></i>
        </a> -->
        <span class="header-icon header-icon-3" style="height: 100%;">
            <div>
                <img @click="open_language_select" src="/site_images/langue.png">
            </div>
        </span>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    export default {
        data: function () {
            return {
                search: '',
                img: null,
                route: '',
            }
        },
        computed: {
            ...mapState(['test', 'isLoading', 'isOpenLoading', 'categories', 'title']),
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
                this.$router.go(-1);
                //this.$router.push(this.route?this.route.path:"");
                /*let session = this.$session;
                let r = session.get("list_routes");
                let path = r[r.length-2];
                console.log("rrr", r, path)
                session.set("list_routes", r.splice(-1));
                this.$router.push(path!=undefined?path:"");*/
            },
            open_language_select() {
                this.$hide_modal.hide_modal('menu-main');
                this.$show_modal.show_modal({id: 'menu-language'});
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

    .header-logo-center .header-icon-3 {
      right: 20px!important;
    }
</style>
