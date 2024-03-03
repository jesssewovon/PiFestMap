<template>
  <div>
    <div id="select-business-type" class="menu menu-box-modal rounded-m app-background-color" data-menu-effect="menu-over" style="display: block;width: 95%;">
        <div class="menu-title">
            <h1 class="font-24 app-color">{{$t('message.choose_category')}}</h1>
            <a href="#" class="close-menu"><i class="fa fa-times app-color"></i></a>
        </div>
        <div class="me-4 ms-3 mt-1">
            <div class="list-group list-custom-small" v-if="business_types.length > 0">
                <a :data-category-id="type.id" :data-category-libelle="$t('message.categories.'+type.code)" :data-category-src="'images/'+type.img" href="#" class="categorie close-menu" v-for="type in business_types" :key="type.id" v-on:click="select_type(type)">
                  <img class="me-3 mt-1" :src="'images/'+type.img">
                  <span class="app-color">{{$t('message.categories.'+type.code)}}</span>
                  <i v-if="selected_category && selected_category.id == type.id" class="fa fa-check-circle color-piketplace"></i>
                </a>
            </div>
            <div class="clear"></div>
        </div>
    </div>
  </div>
</template>

<script>
    import { mapState } from 'vuex';

export default {
    data() {
        return {
            keyword: null,
        };
    },
    computed: {
        ...mapState(['isLoggedIn']),
        user:{
            get(){
                return this.$store.state.user
            },
            set(val){
                this.$store.state.user = val
            }
        },
        business_types:{
            get(){
                return this.$store.state.business_types
            },
            set(val){
                this.$store.state.business_types = val
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
    mounted() {
        //this.getResults();
    },
    watch: {
        keyword(after, before) {
            //this.getResults();
        }
    },
    methods: {
        select_type(type){
            this.business_profile.business_type = type
            this.business_profile.business_types_id = type.id
        }
    }
}
</script>

<style>
select {
    width: 150px;
    line-height: 49px;
    height: 38px;
    font-size: 22px;
    outline: 0;
    margin-bottom: 15px;
}
.list-custom-small img {
  width: 20px;
  height: 20px;
  margin-right: 10px;
}
.list-custom-small {
  line-height: 38px;
}
</style>