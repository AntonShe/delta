<template>
    <div class="add-address" @click="emmitClick">
        <div class="add-address__text">Добавить новый адрес</div>
    </div>
</template>

<script>
export default {
    name: 'AddNew',
    emits: ['clicked'],
    methods: {
        emmitClick() {
            this.$emit('clicked')
        }
    }
}
</script>

<style lang="scss" scoped>
.add-address {
    margin-left: 25px;
    padding: 10px 20px;
    max-width: 240px;
    width: 100%;
    text-align: center;
    @include _border-radius(8px);
    background-color: $Zinc-50;
    border: 1px solid $Zinc-200;
    cursor: pointer;

    &:hover {
        border-color: $Indigo-600;
    }
}
</style>