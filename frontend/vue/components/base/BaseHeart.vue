<template>
    <div class="button-like" :class="{
                'button-like-active': getIsFavourite
			}"
         @click="change"
    ></div>
</template>

<script>
export default {
    name: "BaseHeart",
    props: {
        isFavourite: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            isActive: this.isFavourite
        }
    },
    methods: {
        change() {
            this.isActive = !this.isActive;
            this.$emit('toggle', this.isActive);
        }
    },
    computed: {
        getIsFavourite() {
            return this.isActive;
        }
    }
}
</script>

<style scoped>
.button-like {
    position: inherit;
}
</style>