<template>
	<div class="balance">
        <ProfileTitle title="Баланс" :isShowAlways=true />
        <div class="balance__card" v-if="cardParams">
            <div class="balance__card-sum">
                {{ getSum }} ₽
            </div>
            <div class="balance__card-title">
                {{ cardParams.title }}
            </div>
            <div class="balance__card-text">
                {{ cardParams.text }}
            </div>
            <div class="balance__card-button">
                <button @click="showModal">
                    {{ cardParams.button }}
                </button>
            </div>

            <BaseModal :isOpen="isShowModal" @close="hideModal" :withCross="false">

                    <template v-slot:title>
                        <button class="balance__popup-back" @click="hideModal">
                            {{ cardParams.modal.title }}
                        </button>
                    </template>

                <div class="balance__popup">
                    <div class="balance__popup-text">Скачайте заявление и отправьте скан нам на почту</div>
                    <router-link :to="cardParams.modal.link" class="balance__popup-button">
                        {{ cardParams.modal.button }}
					</router-link>
                </div>
            </BaseModal>

            <BasePreloader :isShow="isShowPreloader"/>
        </div>
	</div>
</template>

<script>
import axios from "axios";
import ProfileTitle from "./ProfileTitle.vue";
export default {
    name: "ProfileBalance",
    components: {ProfileTitle},
	data() {
        return {
            balance: {},
			physical: {
                title: 'Возвратные средства на счет',
                text: 'Данные средства вы можете использовать как на покупки в магазине, так и вывести на карту',
                button: 'Вывести на карту',
                modal: {
                    title: 'Вывести средства на счет',
					button: 'Вывести на карту',
                }
			},
            legal: {
                title: 'Возвратные средства на счет',
                text: 'Данные средства вы можете использовать как на покупки в магазине, так и вывести на расчётный счёт',
                button: 'Вывести на расчетный счёт',
				modal: {
                    title: 'Вывести средства на расчетный счет',
                    button: 'Скачать заявление',
				}
            },
			cardParams: null,
			isShowModal: false,
			modalWithCross: false,
            isShowPreloader: true
		}
	},
	beforeMount() {
        this.getBalance();
    },
	methods: {
        getBalance() {
            axios.get('/get-profile-balance').then(response => response.data)
                .then(result => {
                    this.balance = result;

                    this.cardParams = (this.balance.isLegal) ? this.legal : this.physical;
                    this.cardParams.modal.link = result.link;

                    this.isShowPreloader = false;
                });
		},
		showModal() {
			this.isShowModal = true;
        },
        hideModal() {
            this.isShowModal = false;
		},
        download() {
            let link = this.cardParams.modal.link;

            console.log('тут должна быть ссылка на скачивание');
		},
	},
	computed: {
        getSum() {
            return (this.balance) ? this.balance.sum : 0;
		}
	}
}
</script>

<style lang="scss" scoped>
.balance {
    &__card {
        padding: 20px;
        max-width: 524px;
        @include _border-radius(8px);
        background-color: $white;
        border: 1px solid $Zinc-200;

        &-sum {
            margin-bottom: 12px;
            @include _typography-ext(fn, 20, 28, 600, ls, $Red-400);
        }

        &-title {
            margin-bottom: 4px;
            @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);
        }

        &-text {
            margin-bottom: 12px;
            @include _typography-ext(fn, 14, 20, 400, ls, $Zinc-500);
        }

        &-button {
            button {
                @include _button-reset(9px 16px, $Zinc-700, none);
                @include _border-radius(8px);
                @include _typography-ext(fn, 16, 26, 600, ls, $white);

                @media (max-width: $screen-md) {
                    width: 100%;
                }
            }
        }
    }

    &__popup {
        display: flex;
        flex-direction: column;

        &-back {
            @include _button-reset(0 28px, bgc, none);
            background: get-icon('tick-left', $Zinc-900) no-repeat left;
            cursor: pointer;
        }

        &-text {
            margin-bottom: 20px;

            @media (min-width: $screen-md) {
                margin-bottom: 34px; 
            }
        }

        &-button {
            display: block;
            align-self: end;
            width: 100%;
            background-color: $Zinc-700;
            @include _typography-ext(fn, 16, 26, 600, ls, $white);
            padding: 9px 16px;
            border: none;
            @include _border-radius(8px);
            text-align: center;

            @media (min-width: $screen-md) {
                width: 186px;
                text-align: left;
            }
        }
    }
}
</style>