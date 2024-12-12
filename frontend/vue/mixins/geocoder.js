import axios from "axios"
import {loadYmap} from "vue-yandex-maps";

// Тип обращения к Яндексу (см. YandexLogsTypes.php)
const JSAPI_GEOCODER = 4

export const geocoder = {
    methods: {
        // Для логирования запросов к Яндексу
        async geocodeForLPost(data) {
            if (typeof ymaps === 'undefined') {
                await loadYmap(this.$yandexSettings)
            }
            // axios.post('/yandex-logger/write-log', {typeId: JSAPI_GEOCODER})
            return ymaps.geocode(data)
        },
    }
}