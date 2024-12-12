<template>
	<div class="order-step">
        <OrderStepTitle :number="2" title="Получатель" :is-active="true"/>
        <div v-if="userInfo.canSwitchToLegal" class="order-step__info">Если вы хотите оформить заказ как юр.лицо или ИП, заполните реквизиты в <router-link to="/profile" class="order-step__info-link">личном кабинете</router-link></div>
        <div>
            <div>
                <div class="order-step__physical" v-if="!isLegal">
                    <Personal :userInfo="userInfo" :isProfile="false"/>

                    <div class="order-step__phone information__wrapper">
                        <Phone
                            :phone="userInfo.phone"
                            :isDetail="true"
                            :withTitle="true"
                            :isShowModal="isShowConfirmModal"
                            @setPhone="setPhone"
                            @confirm="setPhoneConfirmed"
                            :isRequeired="isRequiredPhysicalPhone"
                        />
                    </div>
                </div>
                <div v-if="isLegal">
                    <div class="order-step__legal">
                        <Personal :userInfo="userInfo" :isProfile="false" :is-legal="true"/>
                        <div class="order-step__phone information__wrapper">
                            <Phone
                                :phone="userInfo.phone"
                                :isDetail="true"
                                :withTitle="true"
                                :isShowModal="isShowConfirmModal"
                                @setPhone="setPhone"
                                @confirm="setPhoneConfirmed"
                                :isRequeired="isRequiredPhysicalPhone"
                            />
                        </div>
                        <Company/>
                    </div>
                </div>
            </div>
        </div>

	</div>
</template>

<script>
import OrderStepTitle from "../../OrderStepTitle"
import Personal from "../../../../profile/content/info/input_block/Personal"
import Phone from "../../../../profile/content/info/input_block/Phone"
import Legal from "../../../../profile/content/info/Legal"
import Physical from "../../../../profile/content/info/Physical"
import Company from "../../../../profile/content/info/input_block/Company"
import CartNotification from "../../../../cart/CartNotification"
import { inject } from "vue"
import { mapMutations } from "vuex"

export default {
    name: "Receiver",
    components: {
        CartNotification,
        Company,
        Physical,
        Legal,
        Phone,
        Personal,
        OrderStepTitle
    },
	data() {
        return {
            newPhone: null,
            isShowConfirmModal: false,
			notification: 'Для оформления заказа обязательно подтверждение номера телефона',
            notificationLegal: 'Оформив профиль как юридическое лицо, изменить его в последующем на профиль для физического лица будет нельзя',
			activeTab: 1,
		}
	},
    setup() {
        const userInfo = inject('userInfo')
        const updateUserInfo = inject('updateUserInfo')

        return {
            userInfo,
            updateUserInfo
        }
    },
    methods: {
        ...mapMutations({
            setValidation: 'order/setValidation'
        }),        
        setPhone(value) {
            this.newPhone = value;
            this.setValidation({
                item: 'userPhone',
                value: false
            })
		},
        setPhoneConfirmed() {
            this.setValidation({
                item: 'userPhone',
                value: true
            })

            this.updateUserInfo({
                phone: this.newPhone
            })

            localStorage.unregistrationPhone = this.newPhone
		},
        isTabActive(tab) {
            return this.activeTab === tab;
        },
        setTabActive(tab) {
            this.activeTab = tab;
        }
	},
	computed: {
        isRequiredPhysicalPhone() {
            return this.userInfo.phone.length === 0;
		},
        isRequiredLegalPhone() {
            return this.userInfo.phone.length === 0;
        },
        isLegal() {
            return this.userInfo.isLegal == 1
        }
	}
}
</script>

<style lang="scss">
.order-step {
    &__tabs {
        width: 100%;
        max-width: 520px;
        display: flex;
        margin: 20px 0;
    }

    &__tab {
        width: 50%;
        text-align: center;
        @include _typography-ext(fn, 16, 26, 600, ls, $Zinc-400);
        border-bottom: 2px solid #E4E4E7;
        cursor: pointer;
        transition: border-bottom-color 0.2s ease-in-out;

        &.active {
            color: $Red-400;
            border-color: $Red-400;
        }
    }

    &__physical {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    &__phone {
        height: max-content;
    }

    &__notification {
        max-width: 520px;

        .notification {
            align-items: start;

            &__text {
                width: calc(100% - 26px);
            }
            &__icon {
                width: 30px;
            }

            &__cross {
                display: none;
            }
        }
    }

    &__legal {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
}

.order-step__info {
    @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-400);
    margin-left: 14px;

    @media (min-width: $screen-xl-l) {
        margin-left: 20px;
    }
}

.order-step__info-link {
    color: $Indigo-400;
}
</style>