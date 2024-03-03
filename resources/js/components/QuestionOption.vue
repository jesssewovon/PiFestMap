<template>
    <div id="option" class="menu menu-box-modal rounded-m app-background-color" data-menu-height="300" data-menu-width="350" style="display: block; width: 350px; height: 300px;">
        <h1 class="text-center mt-4">
            <i class="fa fa-3x fa-info-circle scale-box color-blue-dark shadow-xl rounded-circle app-color"></i>
        </h1>
        <h3 class="text-center mt-3 font-700 app-color">{{$t('message.confirmation.you_sure')}}</h3>
        <p class="boxed-text-xl opacity-70 message app-color">
            You can continue with your previous actions.<br> Easy to attach these to success calls.
        </p>
        <div class="row mb-0 me-3 ms-3">
            <div class="col-6">
                <a @click="$hide_modal.all" class="btn close-menu btn-full btn-m app-border app-color font-600 rounded-s">{{$t('message.confirmation.no_cancel')}}</a>
            </div>
            <div class="col-6">
                <a @click="$emit('continue')" class="btn close-menu btn-full btn-m app-dark-background app-color-light font-600 rounded-s">{{$t('message.confirmation.yes_continue')}}</a>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    export default {
        data: function () {
            return {
                
            }
        },
    }
</script>