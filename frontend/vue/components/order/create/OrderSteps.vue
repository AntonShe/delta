<template>
	<div>
		<Receive v-if="isLoadedDeliveryProfile" @setDeliveryType="setDeliveryType"/>

		<Receiver v-if="isLoadedUserInfo" />

		<Payment v-if="isLoadedUserInfo" :default-type="paymentType"/>

<!--		<Documents v-if="isLoadedUserInfo && isLegal" />-->
	</div>
</template>

<script>
import Receive from "./steps/receive/Receive"
import Receiver from "./steps/receiver/Receiver"
import Payment from "./steps/payment/Payment"
import Documents from "./steps/documents/Documents"
import {provide, readonly, ref} from "vue"
import { useRouter } from 'vue-router'
import axios from "axios"
import { mapActions, mapMutations, mapState } from 'vuex'

export default {
    name: "OrderSteps",
    components: {
        Documents,
        Payment,
        Receiver,
        Receive
    },
	props: {
        order: Object
	},
    emits: ['delivery'],
    setup() {
        //region user data
        const userInfo = ref({})
        const saveErrors = ref([])
        const router = useRouter()

        const setUserInfo = () => {
            axios.get('/user/get-user-info')
                .then(result => {
                    if (_.isEmpty(result.data)) {
                        // userInfo.value = {
                        //     firstName: localStorage.getItem('firstName') === null
                        //         ? '' :localStorage.getItem('firstName'),
                        //     secondName: localStorage.getItem('secondName') === null
                        //         ? '' :localStorage.getItem('secondName'),
                        //     lastName: localStorage.getItem('lastName') === null
                        //         ? '' :localStorage.getItem('lastName'),
                        //     phone: localStorage.getItem('unregistrationPhone') === null
                        //         ? '' :localStorage.getItem('unregistrationPhone'),
                        //     isLegal: 0,
                        //     company: {}
                        // }

                        localStorage.setItem('notAuthFromOrder', 1)
                        router.push({
                            name: 'Cart'
                        })
                    } else {
                        userInfo.value = result.data;
                    }
                });
        }
        const updateUserInfo = (data) => {
            axios.patch('/customer', data)
                .then(result => {
                    if (result.data.status == true) {
                        saveErrors.value = []
                        setUserInfo()
                    } else {
                        saveErrors.value = [...result.data.errors]
                    }
                });
        }
        //endregion

        provide('userInfo', readonly(userInfo))
        provide('saveErrors', readonly(saveErrors))
        provide('updateUserInfo', updateUserInfo)

        return {
            userInfo,
            setUserInfo
        }
    },
    watch: {
        userInfo: {
            deep: true,
            handler() {
                let validValue

                validValue = !_.isEmpty(this.userInfo.lastName)
                    && !_.isEmpty(this.userInfo.firstName)

                if (this.isLegal) {
                    validValue = !_.isEmpty(this.userInfo.company[0].legalName)
                        && !_.isEmpty(this.userInfo.company[0].legalInn)
                        && !_.isEmpty(this.userInfo.company[0].legalKpp)
                        && !_.isEmpty(this.userInfo.company[0].legalAddress)
                        && !_.isEmpty(this.userInfo.company[0].legalCheckingAcc)
                        && !_.isEmpty(this.userInfo.company[0].legalBank)
                        && !_.isEmpty(this.userInfo.company[0].legalBik)

                    if (!_.isEmpty(this.userInfo.company[1])) {
                        validValue = !_.isEmpty(this.userInfo.company[1].legalName)
                            && !_.isEmpty(this.userInfo.company[1].legalInn)
                            && !_.isEmpty(this.userInfo.company[1].legalKpp)
                            && !_.isEmpty(this.userInfo.company[1].legalAddress)
                            && !_.isEmpty(this.userInfo.company[1].legalCheckingAcc)
                            && !_.isEmpty(this.userInfo.company[1].legalBank)
                            && !_.isEmpty(this.userInfo.company[1].legalBik)
                    }
                }

                this.setValidation({
                    item: 'userProfile',
                    value: validValue
                })

                this.setValidation({
                    item: 'userPhone',
                    value: !_.isEmpty(this.userInfo.phone)
                })
            }
        }
    },
    computed: {
        ...mapState({
            deliveryProfile: state => state.order.deliveryProfile
        }),
        isLegal() {
            return this.userInfo.isLegal == 1
        },
        paymentType() {
            return this.isLegal ? 3 : 2//1
        },
        isLoadedUserInfo() {
            return !_.isEmpty(this.userInfo)
        },
        isLoadedDeliveryProfile() {
            return !_.isEmpty(this.deliveryProfile)
        },
    },
    beforeMount() {
        this.getDeliveryProfile()
        this.setUserInfo()
    },
    methods: {
        ...mapMutations({
            setValidation: 'order/setValidation'
        }),
        ...mapActions({
            getDeliveryProfile: 'order/getDeliveryProfile'
        }),
        setDeliveryType(type) {
            let validValue  = !_.isEmpty(this.deliveryProfile[type])

            this.setValidation({
                item: 'delivery',
                value: validValue,
            })
        }
    }
}
</script>

<style lang="scss">
.order-step {
    padding-bottom: 50px;
    margin-bottom: 50px;
    border-bottom: 2px solid $Zinc-200;
    background-color: #fff;

    &--payment{
        background: none;
    }
}
</style>