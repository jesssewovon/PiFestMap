<template>
	<div>
		<div id="info" class="menu menu-box-modal app-background-color rounded-m" data-menu-height="310" data-menu-width="350" style="display: block; width: 350px; height: 310px;">
			<h1 class="text-center mt-4">
				<i class="fa fa-3x fa-info-circle scale-box color-white shadow-xl rounded-circle app-color"></i>
			</h1>
			<h1 class="text-center mt-3 font-700 color-white title app-color">All's Good</h1>
			<p class="boxed-text-l color-white opacity-70 message app-color">
				You can continue with your previous actions.<br> Easy to attach these to success calls.
			</p>
			<a href="#" class="close-menu btn btn-m btn-center-m button-s shadow-l rounded-s text-uppercase font-600 color-white btn-text app-dark-background">
				Great, Thanks!
			</a>
		</div>
		<div id="success" class="menu menu-box-modal app-background-color rounded-m" data-menu-height="310" data-menu-width="350" style="display: block; width: 350px; height: 310px;">
			<h1 class="text-center mt-4">
				<i class="fa fa-3x fa-check-circle scale-box color-white shadow-xl rounded-circle app-color"></i>
			</h1>
			<h1 class="text-center mt-3 font-700 color-white title app-color">All's Good</h1>
			<p class="boxed-text-l color-white opacity-70 message app-color">
				You can continue with your previous actions.<br> Easy to attach these to success calls.
			</p>
			<a href="#" class="close-menu btn btn-m btn-center-m button-s shadow-l rounded-s text-uppercase font-600 color-white btn-text app-dark-background">
				Great, Thanks!
			</a>
		</div>
		<div id="error" class="menu menu-box-modal app-background-color rounded-m" data-menu-height="310" data-menu-width="350" style="display: block; width: 350px; height: 310px;">
			<h1 class="text-center mt-4">
				<i class="fa fa-3x fa-times-circle scale-box color-white shadow-xl rounded-circle app-color"></i>
			</h1>
			<h1 class="text-center mt-3 font-700 color-white title app-color">All's Good</h1>
			<p class="boxed-text-l color-white opacity-70 message app-color">
				You can continue with your previous actions.<br> Easy to attach these to success calls.
			</p>
			<a href="#" class="close-menu btn btn-m btn-center-m button-s shadow-l rounded-s text-uppercase font-600 color-white btn-text app-dark-background">
				Great, Thanks!
			</a>
		</div>
		<div id="menu-success-modal" class="menu menu-box-modal bg-green-dark rounded-m" data-menu-height="310" data-menu-width="350" style="display: block; width: 350px; height: 310px;">
			<h1 class="text-center mt-4">
				<i class="fa fa-3x fa-check-circle scale-box color-white shadow-xl rounded-circle"></i>
			</h1>
			<h1 class="text-center mt-3 font-700 color-white title">All's Good</h1>
			<p class="boxed-text-l color-white opacity-70 message">
				You can continue with your previous actions.<br> Easy to attach these to success calls.
			</p>
			<a href="#" class="close-menu btn btn-m btn-center-m button-s shadow-l rounded-s text-uppercase font-600 bg-white color-black btn-text">
				Great, Thanks!
			</a>
		</div>
		<div id="menu-success-box-sheet" class="menu menu-box-bottom bg-green-dark rounded-m" data-menu-height="335" data-menu-effect="menu-over" style="display: block; height: 335px;">
			<h1 class="text-center mt-4"><i class="fa fa-3x fa-check-circle scale-box color-white shadow-xl rounded-circle"></i></h1>
			<h1 class="text-center mt-3 font-700 color-white title">All's Good</h1>
			<p class="boxed-text-l color-white opacity-70 message">
			You can continue with your previous actions.<br> Easy to attach these to success calls.
			</p>
			<a href="#" class="close-menu btn btn-m btn-center-m button-s shadow-l rounded-s text-uppercase font-600 bg-white color-black">Great, Thanks!</a>
		</div>
		<div id="menu-warning-1" class="menu menu-box-bottom rounded-m" data-menu-height="185" data-menu-effect="menu-over" style="display: block; height: 185px;">
			<div class="menu-title">
				<i class="fa fa-times-circle float-start me-3 scale-box ms-3 fa-4x color-red-dark"></i>
				<p class="color-highlight">The task has</p>
				<h1>Failed</h1>
				<a href="#" class="close-menu"><i class="fa fa-times-circle"></i></a>
			</div>
			<div class="content mt-0 mb-0">
				<p class="pe-3">
				This task has failed. This message can now be dismissed manually or programatically.
				</p>
			</div>
		</div>
		<div id="menu-warning-modal" class="menu menu-box-modal bg-red-dark rounded-m" data-menu-height="310" data-menu-width="350" style="display: block; width: 350px; height: 310px;">
			<h1 class="text-center mt-4">
				<i class="fa fa-3x fa-times-circle scale-box color-white shadow-xl rounded-circle"></i>
			</h1>
			<h1 class="text-center mt-3 text-uppercase color-white font-700 title">Wooops!
			</h1>
			<p class="boxed-text-l color-white opacity-70 message">
				You can continue with your previous actions.<br> Easy to attach these to success calls.
			</p>
			<a href="#" class="close-menu btn btn-m btn-center-l button-s shadow-l rounded-s text-uppercase font-600 bg-white color-black btn-text">Hmmm, Check again?
			</a>
		</div>
		<div id="comment_sent" class="menu menu-box-modal rounded-m" data-menu-hide="1000" data-menu-width="220" data-menu-height="160" style="display: block; width: 220px; height: 160px;">
			<h1 class="text-center fa-5x mt-2 pt-3 pb-2"><i class="fa fa-check-circle color-piketplace"></i></h1>
			<h2 class="text-center">{{ $t('message.sent') }}</h2>
		</div>
		<div id="saved" class="menu menu-box-modal rounded-m" data-menu-hide="1000" data-menu-width="220" data-menu-height="160" style="display: block; width: 220px; height: 160px;">
			<h1 class="text-center fa-5x mt-2 pt-3 pb-2"><i class="fa fa-check-circle color-green-dark"></i></h1>
			<h2 class="text-center">{{ $t('message.saved') }}</h2>
		</div>
		<div id="added_to_chart" class="menu menu-box-modal rounded-m" data-menu-hide="1000" data-menu-width="220" data-menu-height="160" style="display: block; width: 220px; height: 160px;">
		    <h1 class="text-center fa-5x mt-2 pt-3 pb-2">
		    	<i class="fa fa-shopping-bag color-piketplace"></i>
		    </h1>
		    <h2 class="text-center">{{ $t('message.cart.added_to_cart') }}</h2>
		</div>

		<div id="menu-share-thumbs" class="menu menu-box-modal rounded-m" data-menu-height="320" data-menu-width="350" style="display: block; width: 350px; height: 320px;">
			<div class="menu-title">
				<!-- <p class="color-highlight">Tap to</p> -->
				<h1>{{$t('message.menu')}}</h1>
				<a href="#" class="close-menu"><i class="fa fa-times-circle"></i></a>
			</div>
			<div class="divider divider-margins"></div>
			<div class="d-flex">
				<div class="ms-auto me-auto">
					<router-link to="/profil">
					<img src="/pi.png" class="profil" style="width: 55px;height: 55px;border-radius: 50%;object-fit: cover;">
					</router-link>
					<p class="font-11 text-center opacity-70">{{$t('message.edit_profil')}}</p>
				</div>
				<div class="me-auto">
					<router-link to="/my-store" class="shareToTwitter external-link icon icon-l bg-red-light rounded-s shadow-xl"><i class="fas fa-store font-22"></i><br></router-link>
					<p class="font-11 text-center opacity-70">{{$t('message.side_menu.my_store')}}</p>
				</div>
				<div class="me-auto">
					<router-link to="/my-orders" class="external-link icon icon-l bg-green-light rounded-s shadow-xl"><i class="fa fa-list-alt font-22"></i><br></router-link>
					<p class="font-11 text-center opacity-70">{{$t('message.side_menu.my_orders')}}</p>
				</div>
			</div>
		</div>
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
            test(){
            	alert('testtt')
            }
        }
    }

    
</script>