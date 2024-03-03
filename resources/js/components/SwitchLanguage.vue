<template>
  <div>
    <div id="menu-language" class="menu menu-box-modal rounded-m app-background-color"  data-menu-effect="menu-over" style="display: block; min-height: 265px;width: 90%;">
        <div class="menu-title">
            <h1 class="font-24 app-color">Choose your Language</h1>
            <a href="#" class="close-menu"><i class="fa fa-times app-color"></i></a>
        </div>
        <div class="me-4 ms-3 mt-2">
            <div class="list-group list-custom-small" style="margin-bottom: 15px;">
                <a v-for="lang in languages" href="#" @click.prevent="update_selected_category_translate(lang.code)" class="close-menu">
                  <img class="me-3 mt-2" :src="'/images/flags/'+lang.flag" style="width:32px;height:32px;">
                  <span class="app-color">{{lang.name}}</span>
                  <i v-if="$i18n.locale==lang.code" class="fa fa-check-circle app-color" style=""></i>
                </a>
            </div>
            <div class="clear"></div>
        </div>
    </div>

    <!-- <select v-model="$i18n.locale">
      <option
        v-for="(lang, i) in langs"
        :key="`lang-${i}`"
        :value="lang">
          {{ lang }}
      </option>
    </select> -->

  </div>
</template>

<script>
  import axios from 'axios';
  import { mapState } from 'vuex';

  export default {
    name: 'SwitchLocale',
    data() {
        return { langs: ['en', 'cn', 'es', 'fr'] }
    },
    computed: {
        ...mapState(['isLoggedIn', 'isLoading', 'languages']),
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
        update_selected_category_translate(lang){
          this.$hide_modal.hide_modal('menu-language')
          this.$i18n.locale = lang;
          
          this.$store.dispatch('setLang', lang)
          if (!this.isLoggedIn) {return}
          this.user.locale = lang;
          /*let tr = $('#titre').attr('data-translate')
          $('#titre').html(this.$t(tr))*/
          axios.post('/api/v1/switchLocale', {lang:lang})
            .then(res => {
                //console.log(data);
            })
            .catch(error => {
              console.log(error)
            });
            setTimeout(function() {
                /*let id = $('#category_selected_id').val()
                let libelle = $('a[data-category-id='+id+']').attr('data-category-libelle')
                $('#category_selected_label').html(libelle)*/
            }, 100)
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
</style>