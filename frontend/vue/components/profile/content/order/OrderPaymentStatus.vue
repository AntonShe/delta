<template>
	<div class="status">
		<div v-if="isNeedLink" class="link">
            <span :class="['link__btn', {'disabled': isTransactionDisabled}, {'link__btn_big': isBig}]" @click.stop="sendTransaction">
                {{ getTitle }}
            </span>
		</div>
		<div v-else>
			<div v-if="isDetail" class="badge" :class="getClass('badge')">
				{{ getTitle }}
			</div>
			<div v-else class="text" :class="getClass('text')">
				{{ getTitle }}
			</div>
		</div>
	</div>
</template>

<script>
import axios from "axios"

export default {
    name: "OrderPaymentStatus",
	props: {
        status: Number,
        link: String,
		isLegal: {
            type: Boolean,
			default: false
		},
		isDetail: {
            type: Boolean,
			default: true
		},
        isTransactionDisabled: {
            type: Boolean,
            default: false
        },
        isBig: {
            type: Boolean,
            default: false
        },
        isShortText: {
            type: Boolean,
            default: false
        },
        orderNumber: {
            type: Number,
            default: 0
        }
	},
	data() {
        return {
            statusList: {
                0: {
                    title: {
                        physical: this.isShortText ? 'Оплатить онлайн' : 'Оплатить картой онлайн',
                        legal: 'Счет для оплаты',
					},
                    class: {
                        text: 'text__orange',
						badge: 'badge__orange'
					},
				},
                1000: {
                    title: 'Ожидает оплаты',
                    class: {
                        text: 'text__rose',
                        badge: 'badge__rose'
                    },
				},
                1: {
                    title: 'Создан',
                    class: {
                        text: 'text__indigo-600',
                        badge: 'badge__indigo-600'
                    },
				},
                2: {
                    title: 'Создан',
                    class: {
                        text: 'text__indigo-600',
                        badge: 'badge__indigo-600'
                    },
				},
                31: {
                    title: 'В обработке',
                    class: {
                        text: 'text__indigo-600',
                        badge: 'badge__indigo-600'
                    },
				},
                32: {
                    title: 'Сборка',
                    class: {
                        text: 'text__rose',
                        badge: 'badge__rose'
                    },
				},
                33: {
                    title: 'Отгружен, в пути',
                    class: {
                        text: 'text__indigo-400',
                        badge: 'badge__indigo-400'
                    },
				},
                34: {
                    title: 'Принят в курьерской службе',
                    class: {
                        text: 'text__indigo-400',
                        badge: 'badge__indigo-400'
                    },
				},
                36: {
                    title: 'Доступен к выдаче ПВЗ',
                    class: {
                        text: 'text__green',
                        badge: 'badge__green'
                    },
                },
                7: {
                    title: 'Выполнен',
                    class: {
                        text: 'text__green',
                        badge: 'badge__green'
                    },
				},
                8: {
                    title: 'Отменен',
                    class: {
                        text: 'text__zinc',
                        badge: 'badge__zinc'
                    },
				},
                9: {
                    title: 'Отменен магазином',
                    class: {
                        text: 'text__zinc',
                        badge: 'badge__zinc'
                    },
				},                
                100: {
                    title: 'Создан',
                    class: {
                        text: 'text__indigo-600',
                        badge: 'badge__indigo-600'
                    },
				},
			}
		}
	},
	computed: {
        isNeedLink() {
            return !_.isEmpty(this.link) && this.status === 0
        },
        getStatusConfig() {
            return this.statusList[this.status];
		},
        getTitle() {
            const status = this.getStatusConfig;

            if (typeof status.title === 'string') {
                return status.title;
			}

            return (this.isLegal) ? status.title.legal : status.title.physical;
		},
	},
	methods: {
        getClass(type) {
            return this.getStatusConfig.class[type];
        },
        sendTransaction() {
            if (this.isTransactionDisabled) return

            axios.get('/order/payment', { 
                params: {
                    trans_id: this.link,
                    orderNumber: this.orderNumber
                }
            })
            .then(response => {
                if (response.data.url) {
                    window.location.href = response.data.url
                }
            })
            .catch(error => {
                throw new Error('Ошибочная транзакция', error)
            })
        }
	}
}
</script>

<style lang="scss" scoped>
.status {
    width: 100%;
    .link__btn {
        @include _border-radius(8px);
        @include _typography-ext(fn, 16, 26, 600, ls, $Zinc-700);
        padding: 9px;            
        display: block;
        text-align: center;
        background-color: $Red-200;
        cursor: pointer;

        @media (min-width: $tablet-width) {
            width: 230px;
            max-width: 100%;
            min-width: 216px;
        }

        &.disabled {
            background-color: $Zinc-100;
            cursor: default;
        }
    }

    .text {
        @include _typography-ext(fn, 14, 20, 600, ls, fc);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;

        @media (min-width: $tablet-width) {
            font-size: 16px;
        }

        @media (min-width: $screen-xl-l) {
            font-size: 20px;
            line-height: 28px;
        }

        &__orange {
            color: $Orange-600;
        }

        &__rose {
            color: $Rose-400;
        }

        &__indigo-600 {
            color: $Indigo-600;
        }

        &__indigo-400 {
            color: $Indigo-400;
        }

        &__green {
            color: $Green-600;
        }

        &__zinc {
            color: $Zinc-400;
        }
    }

    .badge {
        padding: 6px 24px;
        @include _border-radius(100px);
        @include _typography-ext(fn, 14, 16, 600, ls, fc);
        text-align: center;
        width: fit-content;

        @media (min-width: $screen-md) {
            font-size: 18px;
            line-height: 24px;
        }

        &__orange {
            background-color: $Orange-50;
            color: $Orange-600;
        }

        &__rose {
            background-color: $Rose-50;
            color: $Rose-400;
        }

        &__indigo-600 {
            background-color: $Indigo-50;
            color: $Indigo-600;
        }

        &__indigo-400 {
            background-color: $Indigo-50;
            color: $Indigo-400;
        }

        &__green {
            background-color: $Green-50;
            color: $Green-600;
        }

        &__zinc {
            background-color: $Zinc-50;
            color: $Zinc-400;
        }
    }

    // themes
    .link__btn_big {
        @include _typography-ext(fn, 18, 24, 600, ls, $Zinc-900);
        width: 100%;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;

        @media (min-width: $tablet-width) {
            width: 300px;
        }
    }
}
</style>