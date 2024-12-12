<template>
	<div class="legal">
		<div class="legal__notification">
			<CartNotification :text="notificationText" />
		</div>
        <div class="legal__wrap">
            <Personal :userInfo="userInfo" :is-legal="true"/>
            <div class="legal__right-wrap">
                <Email
                    :email="userInfo.email"
                    :is-legal="true"
                    :needEmail="needEmail"
                    @setNeedEmail="needEmail = false"
                />
                <Password
                    :isPasswordExist="userInfo.isPasswordExist"
                    :is-legal="true"
                    :needEmail="needEmail"
                    @setNeedEmail="needEmail = true"
                />
            </div>
        </div>
		<Company/>
	</div>
</template>

<script>
import Personal from "./input_block/Personal"
import Password from "./input_block/Password"
import Email from "./input_block/Email"
import Company from "./input_block/Company"
import CartNotification from "../../../cart/CartNotification"
import { mapState } from 'vuex'

export default {
    name: "Legal",
    components: {
        CartNotification,
        Company,
        Email,
        Password,
        Personal
    },
	data() {
        return {
            notificationText: 'При регистрации в качестве юридического лица, оформление заказов будет возможно только от лица компании. ' +
				'Если вы захотите в дальнейшем оформить заказ как физическое лицо, вам понадобится зарегистрировать новый аккаунт',
            needEmail: false
		}
	},
    computed: {
        ...mapState({
            userInfo: state => state.user.userInfo
        })
    }    
}
</script>

<style lang="scss">
.legal {
    &__notification {
        max-width: 1068px;

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

    &__wrap {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;

        @media (max-width: $screen-xl-l) {
            flex-direction: column;
        }
    }
}
</style>