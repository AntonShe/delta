<template>
    <tr class="body__row row">
        <td class="body__col col">{{ data.id }}</td>
        <td class="body__col col">{{ data.name }}</td>
        <td class="body__col col">{{ shortFormatDate(data.startDate) }}</td>
        <td class="body__col col">{{ shortFormatDate(data.endDate) }}</td>
        <td class="body__col col">{{ data.sort }}</td>
        <td class="body__col col">{{ data.isActive ? 'Нет' : 'Да' }}</td>                
    </tr>
</template>

<script>
import {format_date} from "../../../mixins/format_date"

export default {
    name: "ShelfItem",
    props: {
        data: {
            type: Object
        }
    },
    mixins: [format_date]
}
</script>