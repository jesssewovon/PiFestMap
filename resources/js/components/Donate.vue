<template>
	<div id="donate" class="menu menu-box-bottom rounded-m" data-menu-height="250" data-menu-effect="menu-over" style="display: block; height: 250px;">
        <div class="menu-title">
            <p class="color-highlight">{{$t('message.donate.donate_text')}},</p>
            <h1 class="font-24">{{$t('message.donate.donate')}}</h1>
            <a href="#" class="close-menu"><i class="fa fa-times-circle"></i></a>
        </div>
        <form class="content mt-0 mb-0">
            <div class="input-style no-borders has-icon mb-4" style="margin-bottom: 5px!important;">
                <i class="fas fa-donate"></i>
                <input type="name" class="form-control" id="amount" :placeholder="$t('message.donate.amount')" v-model="form.amount" v-on:input="clear_invalid">
                <i class="fa fa-times disabled invalid color-red-dark"></i>
                <i class="fa fa-check disabled valid color-green-dark"></i>
                <em>(required)</em>
            </div>
            <label id="invalid-value" class="color-red-dark font-11 font-500" style="display: none;">Invalid value</label>
            <button @click.prevent="pay" class="btn btn-full btn-m shadow-l rounded-s bg-highlight font-600 top-20">{{$t('message.donate.donate')}}</button>
        </form>
    </div>
</template>

<script>
    import axios from 'axios';

    export default{
        data: function () {
	        return {
	        	form: {
	        		amount: "",
	        	},
                user: {}
	        }
        },
        mounted() {
            this.user = this.$session.get('user');
        },
        methods: {
        	pay(){
                let amount = this.form.amount;
                //alert(parseFloat(amount));
                amount = amount.replaceAll(',', '.');
                let amount_converted = parseFloat(amount);
                if (amount == '' || amount_converted==0 || isNaN(amount_converted) || amount!=amount_converted) {
                    //alert('here');
                    $('#invalid-value').css('display', 'block');
                    return;
                }
                let data = {session: this.$session};
                this.$auth.authenticate(data);
                let dataPay = {
                    userId: this.user.id,
                    uniqueId: this.uniqueId,
                    type: 'donation',
                    memo: this.$t('message.side_menu.donate'),
                    amount: amount_converted,
                };
                this.form.amount = "";
                this.$hide_modal.hide_modal('donate');
                this.$pay.payment(dataPay);
            },
            clear_invalid(){
                $('#invalid-value').css('display', 'none');
            }
        }
    }
</script>