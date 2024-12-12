<template>
	<div class="information__wrapper company">
        <input-block-title title="Данные компании" />

        <div class="company-info">
            <div class="company-info__left">
                <base-field
                    id="payer[legalName]"
                    ref="payer[legalName]"
                    name="payer[legalName]"
                    placeholder="Название организации"
                    :value="payer.legalName"
                    @updateInputValue="setInformation"
                    :isRequired=true
                />

                <base-field
                    id="payer[legalInn]"
                    ref="payer[legalInn]"
                    name="payer[legalInn]"
                    placeholder="ИНН"
                    :mask="masks.numberMask"
                    :value="payer.legalInn"
                    @updateInputValue="setInformation"
                    :isRequired=true
                />
                <base-field
                    id="payer[legalKpp]"
                    ref="payer[legalKpp]"
                    name="payer[legalKpp]"
                    placeholder="КПП"
                    :mask="masks.kppMask"
                    :value="payer.legalKpp"
                    @updateInputValue="setInformation"
                    :isRequired=true
                />

                <base-field
                    id="payer[legalAddress]"
                    ref="payer[legalAddress]"
                    name="payer[legalAddress]"
                    placeholder="Юридический адрес"
                    :value="payer.legalAddress"
                    @updateInputValue="setInformation"
                    :isRequired=true
                />

                <base-field
                    id="payer[legalCheckingAcc]"
                    ref="payer[legalCheckingAcc]"
                    name="payer[legalCheckingAcc]"
                    placeholder="Расчетный счет"
                    :value="payer.legalCheckingAcc"
                    @updateInputValue="setInformation"
                    :isRequired=true
                />

                <base-field
                    id="payer[legalBank]"
                    ref="payer[legalBank]"
                    name="payer[legalBank]"
                    placeholder="Наименование банка"
                    :value="payer.legalBank"
                    @updateInputValue="setInformation"
                    :isRequired=true
                />
            </div>
            <div class="company-info__right">
                <base-field
                    id="payer[legalBik]"
                    ref="payer[legalBik]"
                    name="payer[legalBik]"
                    placeholder="БИК"
                    :mask="masks.bikMask"
                    :value="payer.legalBik"
                    @updateInputValue="setInformation"
                    :isRequired=true
                />

                <base-field
                    id="payer[legalCorAcc]"
                    name="payer[legalCorAcc]"
                    placeholder="Корреспондентский счет"
                    :value="payer.legalCorAcc"
                    @updateInputValue="setInformation"
                />

                <base-field
                    id="payer[legalBankBook]"
                    name="payer[legalBankBook]"
                    placeholder="Лицевой счет"
                    :value="payer.legalBankBook"
                    @updateInputValue="setInformation"
                />

                <base-field
                    id="payer[legalSignatoryPosition]"
                    name="payer[legalSignatoryPosition]"
                    placeholder="Должность подписанта"
                    :value="payer.legalSignatoryPosition"
                    @updateInputValue="setInformation"
                />

                <base-field
                    id="payer[legalSignatoryName]"
                    name="payer[legalSignatoryName]"
                    placeholder="ФИО подписанта"
                    :value="payer.legalSignatoryName"
                    @updateInputValue="setInformation"
                />

                <base-field
                    id="payer[legalSignatoryBase]"
                    name="payer[legalSignatoryBase]"
                    placeholder="Действует на основании"
                    :value="payer.legalSignatoryBase"
                    @updateInputValue="setInformation"
                />
            </div>
        </div>
        <div class="company-info__btn-checkbox"
             :class="{
			     'active': !isDifferent,
			 }"
        >
            <base-checkbox
                label="Организация-грузополучатель отличается от плательщика"
                :value="!isDifferent"
                @updateValue="setDifferent"
                id="isDifferent"
                name="isDifferent"
            />
        </div>

        <div v-if="!isDifferent" class="company-info">
            <div class="company-info__left">
                <base-field
                    id="getter[legalName]"
                    ref="getter[legalName]"
                    name="getter[legalName]"
                    placeholder="Название организации"
                    :value="getter.legalName"
                    @updateInputValue="setInformation"
                    :isRequired=true
                />

                <base-field
                    id="getter[legalInn]"
                    ref="getter[legalInn]"
                    name="getter[legalInn]"
                    placeholder="ИНН"
                    :mask="masks.numberMask"
                    :value="getter.legalInn"
                    @updateInputValue="setInformation"
                    :isRequired=true
                />
                <base-field
                    id="getter[legalKpp]"
                    ref="getter[legalKpp]"
                    name="getter[legalKpp]"
                    placeholder="КПП"
                    :mask="masks.kppMask"
                    :value="getter.legalKpp"
                    @updateInputValue="setInformation"
                    :isRequired=true
                />

                <base-field
                    id="getter[legalAddress]"
                    ref="getter[legalAddress]"
                    name="getter[legalAddress]"
                    placeholder="Юридический адрес"
                    :value="getter.legalAddress"
                    @updateInputValue="setInformation"
                    :isRequired=true
                />

                <base-field
                    id="getter[legalCheckingAcc]"
                    ref="getter[legalCheckingAcc]"
                    name="getter[legalCheckingAcc]"
                    placeholder="Расчетный счет"
                    :value="getter.legalCheckingAcc"
                    @updateInputValue="setInformation"
                    :isRequired=true
                />

                <base-field
                    id="getter[legalBank]"
                    ref="getter[legalBank]"
                    name="getter[legalBank]"
                    placeholder="Наименование банка"
                    :value="getter.legalBank"
                    @updateInputValue="setInformation"
                    :isRequired=true
                />
            </div>
            <div class="company-info__right">
                <base-field
                    id="getter[legalBik]"
                    ref="getter[legalBik]"
                    name="getter[legalBik]"
                    placeholder="БИК"
                    :mask="masks.bikMask"
                    :value="getter.legalBik"
                    @updateInputValue="setInformation"
                    :isRequired=true
                />

                <base-field
                    id="getter[legalCorAcc]"
                    name="getter[legalCorAcc]"
                    placeholder="Корреспондентский счет"
                    :value="getter.legalCorAcc"
                    @updateInputValue="setInformation"
                />

                <base-field
                    id="getter[legalBankBook]"
                    name="getter[legalBankBook]"
                    placeholder="Лицевой счет"
                    :value="getter.legalBankBook"
                    @updateInputValue="setInformation"
                />

                <base-field
                    id="getter[legalSignatoryPosition]"
                    name="getter[legalSignatoryPosition]"
                    placeholder="Должность подписанта"
                    :value="getter.legalSignatoryPosition"
                    @updateInputValue="setInformation"
                />

                <base-field
                    id="getter[legalSignatoryName]"
                    name="getter[legalSignatoryName]"
                    placeholder="ФИО подписанта"
                    :value="getter.legalSignatoryName"
                    @updateInputValue="setInformation"
                />

                <base-field
                    id="getter[legalSignatoryBase]"
                    name="getter[legalSignatoryBase]"
                    placeholder="Действует на основании"
                    :value="getter.legalSignatoryBase"
                    @updateInputValue="setInformation"
                />
            </div>
        </div>

        <div class="error-hit" v-if="hasError && !isListOfErrors">{{ getError }}</div>
        <div class="error-hit" v-if="hasError && isListOfErrors">
            <span v-for="error in getError" v-html="parseBreak(error)"></span>
        </div>

        <button @click="saveData"
                class="company-info__button"
                :class="{
                        'disabled': isButtonDisabled
					}">
            Сохранить
        </button>
	</div>
