<template>
    <div class="page-title page-title-fixed">
        <h1 id="title" style="font-size: 24px;"><i v-if="route!=null && route!='' && route!='/'" @click="redirect" class="fa fa-angle-left"></i>&nbsp;{{title}}</h1>
        <router-link to="/notifications" class="page-title-icon shadow-xl bg-theme color-theme">
            <div>
                <i class="fa fa-bell"></i>
            </div>
            <span class="badge nb_notif"></span>
        </router-link>
        <router-link to="/cart" class="page-title-icon shadow-xl bg-theme color-theme show-on-theme-light cart" data-toggle-theme>
            <div>
                <i class="fa fa-shopping-cart"></i>
            </div>
            <span class="badge nb_cart">{{nb}}</span>
        </router-link>
        <a href="#" class="page-title-icon shadow-xl bg-theme color-theme show-on-theme-dark" data-toggle-theme>
            <i class="fa fa-shopping-cart color-yellow-dark"></i>
        </a>
        <a href="#" class="page-title-icon shadow-xl bg-theme color-theme button-menu" data-menu="menu-main">
            <i class="fa fa-bars"></i>
        </a>
    </div>
</template>

<script>
    export default{
        data() {
            return {
                title: '',
                route: '',
                nb: 0,
            };
        },
        mounted() {
            this.getTitle();
        },
        methods: {
            async getTitle() {
                let res = await axios.get(`https://ipapi.co/json`).then(res => this.title=res.data.country_name);
                
            },
            setData(data){
                this.title = data.title
                this.route = data.route
                this.nb = data.nb
            },
            redirect(){
                this.$router.push(this.route.path);
            }
        }
    }
</script>

<style scoped>
    .badge {
      position: relative;
      top: -55px;
      right: -10px;
      padding: 5px 5px;
      border-radius: 50%;
      background: red;
      color: white;
    }
    .page-title{
        margin-top: -43px;
    }
</style>
