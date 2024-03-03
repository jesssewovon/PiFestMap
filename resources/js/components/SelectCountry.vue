<template>
  <div>
    <div id="select-country" class="menu menu-box-bottom rounded-m" data-menu-height="365" data-menu-effect="menu-over" style="display: block; height: 390px;">
        <div class="menu-title">
            <h1 class="font-24 color-piketplace">{{ $t('message.select_country') }}</h1>
            <a href="#" class="close-menu"><i class="fa fa-times-circle"></i></a>
        </div>
        <div class="me-4 ms-3 mt-1">
            <div class="">
                <input type="text" :placeholder="$t('message.search_country')" v-model="keyword" style="display: inline;width: 100%;height: 35px;background-color: rgba(255,255,255,0.7);border-radius: 21px;padding-right: 25px;padding-left: 10px;"><i class="fas fa-search" style="margin-left: -20px;"></i>
            </div>
            <div class="list-group list-custom-small" v-if="countries.length > 0" style="overflow-y: scroll;height: 300px;margin-bottom: 26px;">
                <a v-if="show_all" v-on:click="select_country('all')">
                    <strong>{{$t('message.show_all')}} *</strong>
                    <i style="line-height: 34px;opacity: 1;">
                        <img src="/site_images/w.png" style="width: 13px;height: 13px;margin-right: 0">
                    </i>
                </a>
                <a class="close-menull" v-for="country in countries" :key="country.id" v-on:click="select_country(country.code)">
                  <span>{{country.name}}</span>
                  <i v-html="$functions.isoToEmoji(country.code)" style="line-height: 34px;opacity: 1;"></i>
                </a>
            </div>
            <div class="clear"></div>
        </div>
        <input type="hidden" id="caller_id" name="">
    </div>
    <div id="select-city" class="menu menu-box-bottom rounded-m" data-menu-height="365" data-menu-effect="menu-over" style="display: block; height: 390px;">
        <div class="menu-title">
            <h1 class="font-24 color-piketplace">{{ $t('message.select_city') }}</h1>
            <a href="#" class="close-menu"><i class="fa fa-times-circle"></i></a>
        </div>
        
        <div class="me-4 ms-3 mt-2">
            <div class="">
                <input type="text"  v-model="keyword_city" style="display: inline;width: 100%;height: 35px;background-color: rgba(255,255,255,0.7);border-radius: 21px;padding-right: 25px;padding-left: 10px;"><i class="fas fa-search" style="margin-left: -20px;"></i>
            </div>
            <div class="list-group list-custom-small" v-if="cities.length > 0" style="overflow-y: scroll;height: 300px;">
                <a v-for="city in cities" :key="city.id" v-on:click="select_city(city.name)">
                  
                  <!-- <span>{{country.name}}</span> -->
                  <span>{{city.name}}</span>
                  <!-- <i class="fa fa-check-circle"></i> -->
                </a>
            </div>
            <div class="clear"></div>
        </div>
        <input type="hidden" id="caller_idl" name="">
    </div>
  </div>
</template>

<script>
    //import file_countries from "../Countries-States-Cities/countries.json";
    //import file_states from "../Countries-States-Cities/states.json";
    //import file_cities from "../Countries-States-Cities/cities_country.json";
    //import file_countries_cities from "../Countries-States-Cities/countries+cities.json";

    import {mapActions, mapState} from "vuex";

