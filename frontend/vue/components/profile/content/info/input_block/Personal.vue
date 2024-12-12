<template>
	<div class="physical-personal information__wrapper">
        <InputBlockTitle title="Личные данные" />
		<base-field
                id="lastName"
				name="lastName"
				placeholder="Фамилия"
				:value="userInfo.lastName"
				@updateInputValue="setNewInformation"
                :isRequired=true
                :isNeedPencil=true
		/>

        <base-field
				id="firstName"
				name="firstName"
				placeholder="Имя"
				:value="userInfo.firstName"
                @updateInputValue="setNewInformation"
                :isRequired=true
                :isNeedPencil=true
        />

        <base-field
                id="secondName"
				name="secondName"
				placeholder="Отчество"
				:value="userInfo.secondName"
                @updateInputValue="setNewInformation"
                :isNeedPencil=true
        />

		<div v-if="isProfile">
            <div class="physical-personal__sex">
                <base-radio-group
                    :list="radioButtons"
                    inputName="sex"
                    mainLabel="Пол"
                    :defaultValue="userInfo.sex"
                    @onChanged="setNewInformation"
                />
            </div>
            <div class="physical-personal__input-block">
                <base-date-picker
                        id="birthday"
                        name="birthday"
                        placeholder="Дата рождения"
                        :value="userInfo.birthday"
                        @changeDate="setNewInformation"
                />
            </div>

            <Phone
					:phone="userInfo.phone"
					:withTitle="true"
					:isShowModal="isShowConfirmModal"
					@updatePhone="setNewInformation"
					@confirm="setPhoneConfirmed"
                    :is-cabinet="isProfile"
			/>
		</div>

		<div class="information__button-wrap">
			<button class="information__button"
                    :class="{
                        'disabled': isSaveDisabled
					}"
                    @click="saveInformation">
				Сохранить изменения
			</button>
		</div>
	</div>
</template>

<script>
import Confirm from "../Confirm"
import Phone from "./Phone"
import InputBlockTitle from "../InputBlockTitle"
import BaseField from "../../../../base/BaseField"
import BaseRadioGroup from "../../../../base/BaseRadioGroup"
import BaseDatePicker from "../../../../base/BaseDatePicker"
import { mapActions } from 'vuex'

export default {
    name: "Personal",
    components: {
        BaseDatePicker,
        BaseRadioGroup,
        BaseField,
        Phone,
        Confirm,
        InputBlockTitle
    },
	props: {
		isProfile: {
            type: Boolean,
			default: true
		},
        isLegal: {
            type: Boolean,
            default: false
        },
        userInfo: {
            type: Object,
            required: true
        }
	},
	data() {
        return {
            radioButtons: [
                {
                    label: 'Мужской',
					value: '1',
					id: 'male',
					name: 'male',
				},
                {
                    label: 'Женский',
                    value: '0',
                    id: 'female',
                    name: 'female',
                },
			],
			newInformation: {},
            isShowConfirmModal: false,
            isSaveDisabled: true,
			isPhoneNew: false        
		}
	},
	methods: {
        ...mapActions({
            updateUserInfo: 'user/updateUserInfo'
        }),        
        setNewInformation(value, name) {
            if (this.userInfo[`${name}`] != value && !_.isEmpty(value) && value !== undefined) {
                this.newInformation[`${name}`] = localStorage[`${name}`] = value

                if (((
                        this.userInfo.lastName != ""
                        || (this.newInformation.lastName != "" && this.newInformation.lastName !== undefined)
                    )
                    && (
                        this.userInfo.firstName != ""
                        || (this.newInformation.firstName != "" && this.newInformation.firstName !== undefined)
                    ))
                ) {
                    this.isSaveDisabled = false
                }
            } else {
                this.isSaveDisabled = true
            }
		},
        saveInformation() {
            if (!_.isEmpty(this.newInformation) && !this.isSaveDisabled) {
                if (this.isPhoneNew) {
                    this.isShowConfirmModal = true
                }

                this.updateUserInfo({
                    ...this.newInformation,
                    isLegal: this.isLegal
                })
                this.isSaveDisabled = true
            }
		},
        setPhoneConfirmed(isConfirmed) {
            if (isConfirmed) {
                let pin = localStorage.getItem('pin')
                let pinKey = pin.includes('@') ? 'email' : 'phone'

                this.isShowConfirmModal = false;
                this.isPhoneNew = false

                this.updateUserInfo({
                    isLegal: this.isLegal,
                    [pinKey]: pin
                })
                this.isSaveDisabled = true
            }
		},
	}
}
</script>

<style lang="scss" scoped>
.physical-personal {
    gap: 20px;

    .base-radio-group {
        column-gap: 24px;
    }

    &__input-block {
        margin-bottom: 20px;

        @media (max-width: $screen-md) {
            margin-bottom: 10px;
        }
    }

    #birthdate {
        input[type="date"] {
            margin-bottom: 10px;
            width: 100%;
        }
        svg-store {
            display: none;
        }
    }
}

.physical-personal__sex {
    padding: 10px;
    margin-bottom: 10px;
}
</style>