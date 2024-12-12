<template>
    <div class="star-rating">
        <div class="star"
             v-for="n in max"
             :class="{'active': n <= rating }"
            :key="n"
        >
        </div>
    </div>
</template>

<script>
export default {
    name: "StarRating",
    props: {
        current: Number,
        max: {
            type: Number,
            default: 5
        },
    },
    computed: {
        rating() {
            let toStr = this.current + ''

            return Math.round(Number(toStr.replaceAll(',', '.')))
        }
    }
}
</script>

<style lang="scss" scoped>
.star-rating {
    width: 100%;
    position: relative;
    display: flex;
    justify-content: space-between;

    .star {
        width: 18px;
        height: 18px;
        background: get-icon('star', $Zinc-400) no-repeat center;

        &.active {
            background: get-icon('star', $Red-400) no-repeat center;
        }
    }
}
</style>