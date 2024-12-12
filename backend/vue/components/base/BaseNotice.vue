<template>
    <div class="notice"
         :class="getClass"
    >
        <div class="notice__text">{{text}}</div>
    </div>
</template>

<script>
export default {
    name: 'BaseNotice',
    props: {
        showTime: {
            type: Number,
            default: 3000
        }
    },
    data() {
        return {
            text: '',
            type: 'success',
            isVisible: false,
            time: this.showTime
        }
    },
    expose: ['show'],
    computed: {
        getClass() {
            return 'notice-' + this.type + (' notice-' + (this.isVisible ? 'render' : 'hide'))
        }
    },
    methods: {
        show(type = 'success', text = '') {
            this.text = text
            this.type = type
            this.isVisible = true

            let timer = setTimeout(() => {
                this.isVisible = false
                clearInterval(timer)
            }, this.time)

        }
    }
}
</script>

<style lang="scss" scoped>
    .notice {
        position: fixed;
        top:80px;
        right: 0px;
        transition: .5s;

        &-render {
            right: 0px;
        }

        &-hide {
            right: -100%;
        }

        &-success {
            background-color: #1bdf84;
        }

        &-warning {
            background-color: #ff8d2b;
        }

        &-danger {
            background-color: #ff3a3a;
        }

        &__text {
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            padding: 15px  40px;
        }
    }
</style>