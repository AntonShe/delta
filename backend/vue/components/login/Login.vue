<template>
    <div
        class="surface-ground flex align-items-center justify-content-center min-h-screen min-w-screen overflow-hidden">
        <div class="flex flex-column align-items-center justify-content-center">
            <img src="/img/logo-header.svg" alt="Deltabook logo" class="mb-5 w-14rem flex-shrink-0"/>
            <div v-if="!isCodeError"
                 style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full surface-card py-8 px-5 sm:px-8" style="border-radius: 53px">
                    <div class="text-center mb-5">
                        <div class="flex justify-content-center align-items-center mb-3">
                            <div class="flex justify-content-center align-items-center border-circle"
                                 style="height: 3.2rem; width: 3.2rem; background: var(--primary-color);">
                                <i class="pi pi-user pi-exclamation-circle text-2xl text-white"></i>
                            </div>
                        </div>
                        <div class="text-900 text-3xl font-medium mb-3">Добро пожаловать!</div>
                        <span class="text-600 font-medium">Войдите, чтобы продолжить</span>
                    </div>
                    <div>
                        <label for="login" class="block text-900 text-xl font-medium mb-2">Электронная почта</label>
                        <InputText
                            id="login"
                            name="login"
                            type="text"
                            placeholder="Электронная почта"
                            class="w-full md:w-30rem mb-5"
                            style="padding: 1rem"
                            v-model="login"
                        />

                        <label for="pass" class="block text-900 font-medium text-xl mb-2">Пароль</label>
                        <Password
                            id="pass"
                            name="pass"
                            class="w-full mb-3"
                            inputClass="w-full"
                            placeholder="Пароль"
                            v-model="password"
                            :feedback="false"
                            :inputStyle="{ padding: '1rem' }"
                        />
                        <p v-if="isError" class="text-pink-500 mb-0">Неправильный логин или пароль</p>
                        <Button
                            label="Войти"
                            class="w-full mt-5 p-3 text-xl"
                            :disabled="isDisable"
                            @click.prevent="authorize"
                        />
                    </div>
                </div>
            </div>
            <div v-else
                 style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, rgba(233, 30, 99, 0.4) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full surface-card py-8 px-5 sm:px-8 flex flex-column align-items-center"
                     style="border-radius: 53px">
                    <div class="text-center">
                        <div class="flex justify-content-center align-items-center mb-3">
                            <div class="flex justify-content-center align-items-center bg-pink-500 border-circle"
                                 style="height: 3.2rem; width: 3.2rem;">
                                <i class="pi pi-fw pi-exclamation-circle text-2xl text-white"></i>
                            </div>
                        </div>
                        <h1 class="text-900 font-bold text-5xl mt-0 mb-2">Произошла ошибка</h1>
                        <span class="text-600 mb-5">Не возможно авторизоваться! <br/> Обратитесь к разработчикам.</span>
                        <div class="mb-5">
                            <ErrorIcon/>
                        </div>
                        <a class="col-12 mt-5 text-center">
                            <i class="pi pi-fw pi-arrow-left text-blue-500 mr-2" style="vertical-align: center"></i>
                            <span class="text-blue-500" @click="this.isCodeError = false">Вернуться</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {IMaskDirective} from 'vue-imask'
import axios from "axios";
import ErrorIcon from "./ErrorIcon";

export default {
    name: "Login",
    components: {
        ErrorIcon
    },
    directives: {
        imask: IMaskDirective
    },
    data() {
        return {
            isError: false,
            isCodeError: false,
            login: '',
            password: '',
            mask: {
                mask: /[0-9a-zA-Z!@#$%&'*+-/=?^_`{|}~\s]+/,
                lazy: true
            }
        }
    },
    computed: {
        isDisable() {
            return (_.isEmpty(this.login) || _.isEmpty(this.password))
        }
    },
    methods: {
        authorize() {
            this.isCodeError = false
            this.isError = false

            if (_.isEmpty(this.login) || _.isEmpty(this.password)) {
                return;
            }

            axios
                .post('/admin/backend/user/login', {
                    email: this.login,
                    password: this.password,
                    isAdmin: 1
                })
                .then(response => {
                    if (response.data.status && response.data.code == 0) {
                        this.$router.push({
                            name: 'AdminMain'
                        })
                    } else {
                        this.isError = true;
                        throw new Error('Ошибка авторизации в админке');
                    }
                })
                .catch(error => {
                    this.isCodeError = true
                    throw new Error('Ошибка авторизации в админке, ' + error);
                })

            return false
        }
    }
}
</script>

<style scoped>
</style>