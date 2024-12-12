import * as Vue from 'vue'
import store from './store'
import router from './router'
import App from "./Main.vue"
import VueLazyload from 'vue-lazyload'

import axios from "axios"
import VueAxios from "vue-axios"
import * as Sentry from "@sentry/vue"

// файл через который подключаются общие компоненты
import * as baseComponents from './components/base/index'

axios.defaults.headers.common = {
    'X-Requested-With': document.querySelector('meta[name="csrf-param"]').getAttribute("content"),
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute("content")
}

const app = Vue.createApp(App)

Sentry.init({
    app,
    dsn: "https://b5af93cc73054aa2b451ed3c895845c7@sentry.labirint.ru/4",
    integrations: [
        new Sentry.BrowserTracing({
            routingInstrumentation: Sentry.vueRouterInstrumentation(router),
        }),
        new Sentry.Replay(),
    ],
  
    // Set tracesSampleRate to 1.0 to capture 100%
    // of transactions for performance monitoring.
    // We recommend adjusting this value in production
    tracesSampleRate: 1.0,
  
    // Set `tracePropagationTargets` to control for which URLs distributed tracing should be enabled
    // tracePropagationTargets: ["localhost", /^https:\/\/yourserver\.io\/api/],
  
    // Capture Replay for 10% of all sessions,
    // plus for 100% of sessions with an error
    replaysSessionSampleRate: 0.1,
    replaysOnErrorSampleRate: 1.0,
});

app.use(store)
    .use(router)
    .use(VueAxios, axios)
    .use(VueLazyload)

for (let componentName in baseComponents) {
    app.component(componentName, baseComponents[componentName])
}
app.config.globalProperties.$emptyCoverUrl = "/img/empty.png"
app.config.globalProperties.$yandexSettings = {
    apiKey: 'e36ff996-8096-449a-b0d8-37a6094ae780&suggest_apikey=299cf49b-60e7-4c97-bf5b-19ee2f0db954',
    lang: 'ru_RU',
    coordorder: 'latlong',
    version: '2.1',
    enterprise: false
}

app.config.globalProperties.$dadataUrl = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address"
app.config.globalProperties.$dadataToken = "b3af7b45a4b4fe985beeb6df08717ea70bb02905"

app.config.globalProperties.$filters = {
    numberFormat(value) {
        return new Intl.NumberFormat().format(value)
    }
}

app.config.globalProperties.$helpers = {
    isMobileScreen() {
        let isMobile = window.innerWidth < 760

        window.addEventListener('resize', () => {
            isMobile = window.innerWidth < 760
        })

        return isMobile
    }
}

app.mount('#main')