</template>

<script>
import {IMaskDirective} from 'vue-imask'
import InputBlockTitle from "../InputBlockTitle"
import BaseField from "../../../../base/BaseField"
import BaseCheckbox from "../../../../base/BaseCheckbox"
import { mapActions, mapState } from 'vuex'

export default {
    name: "Company",
    directives: {
        imask: IMaskDirective
    },
    components: {
        BaseCheckbox,
        BaseField,
        InputBlockTitle
    },
	data() {
        return {
            isButtonDisabled: true,
            changes: {
                payer: {},
                getter: {},
            },
            masks: {
                numberMask: {
                    mask: /[0-9]+/
                },
                kppMask: {
                    mask: '000000000'
                },
                bikMask: {
                    mask: '000000000'
                },

            },
            isDifferent: null
		}
	},
    beforeMount() {
        this.isDifferent = this.userInfo.isPayerSameGetter
    },
    computed: {
        ...mapState({
            userInfo: state => state.user.userInfo,
            saveErrors: state => state.user.saveErrors
        }),
        originalDifferent() {
            return this.userInfo.isPayerSameGetter
        },
        payer() {
            return this.userInfo.company[0]
        },
        getter() {
            return this.userInfo.isPayerSameGetter ? {} : userInfo.value.company[1]
        },
        hasError() {
            return !_.isEmpty(this.saveErrors)
        },
        getError() {
            return _.isEmpty(this.saveErrors) ? "Проверьте правильность заполнения полей" : this.saveErrors
        },
        isListOfErrors() {
            return _.isArray(this.getError)
        },
    },
	methods: {
        ...mapActions({
            updateUserInfo: 'user/updateUserInfo'
        }),    
        parseBreak(str) {
            str = str.replace("\n", '<br/>')

            return str
        },
        setInformation(value, name) {
            let rawData  = name.split('[')
            let legalType = rawData[0]
            let legalField = rawData[1].substring(0, rawData[1].length - 1)

            this.changes[legalType][legalField] = value

            this.isButtonDisabled = !this.checkData()
		},
        setDifferent() {
            this.isDifferent = !this.isDifferent;

            if (this.isDifferent != this.originalDifferent) {
                this.isButtonDisabled = !this.checkData()
            }
        },
        checkData() {
            let isReady = true

            _.forEach(this.$refs, (item) => {
                if (item !== null) {
                    if (_.isEmpty(item.inputValue)) isReady = false

                    if (item.id == 'payer[legalInn]' && item.id == 'getter[legalInn]') {
                        let validInn = this.validateInn(item.inputValue)

                        if (!validInn) isReady = false
                    }

                    if (item.id == 'payer[legalKpp]' && item.id == 'getter[legalKpp]') {
                        let validKpp = this.validateKpp(item.inputValue)

                        if (!validKpp) isReady = false
                    }
                }
            })

            return isReady
        },
        saveData() {
            if (!this.isButtonDisabled && this.checkData()) {
                this.isButtonDisabled = true

                let dataForUpdate = {
                    isLegal: 1,
                    payerSameGetter: this.isDifferent,
                    profile: {
                        payer: {
                            ...this.payer,
                            ...this.changes.payer
                        },
                        getter: {
                            ...this.getter,
                            ...this.changes.getter
                        }
                    }
                }

                this.updateUserInfo(dataForUpdate)
            }
        },
        validateInn(value) {
            let valLength = value.length

            switch (valLength) {
                case 10:
                    let numSum = (2 * parseInt(value[0]))
                        + (4 * parseInt(value[1]))
                        + (10 * parseInt(value[2]))
                        + (3 * parseInt(value[3]))
                        + (5 * parseInt(value[4]))
                        + (9 * parseInt(value[5]))
                        + (4 * parseInt(value[6]))
                        + (6 * parseInt(value[7]))
                        + (8 * parseInt(value[8]))

                    let valDiv = (numSum % 11).toString()

                    return value[9] == valDiv[valDiv.length - 1]
                    break
                case 12:
                    let firstControlSum = (7 * parseInt(value[0]))
                        + (2 * parseInt(value[1]))
                        + (4 * parseInt(value[2]))
                        + (10 * parseInt(value[3]))
                        + (3 * parseInt(value[4]))
                        + (5 * parseInt(value[5]))
                        + (9 * parseInt(value[6]))
                        + (4 * parseInt(value[7]))
                        + (6 * parseInt(value[8]))
                        + (8 * parseInt(value[9]))

                    let firstDiv = (firstControlSum % 11).toString()

                    let secondControlSum = (3 * parseInt(value[0]))
                        + (7 * parseInt(value[1]))
                        + (2 * parseInt(value[2]))
                        + (4 * parseInt(value[3]))
                        + (10 * parseInt(value[4]))
                        + (3 * parseInt(value[5]))
                        + (5 * parseInt(value[6]))
                        + (9 * parseInt(value[7]))
                        + (4 * parseInt(value[8]))
                        + (6 * parseInt(value[9]))
                        + (8 * parseInt(value[10]))

                    let secondDiv = (secondControlSum % 11).toString()

                    return (
                        value[10] == firstDiv[firstDiv.length - 1]
                        && value[11] == secondDiv[secondDiv.length - 1]
                    )
                    break
                default:
                    return false
                    break
            }


        },
        validateKpp(value) {
            return value.length == 9
        },
	}
}
</script>

