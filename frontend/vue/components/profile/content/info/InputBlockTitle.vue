<template>
    <div class="title-min">{{ title }}</div>
</template>

<script>
export default {
    name: "InputBlockTitle",
	props: {
        title: String
	}
}
</script>

<style lang="scss" scoped>
.title-min {
    margin-bottom: 10px;
    @include _typography-ext(fn, 20, 28, 600, ls, $Zinc-900);

    @media (max-width: $screen-md) {
        @include _typography-ext(fn, 18, 24, 600, ls, $Zinc-900);
    }
}
</style>