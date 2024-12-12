<template>
    <div class="genres">
        <div class="genres__form" v-if="selectGenre.id">
            <base-field
                name="genres"
                id="genres"
                class="genres__input genres__input_small genres__input_mb"
                type="text"
                placeholder="Название раздела для создания"
                :mask="textMask"
                v-model="genreName"
                @update-input-value="setGenreName"
            />
            <button class="button button_flex-grow-1 genres__create-button"
                    :class="{'button_disabled': !this.selectGenre}"
                    @click="createGenre"
                    :disabled="!this.selectGenre"
            >
                Создать подраздел
            </button>
            <button class="button button_bg-color_red"
                    :class="{'button_disabled': !this.selectGenre}"
                    @click="deleteGenre"
                    :disabled="!this.selectGenre"
            >
                Удалить раздел
            </button>
        </div>
        <ul class="genres">
            <base-genres-tree
                :genres="genresForTree"
                @set-genre="(genre) => {this.$emit('setGenre', genre)}"
                @unset-genre="this.$emit('unsetGenre')"
            />
        </ul>
    </div>
</template>

<script>
import BaseGenresTree from "./BaseGenresTree";
export default {
    name: "BaseGenres",
    components: {
        BaseGenresTree
    },
    emits: ['setGenre', 'unsetGenre', 'createGenre', 'deleteGenre', 'setErrors'],
    inject: ['selectGenre', 'genresForTree'],
    data() {
        return {
            genreName: '',
            textMask: {
                mask: /^[0-9A-Za-zА-Яа-яЁё\-\(\)\.\,\!\"\:\'\s]+$/,
            }
        }
    },
    methods: {
        setGenreName(name) {
            this.genreName = name
        },
        createGenre() {
            if (this.genreName && this.selectGenre) {
                let data = {
                    name: this.genreName,
                    parentId: this.selectGenre.id
                }

                axios
                    .post('/admin/backend/genre', data)
                    .then(response => {
                        if (response.data.errors) {
                            this.$emit('setErrors', response.data.errors)
                        } else {
                            this.$emit('createGenre')
                        }
                    })
                    .catch(error => {
                        this.$emit('setErrors', error)
                    })
            }
        },
        deleteGenre() {
            if (this.selectGenre.children) {
                this.$emit('setErrors', 'Сначала удалите поджанры')
                return
            }
            axios
                .delete(`/admin/backend/genre?id=${this.selectGenre.id}`)
                .then(response => {
                    if (response.data.errors) {
                        this.$emit('setErrors', response.data.errors)
                    } else {
                        this.$emit('deleteGenre')
                    }
                })
                .catch(error => {
                    this.$emit('setErrors', error)
                })
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

    &__input {
        flex-grow: 1;

        &_small {
            width: 440px;
        }

        &_mb {
            margin-bottom: 10px;
        }
    }

    &__form {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        width: 440px;
        margin-bottom: 20px;
    }

    &__create-button {
        margin-right: 20px;
    }
}
</style>