import * as Vue from 'vue'
import router from './router'
import App from "./Main.vue"

import axios from "axios"
import VueAxios from "vue-axios"
import Multiselect from '@vueform/multiselect'

import PrimeVue from 'primevue/config';

import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import InputMask from 'primevue/inputmask';
import RadioButton from 'primevue/radiobutton';
import Password from 'primevue/password';
import DataTable from 'primevue/datatable';
import Dropdown from 'primevue/dropdown';
import Dialog from 'primevue/dialog';
import DynamicDialog from 'primevue/dynamicdialog';
import Column from 'primevue/column';
import Calendar from 'primevue/calendar';
import Checkbox from 'primevue/checkbox';
import MultiSelect from 'primevue/multiselect';
import Slider from 'primevue/slider';
import SelectButton from 'primevue/selectbutton';
import TriStateCheckbox from 'primevue/tristatecheckbox';
import Textarea from 'primevue/textarea';
import Toast from 'primevue/toast';
import ToastService from 'primevue/toastservice';

import styles from './assets/sass/main.scss'
import * as baseComponents from './components/base/index'

axios.defaults.headers.common = {
    'X-Requested-With': document.querySelector('meta[name="csrf-param"]').getAttribute("content"),
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute("content")
}

const app = Vue.createApp(App)

app.config.unwrapInjectedRef = true

app.use(PrimeVue, { ripple: true });
app.use(VueAxios, axios);
app.use(router)
app.use(ToastService);

app.component('multiselect', Multiselect);
app.component('Button', Button);
app.component('InputText', InputText);
app.component('InputNumber', InputNumber);
app.component('InputMask', InputMask);
app.component('RadioButton', RadioButton);
app.component('Password', Password);
app.component('DataTable', DataTable);
app.component('Dropdown', Dropdown);
app.component('Dialog', Dialog);
app.component('DynamicDialog', DynamicDialog);
app.component('Column', Column);
app.component('Calendar', Calendar);
app.component('Checkbox', Checkbox);
app.component('MultiSelect', MultiSelect);
app.component('Slider', Slider);
app.component('SelectButton', SelectButton);
app.component('TriStateCheckbox', TriStateCheckbox);
app.component('Textarea', Textarea);
app.component('Toast', Toast);

for (let componentName in baseComponents) {
    app.component(componentName, baseComponents[componentName])
}

app.config.globalProperties.$yandexSettings = {
    apiKey: 'e36ff996-8096-449a-b0d8-37a6094ae780&suggest_apikey=299cf49b-60e7-4c97-bf5b-19ee2f0db954',
    lang: 'ru_RU',
    coordorder: 'latlong',
    version: '2.1',
    enterprise: false
}

app.config.globalProperties.$dadataUrl = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address"
app.config.globalProperties.$dadataToken = "b3af7b45a4b4fe985beeb6df08717ea70bb02905"

app.mount('#main')