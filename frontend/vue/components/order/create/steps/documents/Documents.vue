<template>
	<div>
        <OrderStepTitle :number="4" title="Документы для печати" :isActive="isActive"/>

		<div class="order-documents">
			<BaseCheckbox
				v-for="(document, index) in documents"
				:key="index"
				:label="document.title"
				:value="false"
                :id="index"
			/>
		</div>
	</div>
</template>

<script>
import OrderStepTitle from "../../OrderStepTitle.vue";
import BaseCheckbox from "../../../../base/BaseCheckbox.vue";

export default {
    name: "Documents",
    components: {BaseCheckbox, OrderStepTitle},
	data() {
        return {
            documents: [
                {
                    title: 'ИНН/ОГРН',
                    link: '/file/INN_OGRN_DELTABOOK.pdf'
                },
                {
                    title: 'Устав',
                    link: '/file/Ustav_Deltabook.pdf'
                }
            ]
        }
    }
}
</script>

<style lang="scss" scoped>
.order-documents {
    @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);
    padding: 20px;
    background-color: $white;
}
</style>