export default {
    props:{
        show_all: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            keyword: null,
            keyword_city: null,
            countries: [],
            cities: [],
            code: '',
        };
    },
    computed: {
      ...mapState(['isLoggedIn', 'country_cities']),
      user:{
          get(){
              return this.$store.state.user
          },
          set(val){
              this.$store.state.user = val
          }
      },
      countries_db:{
          get(){
              return this.$store.state.countries_db
          },
          set(val){
              this.$store.state.countries_db = val
          }
      },
      locale:{
          get(){
              return this.$store.state.locale
          },
          set(val){
              this.$store.state.locale = val
          }
      }
    },
    mounted() {
        //this.$store.dispatch('set_countries', [])
        /*this.countries_db.forEach((val, index) => {
            val.translations.en = val.name
        })*/
        this.newCountryLoading()
    },
    watch: {
        keyword(after, before) {
            //this.getResults();
            //this.search();
            this.newCountryLoading()
        },
        keyword_city(after, before) {
            //this.getResults();
            this.search_city();
        },
        locale(n, o){
            this.newCountryLoading()
        },
        countries_db(n, o){
            this.newCountryLoading()
        },
        country_cities(n, o){
            this.cities = this.country_cities
        }
    },
    methods: {
        newCountryLoading() {
            /*this.countries_db.forEach((val, index) => {
                val.translations.en = val.name
            })*/
            this.countries = this.countries_db
            this.countries.forEach((val, index) => {
                let nm = val.translations[this.locale]
                val.name = nm==undefined?val.translations.en:nm
                val.sortname = this.$functions.removeAccentCharacter(val.name.toLowerCase())
            })
            this.countries.sort((a, b) => {
              var nameA = a.sortname.toLowerCase(); // ignore upper and lowercase
              var nameB = b.sortname.toLowerCase(); // ignore upper and lowercase
              if (nameA < nameB) {
                return -1;
              }
              if (nameA > nameB) {
                return 1;
              }
              // names must be equal
              return 0;
            });
            if (this.keyword && this.keyword != '') {
                this.countries=this.countries.filter((val) => {
                    return val.sortname.toUpperCase().includes(this.keyword.trim().toUpperCase())
                })
            }
            
            //console.log('jhjhjhh', file_countries_cities)
            //this.countries = file_countries_cities
        },
        getResults() {
            //let locale = this.user.locale;
            let locale = this.$i18n.locale;
            axios.get('/api/v1/livesearch-country', { params: { keyword: this.keyword, locale } })
                .then(res => {
                    this.countries = res.data
                })
                .catch(error => {});
            require('../../../public/template/scripts/bootstrap.min.js');
            
            const _0x77b9x42 = document[_0x962d[24]](_0x962d[119]);
            _0x77b9x42[_0x962d[22]]((_0x77b9xc) => {
                return _0x77b9xc[_0x962d[37]](_0x962d[64], (_0x77b9xb) => {
                    const _0x77b9x37 = document[_0x962d[24]](_0x962d[101]);
                    for (let _0x77b9xa = 0; _0x77b9xa < _0x77b9x37[_0x962d[12]]; _0x77b9xa++) {
                        _0x77b9x37[_0x77b9xa][_0x962d[4]][_0x962d[18]](_0x962d[16]);
                    }
                    for (let _0x77b9xa = 0; _0x77b9xa < _0x77b9x36[_0x962d[12]]; _0x77b9xa++) {
                        _0x77b9x36[_0x77b9xa][_0x962d[20]][_0x962d[112]] = _0x962d[115] + 0 + _0x962d[114];
                    }
                });
            });
        },
        getCities(country_code) {
            //let locale = this.user.locale;
            let locale = this.$i18n.locale;
            axios.get('/api/v1/get-cities-by-country/'+country_code)
                .then(res => {
                    console.log('xwdfsdq', res.data)
                    this.country_cities = res.data.cities
                    this.cities = res.data.cities
                })
                .catch(error => {});
        },
        search(){
            this.countries = this.countries_db.countries.filter(el => el.name.toLowerCase().includes(this.keyword.toLowerCase()));
        },
        search_city(){
            this.cities = this.country_cities.filter(el => el.sortname.toLowerCase().includes(this.keyword_city.trim().toLowerCase()));
        },
        select_country(code){
            if (code == 'all') {
                this.$emit('propagation', 'all')
            }else{
                let index = this.countries_db.findIndex(el => el.code === code);
                this.$emit('propagation', this.countries_db[index])
                //If the new country choosed # from the old one
                if (this.code != code) {
                    this.$emit('propagationCity', '')
                }
                this.code = code
                //this.getCities(code)
                this.cities = []
                this.$store.dispatch('getCities', {code:code, self:this})
            }
            
            this.$hide_modal.hide_modal('select-country');
        },
        select_city(city){
            this.$emit('propagationCity', city)
            this.$hide_modal.hide_modal('select-city');
        },
        loadCitiesWithCountryCode(code){
            this.select_country(code)
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