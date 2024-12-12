<template>
	<div class="input-date" v-if="withInput">
        <div class="input-date__calendar"><BaseSvgStore icon="calendar" /></div>

        <input class="input-date__input"
               :type="getType"
               placeholder="Дата рождения"
               @change="setValue"
               :value="date"
               @focus="() => {this.focused = true}"
               @focusout="() => {this.focused = false}"
        >
	</div>
	<div v-else class="input-date__datepicker_without_input" @click="toggleCalendar">
        <BaseSvgStore icon="calendar" />

        <input  placeholder="Дата рождения"
                :type="getType"
                @change="setValue"
                :value="date"
                @focus="() => {this.focused = true}"
                @focusout="() => {this.focused = false}"
        >
	</div>
</template>

<script>

export default {
    name: "BaseDatePicker",
	props: {
        withInput: {
            type: Boolean,
			default: true
		},
		value: {
            type: String,
			default: ''
		},
        name: {
            type: String,
            default: ''
        }
	},
	data() {
        return {
            date: this.value,
            focused: false,
			isOpen: false,
		}
	},
    emits: ['changeDate'],
    mounted() {
        window.addEventListener('click', this.hideCalendar);
    },
    computed: {
        getType() {
            return _.isEmpty(this.date) && !this.focused ? 'text' : 'date'
        },
    },
    methods: {
        setValue(event) {
            let selectedDateStr
            let curDate = new Date()
            let selectedDate = new Date(event.target.value)

            if (selectedDate.getFullYear() > curDate.getFullYear()
                || selectedDate.getFullYear() < (parseInt(curDate.getFullYear()) - 100)
            ) {
                selectedDateStr = ''
            } else {
                selectedDateStr = event.target.value
            }

            this.date = selectedDateStr
            this.$emit('changeDate', selectedDateStr, this.name);
		},
        toggleCalendar() {
            const input = document.querySelector('.datepicker_without_input input[type="date"]');

			if (! this.isOpen) {
                input.showPicker();
			}

            this.isOpen = !this.isOpen;

		},
        hideCalendar(event) {
            if (! event.composedPath().includes(document.querySelector('div.datepicker_without_input'))) {
                this.isOpen = false;
			}
		}
	}
}
</script>

<style lang="scss">
.input-date {
    position: relative;

    &__input {
        width: 100%;
        height: 44px;
        border: 0;
        border-bottom: 1px solid $Zinc-400;
        border-radius: unset;
        box-shadow: none;
        padding: 9px 40px;
        text-overflow: ellipsis;
        @include _typography-ext(fn, 16, 26, 400, ls, fc);

        &:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 30px white inset !important;
        }

        @include placeholders($Zinc-500, $inter, 16px, 26px);

        &::-webkit-calendar-picker-indicator {
            position: absolute;
            width: 20px;
            height: 20px;
            top: 11px;
            left: 8px;
            opacity: 0;
        }

        &:focus {
            outline: none;
            border-color: $Indigo-400;
        }

        &:hover {
            outline: none;
            border-color: $Zinc-600;
        }

        &.error {
            border: 1px solid  $Rose-600;
        }
    }

    &__calendar {
        position: absolute;
        top: 12px;
        left: 10px;

        & svg {
            width: 20px;
            height: 20px;
        }
    }

    &__datepicker_without_input {
        height: 100%;
        cursor: pointer;

        & svg {
            position: absolute;
            width: 20px;
            height: 20px;
        }

        & input{
            width: 20px;
            opacity: 0;
        }
    }
}

</style>