<template>
    <div>
        <div :class="className">
            <!-- <ins
            class="adsbygoogle"
            :style="{display: 'block'}"
            :data-ad-client="googleAdId"
            :data-ad-slot="slotter"
            data-ad-format="auto"
            data-full-width-responsive="true"
            ></ins> -->

            <ins v-if="type=='general'" class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-3962442438023665"
             data-ad-slot="3809835226"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
            <ins v-else-if="type=='produits'" class="adsbygoogle"
                style="display:block"
                data-ad-format="fluid"
                data-ad-layout-key="+28+rt+12-5t+94"
                data-ad-client="ca-pub-3962442438023665"
                data-ad-slot="8552301100"></ins>
            <ins v-else-if="type=='shippings'" class="adsbygoogle"
             style="display:block"
             data-ad-format="autorelaxed"
             data-ad-client="ca-pub-3962442438023665"
             data-ad-slot="9955946331"></ins>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        timeout: {
            type: Number,
            default: 200,
        },
        type: {
            type: String,
            default: 'general',
        },
        className: String,
        slotter: String
    },
    data() {
        return {
            googleInit: null,
            googleAdId: "ca-pub-3962442438023665"
        }
    },

    mounted() {
      	let timeout = 200;
        if(this.timeout) timeout = this.timeout;
        this.googleInit = setTimeout(() => {
          if (typeof window !== "undefined")
            (window.adsbygoogle = window.adsbygoogle || []).push({});
        }, timeout);
    },
    destroyed() {
        if (this.googleInit) clearTimeout(this.googleInit);
    }

}
</script>

<style>
    .ad-border {
        padding:10px;
        border: 1px solid #eee;
    }

    .ad-noborder {
        padding:10px;
    }
</style>
