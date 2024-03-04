<template>
	<SelectBusinessType />
	<SwitchLanguage />
	<MessageBoxes />
	<Loading />
	<vue3-confirm-dialog></vue3-confirm-dialog>
    <router-view/>
</template>

<script setup>
	import Header from './components/Header.vue'
	import FooterMenu from './components/FooterMenu.vue'
	import SelectBusinessType from './components/SelectBusinessType.vue'
	import SwitchLanguage from './components/SwitchLanguage.vue'
	import MessageBoxes from './components/MessageBoxes.vue'
	import Loading from './components/Loading.vue'

    import { ref, onMounted } from 'vue'

    onMounted(() => {
    })
</script>