<template>
    <div v-if="count > 0" class="messages__container" :style="`z-index: ${level * 100}`">
        <div v-for="(item, key) in list"
             :key="key"
             :class="`messages__text messages__text_${item.type}`"
             @click="close(key)"
             title="Закрыть"
        >
            {{ item.text }}
        </div>
        <div v-if="count > 1" class="messages__text messages__text_hovered" @click="closeAll">
            Закрыть все сообщения
        </div>
    </div>
</template>

<script>
export default {
    name: "BaseMessage",
    emits: ['close', 'closeAll'],
    props: {
        messages: {},
        type: {
            type: String,
            default: 'success'
        },
        level: {
            type: Number,
            default: 4
        },
    },
    data() {
        return {
            list: [],
        }
    },
    computed: {
        count() {
            return this.list.length
        }
    },
    watch: {
        messages(value) {
            if (_.isString(value)) {
                value = [value]
            }

            this.list = _.map(value, item => {
                return {
                    text: item,
                    type: this.type
                }
            })
        }
    },
    methods: {
        close(key) {
            this.$emit('close', key)
        },
        closeAll() {
            this.$emit('closeAll')
        },
    },
}
</script>

<style lang="scss">
.messages {
    &__container {
        position: fixed;
        top: 70px;
        right: 0;
        min-width: 200px;
        max-width: 40vw;
        border: 1px solid $gray;
    }

    &__text {
        width: 100%;
        padding: 10px;
        background-color: $white;
        cursor: pointer;

        &_success {
            background-color: $green;
        }

        &_error {
            background-color: $red;
        }

        &_hovered {
            transition: .3s;

            &:hover{
                background-color: $lightgray;
            }
        }
    }
}

</style>