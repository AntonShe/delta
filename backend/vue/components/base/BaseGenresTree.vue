<template>
    <li v-for="genre in list" class="genres__item" :key="genre.id">
        <div class="genres__content" :class="{'genres__content_padding': !genre.children}">
            <div v-if="genre.children"
                 class="genres__button"
                 :class="{'genres__button_active': this.getShow(genre.id)}"
                 @click="setShow(genre.id)"
            ></div>
            <div class="genres__text"
                 :class="{'genres__text_active': this.selectGenre.id && this.selectGenre.id === genre.id}"
                 @click="setGenre(genre)"
            >
                <b v-if="genre.isCourse">(К)</b> {{ genre.name }}
            </div>
        </div>
        <ul v-if="genre.children"
            class="genres__sub-item"
            :class="{'genres__sub-item_show': getShow(genre.id)}"
        >
            <base-genres-tree :genres="genre.children"
                              @set-genre="(val) => {this.$emit('setGenre', val)}"
                              @unset-genre="this.$emit('unsetGenre')"/>
        </ul>
    </li>
</template>

<script>

export default {
    name: "BaseGenresTree",
    emits: ['setGenre', 'unsetGenre'],
    inject: ['selectGenre'],
    props: {
        genres: {
            type: Array,
            default: []
        }
    },
    data() {
      return {
          showedIds: {},
          show: false,
          list: this.genres
      }
    },
    watch: {
        genres(value) {
            this.list = value
        }
    },
    methods: {
        setGenre(genre) {
            if (genre.id === this.selectGenre.id) {
                this.$emit('unsetGenre')
            } else {
                this.showedIds[genre.id] = true
                this.$emit('setGenre', genre)
            }
        },
        getShow(id) {
            return this.showedIds[id] === undefined ? false : this.showedIds[id]
        },
        setShow(id) {
            if (this.showedIds[id] !== undefined) {
                delete this.showedIds[id]
            } else {
                this.showedIds[id] = true
            }
        },
    },
}
</script>

<style lang="scss">
.genres{
    &__content {
        display: flex;

        &_padding {
            padding-left: 20px;
        }
    }

    &__button {
        position: relative;
        width: 20px;
        height: 20px;

        &::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 2.5px;
            border: 5px solid transparent;
            border-top: 10px solid green;
        }

        &_active {
            &::after {
                transform: rotate(180deg);
                top: unset;
                bottom: 2.5px;
            }
        }
    }

    &__text {
        cursor: pointer;
        flex-grow: 1;

        &_active {
            font-weight: bold;
        }
    }

    &__sub-item {
        display: none;
        padding-left: 20px;

        &_show {
            display: block;
        }
    }
}
</style>