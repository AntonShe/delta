import * as Vue from 'vue'
import SearchApp from "./Search.vue"
import VueLazyload from 'vue-lazyload'

import axios from "axios"
import VueAxios from "vue-axios"

axios.defaults.headers.common = {
    'X-Requested-With': document.querySelector('meta[name="csrf-param"]').getAttribute("content"),
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute("content")
}

Vue.createApp(SearchApp)
    .use(VueAxios, axios)
    .use(VueLazyload)
    .mount('#search')