<style lang="scss">

.information__wrapper.company {
    max-width: 1068px;
    width: 100%;
    padding: 20px 20px 64px 20px;
}

.company-info {
    display: flex;
    margin-bottom: 40px;

    @media (max-width: $screen-xl) {
        flex-direction: column;
    }

    &__left {
        margin-right: 20px;
    }

    &__left,
    &__right {
        width: calc(100% - 20px);
    }

    &__btn-checkbox {
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-500);
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;

        @media (max-width: $screen-md) {
            flex-direction: column;
            row-gap: 20px;
        }

        &.active {
            flex-direction: column;
            align-items: start;
        }

        &__button {
            @media (max-width: $screen-md) {
                width: 100%;
            }
        }
    }

    &__button {
        height: 44px;
        align-self: end;
        float: right;
        @include _button-reset(9px 16px, $Zinc-700, none);
        @include _typography-ext(fn, 16, 26, 600, ls, $white);

        @media (max-width: $screen-md) {
            width: 100%;
        }

        &.disabled {
            color: $Zinc-400;
            @include _button-reset(9px 16px, $Zinc-100, none);
        }

        &:hover:not(.disabled) {
            background-color: $Zinc-900;
        }
    }

    &__different {
        max-width: 484px;
        width: 100%;
    }
}

.error-hit{
    float: left;
    margin-right: 25px;
    margin-top: 25px;
    font-size: 14px;
    line-height: 16px;
    font-weight: 400;
    color: #E11D48;
    margin-bottom: 10px;
    opacity: 1 !important;
}
</style>