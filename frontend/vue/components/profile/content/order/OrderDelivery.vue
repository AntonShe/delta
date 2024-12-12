<template>
	<div>
        <div class="row">
            <div class="title">Получатель</div>
            <div class="content">
				{{ delivery.receiver.name }} {{ delivery.receiver.phone }}
			</div>
        </div>
        <div class="row">
            <div class="title">Адрес доставки</div>
            <div class="content">
				<div class="badge">
					{{ getDeliveryType }}
				</div>

                {{ delivery.address }}
			</div>
<!--			<div v-if="isPvz">
				<BaseSvgStore icon="map" />
                Подробнее о ПВЗ
			</div>-->
        </div>
        <div class="row">
            <div class="title">Способ оплаты</div>
            <div class="content">{{ getPaymentType }}</div>
        </div>
        <div class="row" v-if="isPvz">
            <div class="title">Срок хранения заказа в ПВЗ</div>
            <div class="content">{{ getInfoDateStorage }}</div>
        </div>
	</div>
</template>

<script>
export default {
    name: "OrderDelivery",
	props: {
        delivery: Object,
		paymentType: Number,
    storageDate: String
	},
	data() {
        return {
            payments: {
                1: 'Банковской картой на сайте',
                2: 'Оплата при получении',
                3: 'Счет на оплату',
			}
		}
	},
	computed: {
        isPvz() {
            return this.delivery.type === 2;
		},
        getDeliveryType() {
            return (this.isPvz) ? 'ПВЗ' : 'Курьер';
		},
		getPaymentType() {
            return this.payments[this.paymentType]
		},
    getInfoDateStorage() {
            return this.storageDate ? this.storageDate : 'Информация появится после прибытия заказа в ПВЗ'
    }
	}
}
</script>

<style lang="scss" scoped>
.row {
    display: flex;
    padding: 10px;
    border-top: 1px solid $Zinc-200;
    @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);

    @media (max-width: 955px) {
        flex-direction: column;
    }    

    &:last-child {
        border-bottom: 1px solid $Zinc-200;
    }
}

.title {
    width: 134px;
    margin-right: 46px;
    font-weight: 600;
}

.content {
    display: flex;

    @media (max-width: 955px) {
        flex-direction: column;
    }
}

.badge {
    height: fit-content;
    padding: 0 10px;
    width: max-content;
    margin-right: 6px;
    color: $Green-600;
    background-color: $Green-50;
    @include _border-radius(100px);
}
</style>