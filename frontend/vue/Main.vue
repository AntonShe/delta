<script>
import {ref, provide} from 'vue'

export default {
    name: "Main",
    setup() {
        const windowWidth = ref(window.innerWidth)

        provide('WindowWidth', windowWidth)

        return {windowWidth}
    },
    mounted() {
        this.$nextTick(() => {
            window.addEventListener('resize', this.onResize);
        })
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.onResize);
    },
    methods: {
        onResize() {
            this.windowWidth = window.innerWidth
        }
    }
}
</script>

<template>
    <router-view/>
</template>