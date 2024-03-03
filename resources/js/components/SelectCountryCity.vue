<template>
    <div>
        <div id="select-country-city" class="menu menu-box-bottom rounded-m" data-menu-height="365" data-menu-effect="menu-over" style="display: block; height: 365px;">
            <div class="content" id="tab-group-3">
                <div class="tab-controls tabs-small tabs-rounded" data-highlight="bg-green-dark">
                    <a href="#" class="no-effect" @click="country_tab_active=true">
                        {{$t('message.select_country')}}
                        <span v-if="country_tab_active" style="width: 80%;height: 3px;border-radius: 10px;background-color: #000;margin: auto;display: block;"></span>
                    </a>
                    <a href="#" class="no-effect no-click">
                        {{$t('message.select_city')}}
                        <span v-if="!country_tab_active" style="width: 80%;height: 3px;border-radius: 10px;background-color: #000;margin: auto;display: block;"></span>
                    </a>
                </div>
            </div>
            <input type="hidden" id="caller_id" name="">
            <div v-if="country_tab_active" class="me-4 ms-3 mt-2">
                <div class="">
                    <input type="text"  v-model="keyword" style="display: inline;width: 100%;height: 35px;background-color: rgba(255,255,255,0.7);border-radius: 21px;padding-right: 25px;padding-left: 10px;"><i class="fas fa-search" style="margin-left: -20px;"></i>
                </div>
                <div class="list-group list-custom-small" v-if="countries.length > 0" style="overflow-y: scroll;height: 300px;">
                    <a :data-country-code="country.code" :data-country-libelle="country.libelle" href="#" v-for="country in countries" :key="country.id" v-on:click="select_country(country.code)">
                      
                      <span>{{country['libelle'+($i18n.locale=='fr'?'':'En')]}}</span>
                      <i class="fa fa-check-circle"></i>
                    </a>
                </div>
                <div class="clear"></div>
            </div>
            <div v-else class="me-4 ms-3 mt-2">
                <div class="">
                    <input type="text"  v-model="keyword_city" style="display: inline;width: 100%;height: 35px;background-color: rgba(255,255,255,0.7);border-radius: 21px;padding-right: 25px;padding-left: 10px;"><i class="fas fa-search" style="margin-left: -20px;"></i>
                </div>
                <div class="list-group list-custom-small" v-if="countries.length > 0" style="overflow-y: scroll;height: 300px;">
                    <a :data-city-id="city.id" :data-city-libelle="city.city" href="#" class="close-menu" v-for="city in cities" :key="city.id" v-on:click="select_city(city.city)">
                      
                      <span>{{city.city}}</span>
                      <i class="fa fa-check-circle"></i>
                    </a>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</template>

<script>
    import file_countries from "../Countries-States-Cities/countries.json";
    import file_states from "../Countries-States-Cities/states.json";
    import file_cities from "../Countries-States-Cities/cities_country.json";
export default {
    data() {
        return {
            keyword: null,
            keyword_city: null,
            countries: [],
            selected_country_cities: [],
            cities: [],
            user: null,
            form: {
                country_code: "",
                country_name: "",
                city: "",
            },
            country_tab_active: true,
        };
    },
    mounted() {
        /*this.countries = file_countries.countries.sort(function (a, b) {
          return a.id - b.id;
        });*/
        //this.user = this.$session.get('user');
        this.getResults();
    },
    watch: {
        keyword(after, before) {
            this.getResults();
            //this.search();
        },
        keyword_city(after, before) {
            //this.getResults();
            this.search_city();
        }
    },
    methods: {
        getResults() {
            //let locale = this.user.locale;
            let locale = this.$i18n.locale;
            axios.get('/api/v1/livesearch-country', { params: { keyword: this.keyword, locale } })
                .then(res => this.countries = res.data)
                .catch(error => {});
            //require('../../../public/template/scripts/bootstrap.min.js');
            import.meta.glob('../../../public/template/scripts/bootstrap.min.js');
            
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
        search(){
            this.countries = file_countries.countries.filter(el => el.name.toLowerCase().includes(this.keyword.toLowerCase()));
        },
        search_city(){
            this.cities = this.selected_country_cities.filter(el => el.city.toLowerCase().includes(this.keyword_city.toLowerCase()));
        },
        select_country(code){
            this.selected_country_cities = file_cities.cities.filter(el => el.shortname === code);
            this.cities = this.selected_country_cities.sort(function(a, b) {
              var nameA = a.city.toUpperCase(); // ignore upper and lowercase
              var nameB = b.city.toUpperCase(); // ignore upper and lowercase
              if (nameA < nameB) {
                return -1;
              }
              if (nameA > nameB) {
                return 1;
              }
              // names must be equal
              return 0;
            });
            ///////////////////////////////////////////:
            let libelle = $('a[data-country-code='+code+']').attr('data-country-libelle')
            this.form.country_code = code;
            this.form.country_name = libelle;
            this.country_tab_active = false;
        },
        select_city(city){
            this.form.city = city
            this.country_city = this.form.country_name+", "+city
            this.$hide_modal.hide_modal('select-country-city');
            this.country_tab_active = true;
            this.setData()
        },
        setData(){
            let id_caller = $('#caller_id').val()
            $('#'+id_caller+'_country_selected_code').val(this.form.country_code)
            $('#'+id_caller+'_country_selected').val(this.form.country_name)
            //$('#country_selected_id').val(id)
            $('#'+id_caller+'_country_city_selected_label').html(this.form.country_name+", "+this.form.city)

            $('#'+id_caller+'_country_selected_city').val(this.form.city)
            $('#'+id_caller+'_country_address').val("");

            $('#'+id_caller+'_required-countries_id').css('display', 'none')
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