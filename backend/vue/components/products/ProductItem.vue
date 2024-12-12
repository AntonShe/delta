<template>
    <li class="products__item">
        <div class="products__field">
            <img class="products__image" :src=" data.cover" alt="Изображение книги">
        </div>
        <div class="products__field">
            {{ data.id }} {{ data.labirintId ? `(${data.labirintId})` : '' }}
        </div>
        <div class="products__container">
            <div class="products__field products__field_mb">
                {{ data.title }}
            </div>
            <div class="products__field">
                {{ data.authors }}
            </div>
        </div>
        <div class="products__container">
            <div class="products__field">
                Цена: {{ data.price }} руб
            </div>
            <div class="products__field products__field_mb">
                Кол-во: {{ data.quantity }} шт
            </div>
            <div class="products__field">
                ISBN: {{ data.isbn }}
            </div>
        </div>
    </li>
</template>

<script>
export default {
    name: "ProductItem",
    emits: [''],
    props: {
        data: {
            type: Object
        }
    },
}
</script>

<style lang="scss">
    .products {
        &__item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: solid 1px $gray;
            cursor: pointer;

            &:hover {
                background-color: lightgray;
                transition: .2s;
            }
        }

        &__field {
            min-width: 150px;

            &_mb {
                margin-bottom: 20px;
            }
        }

        &__image {
            max-height: 150px;
        }
    }
</style>