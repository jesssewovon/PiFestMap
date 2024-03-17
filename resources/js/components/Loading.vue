<template>
    <div v-if="deleting || saving || connecting || disconnecting || isSearching" class="loader-background" style="z-index: 11;">
        <div style="text-align: center;width: 100%;margin-top: 150px;">
            <img src="/site_images/transparent-gif/loading3.gif" style="width: 75px;margin-top: 20px;border-radius: 10%;">
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
        computed: {
            ...mapState(['saving', 'connecting', 'disconnecting', 'deleting', 'isSearching']),
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
        }
    }

    
</script>

<style scoped>
    
</style>
