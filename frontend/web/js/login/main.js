jQuery(function ($) {
    $(document).ready(function () {
        const param = $('meta[name="csrf-param"]').attr("content")
        const token = $('meta[name="csrf-token"]').attr("content")

        const backList = [];

        var emailRegex
        var phoneRegex

        async function getForm(type = 0, subFirst = 0, subSecond = 0, params = {}) {
            const response = await fetch('/get-form', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    [param]: token,
                    type: type,
                    subFirst: subFirst,
                    subSecond: subSecond,
                    params: params
                }),
            });

            return await response.json()

        }

        function validateEmail(email) {
            if (
                email == ''
                || typeof email === "undefined"
                || email.length < 5
                || email.length > 50
            ) return false

            let regularStr = /[a-zA-Z]+[a-zA-Z\d\!\#\$\%\&\’\;\+\-\.\=\?\^\_\`\{\}\½\~]*[a-zA-Z\d]?@[a-zA-Z]+[a-zA-Z\d\-\d]*[a-zA-Z\d]?\.[a-zA-Z]+/

            return email == email.match(regularStr)[0]
        }

        function validatePhone(phone) {
            if (
                phone == ''
                || typeof phone === "undefined"
                || phone.length != 18
            ) return false

            let regularStr = /\+7\s\(\d{3}\)\s\d{3}-\d{2}-\d{2}/

            return phone == phone.match(regularStr)[0]
        }

        function redirectPage() {
            if (window.location.pathname.split('/')[1] === 'cart' && localStorage.getItem('fromCart')) {
                window.location = '/cart'
                localStorage.removeItem('fromCart')
            } else {
                window.location.reload()
            }
        }

        function clearStorage() {
            const fromCart = localStorage.getItem('fromCart')
            const userLocation = localStorage.getItem('userLocation')
            const cityList = localStorage.getItem('cityList')

            localStorage.clear()
            if (fromCart) localStorage.setItem('fromCart', fromCart)
            if (userLocation) localStorage.setItem('userLocation', userLocation)
            if (cityList) localStorage.setItem('cityList', cityList)
        }

        function toggleHiddenBodyScroll(action = 'scroll') {
            if (action === 'scroll') {
                document.body.classList.remove('overflow-hidden')
            } else {
                document.body.classList.add('overflow-hidden')
            }
        }

        const popupContainer = $('.popup-container')

        $(document).on('click', '.js-popup-button-back', () => {
            let functionName = backList.pop();
            switch (functionName) {
                case "registration":
                    openFormLoginOrRegistration(true);
                    break;
                case "login":
                    openFormLoginOrRegistration(false);
                    break;
                case "loginPassword":
                    openFormLoginOrRegistration(false);
                    break;
                case "loginPasswordRegistration":
                    openFormLoginPassword(true);
                    break;
                case "restorePassword":
                    openFormLoginPassword(false);
                    break;
                case "restorePasswordCode":
                    openFormRestore(false);
                    break;
                case "restorePasswordRegistration":
                    openFormRestore(true);
                    break;
                default:
                    openFormLoginOrRegistration(false);
            }

        })

        //region Открытие формы входа/регистраци
        function openFormLoginOrRegistration(registration = false) {
            clearStorage();

            let result = getForm(0, 1, 2, {
                activeTab: registration ? 2 : 1,
            });

            result.then((data) => {
                popupContainer.html(data.content)
                toggleHiddenBodyScroll('noscroll')

                emailRegex = IMask(
                    document.getElementById('popup-email'),
                    {
                        mask: /^[a-zA-Z\d\.\_\-\+0-9@]+$/
                    });

                phoneRegex = IMask(
                    document.getElementById('popup-phone'),
                    {
                        mask: '+7 (000) 000-00-00'
                    });
            })
        }

        $(document).on('click', '.js-login-button', () => openFormLoginOrRegistration(false))
        //endregion

        //region отправка кода при регистрации
        $(document).on('submit', '#registration[data-type="registr"]', function (e) {
            e.preventDefault()
            let form = $(this)

            emailRegex = IMask(
                document.getElementById('popup-email'),
                {
                    mask: /^[a-zA-Z\d\.\_\-\+0-9@]+$/
                });

            form.find('.error-message')
                .text('')
                .removeClass('active')

            if (validateEmail(emailRegex.value)) {
                $.ajax({
                    method: "POST",
                    url: '/user/send-pin',
                    data: {
                        [param]: token,
                        [form.serializeArray()[0]['name']]: form.serializeArray()[0]['value'],
                        isNew: 1
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.result === false) {
                            form.find('.error-message')
                                .text('Не удалось отправить код. Попробуйте позже.')
                                .addClass('active')

                            return false;
                        } else if (response.result !== true) {
                            form.find('.error-message')
                                .text(response.result)
                                .addClass('active')

                            return false;
                        }

                        localStorage.setItem('pin', form.serializeArray()[0]['value'])
                        localStorage.setItem('key', response.keyHash)

                        let result = getForm(1, 0, 0, {
                            email: localStorage.getItem('pin')
                        })

                        result.then((data) => {
                            popupContainer.html(data.content)
                            $('#popup-code-1').focus()

                            let resender = $('#resender')
                            let timerTime = resender.data('timer')

                            let timer = setInterval(() => {
                                if (timerTime == 0) {
                                    resender.find('span').text('')
                                    resender.removeClass('button-disable')
                                    clearInterval(timer)
                                } else {
                                    resender.find('span').text(timerTime)
                                    timerTime -= 1
                                }
                            }, 1000)
                        })
                            .then(() => backList.push(backList.length > 0 ? backList[backList.length - 1] + 'Registration' : 'registration'))
                    }
                })
            } else {
                form.find('.error-message')
                    .text('Некорректный E-mail')
                    .addClass('active')
            }
        })
        //endregion

        //region Подтверждение кода
        $(document).on('submit', '#verify-code', function (e) {
            e.preventDefault()
            if (e.target.dataset.blocked === 'true') return

            // if (e.delegateTarget.activeElement.id == "verify-pin") {//FireFox - отстой
            let form = $(this)
            let pin = localStorage.getItem('pin')
            let type = localStorage.getItem('type')
            let pinKey = pin.includes('@') ? 'email' : 'phone'
            let rawPin = form.serializeArray()
            let pinData = ''

            form.find('.error-message')
                .text('')
                .removeClass('active')

            rawPin.forEach((e, i) => {
                pinData += e.value
            })

            if (pinData.length < 5) {
                form.find('.error-message')
                    .text('Неверный код. Проверьте количество символов.')
                    .addClass('active')
                return;
            }

            e.target.dataset.blocked = true
            e.target.querySelector('.js-resender-btn').classList.add('button-disable')
            
            let preloader = new Preloader('.js-popup-container')

            preloader.show()

            $.ajax({
                method: "POST",
                url: '/user/verify-pin',
                data: {
                    [param]: token,
                    [pinKey]: pin,
                    pin: pinData,
                },
                dataType: 'json',
                success: function (response) {

                    if (response.result === false) {
                        form.find('.error-message')
                            .text('Неверный код. Попробуйте ещё раз.')
                            .addClass('active')

                        const resenderBtn = e.target.querySelector('.js-resender-btn')
                        const resenderCounter = resenderBtn.querySelector('span')

                        e.target.dataset.blocked = false
                        if (resenderCounter.innerText === '') resenderBtn.classList.remove('button-disable')
                        preloader.hide()
                        
                        const codeInputs = e.target.querySelectorAll('input')
                        
                        codeInputs.forEach(input => input.value = '')
                        codeInputs[0].focus()

                        return false;
                    } else if (pinKey == 'email' && type == 'regular') {
                        let result = getForm(0, 6, 2, {
                            activeTab: 1,
                            email: pin,
                            type: 'regular'
                        });

                        result.then((data) => {
                            popupContainer.html(data.content)
                        })
                    } else if (pinKey == 'email') {
                        let result = getForm(0, 1, 6, {
                            activeTab: 2,
                            email: pin
                        });

                        result.then((data) => {
                            popupContainer.html(data.content)

                            passMain = IMask(
                                document.getElementById('password'),
                                {
                                    mask: /\S+/
                                });

                            passConfirm = IMask(
                                document.getElementById('consume-password'),
                                {
                                    mask: /\S+/
                                });

                        })
                    } else {
                        $.ajax({
                            method: "POST",
                            url: '/user/auth-user',
                            data: {
                                [param]: token,
                                [pinKey]: pin,
                                isEmployee: 0,
                                isLegal: 0,
                                payerSameGetter: 1,
                            },
                            dataType: 'json',
                            success: function (response) {
                                if (response.result === false) {
                                    form.find('.error-message')
                                        .html('Не удалось создать пользователя.<br/> Попробуйте  позже.')
                                        .addClass('active')

                                    preloader.hide()

                                    return false;
                                } else {
                                    window.dataLayer.push({
                                        event: response.isNewUser ? 'sign_up' : 'login',
                                        method: 'sms'
                                    })
                                    clearStorage()
                                    redirectPage()
                                }
                            }
                        })
                    }
                }
            })
            // }
        })
        //endregion

        //region Подтвержддение пароля
        $(document).on('submit', '#signin[data-type="submit-pass"], #registration[data-type="submit-pass"]', function (e) {
            e.preventDefault()
            let form = $(this)
            let pass = form.serializeArray()[0].value
            let secondPass = form.serializeArray()[1].value
            let type = localStorage.getItem('type')

            let pin = localStorage.getItem('pin')
            let pinKey = pin.includes('@') ? 'email' : 'phone'

            let data = {
                [param]: token,
                [pinKey]: pin,
                password: pass,
                isNew: type == 'regular' ? 0 : 1
            }

            if (pass == ''
                || secondPass == ''
                || pass.length < 8
            ) {
                form.find('.error-message')
                    .html('Некорректный пароль.<br/>Пароль должен быть длиной 8 или более символов.')
                    .addClass('active')

                return false
            }

            if (pass != secondPass) {
                form.find('.error-message')
                    .text('Пароли не совпадают.')
                    .addClass('active')

                return false
            }

            if (type == 'regular') {
                data.key = localStorage.getItem('key')
            }

            $.ajax({
                method: "POST",
                url: '/user/save-user',
                data: {...data},
                dataType: 'json',
                success: function (response) {
                    if (response.result === false) {
                        form.find('.error-message')
                            .html('Не удалось создать пользователя.<br/> Попробуйте  позже.')
                            .addClass('active')

                        return false;
                    } else {
                        if (type == 'regular') {
                            let result = getForm(0, 3, 2, {
                                activeTab: 1
                            });

                            result.then((data) => {
                                popupContainer.html(data.content)

                                emailRegex = IMask(
                                    document.getElementById('popup-email-log'),
                                    {
                                        mask: /^[a-zA-Z\d\.\_\-\+0-9@]+$/
                                    });

                                password = IMask(
                                    document.getElementById('password'),
                                    {
                                        mask: /\S+/
                                    });
                            })
                        } else {
                            window.dataLayer.push({
                                event: 'sign_up',
                                method: 'mail'
                            })
                            clearStorage()
                            redirectPage()
                        }

                        clearStorage()
                    }
                }
            })
        })
        //endregion

        //region Вход по телефону
        $(document).on('submit', '#signin[data-role="same"]', function (e) {
            e.preventDefault()
            if (e.target.dataset.blocked === 'true') return

            let form = $(this)
            form.find('.error-message')
                .text('')
                .removeClass('active')

            if (validatePhone(phoneRegex.value)) {
                e.target.dataset.blocked = true
                e.target.querySelector('.js-get-code-btn').classList.add('button-disable')

                $.ajax({
                    method: "POST",
                    url: '/user/send-pin',
                    data: {
                        [param]: token,
                        [form.serializeArray()[0]['name']]: form.serializeArray()[0]['value'],
                        isNew: 0
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.result === false) {
                            form.find('.error-message')
                                .text('Не удалось отправить код. Попробуйте позже.')
                                .addClass('active')

                            e.target.dataset.blocked = false
                            e.target.querySelector('.js-get-code-btn').classList.remove('button-disable')

                            return false;
                        } else if (response.result !== true) {
                            form.find('.error-message')
                                .text(response.result)
                                .addClass('active')

                            e.target.dataset.blocked = false
                            e.target.querySelector('.js-get-code-btn').classList.remove('button-disable')
                            return false;
                        }

                        localStorage.setItem('pin', form.serializeArray()[0]['value'])
                        localStorage.setItem('key', response.keyHash)

                        let result = getForm(1, 0, 0, {
                            phone: localStorage.getItem('pin')
                        })

                        result.then((data) => {
                            popupContainer.html(data.content)

                            $('#popup-code-1').focus()

                            let resender = $('#resender')
                            let timerTime = resender.data('timer')

                            let timer = setInterval(() => {
                                if (timerTime == 0) {
                                    resender.find('span').text('')
                                    resender.removeClass('button-disable')
                                    clearInterval(timer)
                                } else {
                                    resender.find('span').text(timerTime)
                                    timerTime -= 1
                                }
                            }, 1000)
                        })
                            .then(() => backList.push('login'))
                    },
                    error: function() {
                        e.target.dataset.blocked = false
                        e.target.querySelector('.js-get-code-btn').classList.remove('button-disable')
                    }
                })
            } else {
                form.find('.error-message')
                    .text('Некорректный номер телефона')
                    .addClass('active')
            }
        })
        //endregion

        //region Вход по паролю
        function openFormLoginPassword(registration) {
            let result = getForm(0, 3, 2, {
                activeTab: registration ? 2 : 1,
                title: 'Войти',
                buttonBack: true,
            });

            result.then((data) => {
                popupContainer.html(data.content)

                emailRegex = IMask(
                    document.getElementById('popup-email-log'),
                    {
                        mask: /^[a-zA-Z\d\.\_\-\+0-9@]+$/
                    });
            })
                .then(() => backList.includes('loginPassword') ? '' : backList.push('loginPassword'))

        }

        $(document).on('click', '.popup .login-pass', function (e) {
            e.preventDefault()

            if (e.originalEvent.pointerType == 'mouse'
                || (e.screenX && e.screenX != 0 && e.screenY && e.screenY != 0)
            ) {
                openFormLoginPassword(false)
            } else {
                $('#signin[data-role="same"]').trigger('submit')
            }
        })
        //endregion

        //region Валидация данных
        $(document).on('submit', '#signin[data-role="regular"]', function (e) {
            e.preventDefault()
            if (e.target.dataset.blocked === 'true') return

            let form = $(this)
            let values = form.serializeArray()

            form.find('.error-message')
                .text('')
                .removeClass('active')

            if (validateEmail(emailRegex.value)) {
                e.target.dataset.blocked = true
                e.target.querySelector('.js-password-btn').classList.add('button-disable')

                let preloader = new Preloader('.js-popup-container')

                preloader.show()

                $.ajax({
                    method: "POST",
                    url: '/user/auth-user',
                    data: {
                        [param]: token,
                        [values[0]['name']]: values[0]['value'],
                        [values[1]['name']]: values[1]['value'],
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.result === false) {
                            form.find('.error-message')
                                .text('Неверные логин и пароль.')
                                .addClass('active')

                            e.target.dataset.blocked = false
                            e.target.querySelector('.js-password-btn').classList.remove('button-disable')
                            preloader.hide()

                            return false;
                        }
                        window.dataLayer.push({
                            event: 'login',
                            method: 'mail'
                        })

                        redirectPage()
                    },
                    error: function() {
                        e.target.dataset.blocked = false
                        e.target.querySelector('.js-password-btn').classList.remove('button-disable')
                    }
                })
            } else {
                form.find('.error-message')
                    .text('Некорректный E-mail')
                    .addClass('active')
            }
        })

        //endregion

        function openFormRestore(registration) {

            let result = getForm(0, 4, 2, {
                activeTab: registration ? 2 : 1,
                title: 'Восстановить пароль',
                buttonBack: true
            });

            result.then((data) => {
                popupContainer.html(data.content)

                emailSecRegex = IMask(
                    document.getElementById('popup-email-restore'),
                    {
                        mask: /^[a-zA-Z\d\.\_\-\+0-9@]+$/
                    });
            })
                .then(() => backList.includes('restorePassword') ? '' : backList.push('restorePassword'))
        }

        //region Восстановление пароля
        $(document).on('click', '#signin .button-restore', function (e) {
            e.preventDefault()

            openFormRestore()
        })
        //endregion


        //region отправка кода при восстановлении
        $(document).on('submit', '#restore', function (e) {
            e.preventDefault()
            let form = $(this)
            form.find('.error-message')
                .text('')
                .removeClass('active')

            if (validateEmail(emailSecRegex.value)) {
                $.ajax({
                    method: "POST",
                    url: '/user/send-pin',
                    data: {
                        [param]: token,
                        [form.serializeArray()[0]['name']]: form.serializeArray()[0]['value'],
                        isNew: 0
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.result === false) {
                            form.find('.error-message')
                                .text('Не удалось отправить код. Попробуйте позже.')
                                .addClass('active')

                            return false;
                        } else if (response.result !== true) {
                            form.find('.error-message')
                                .text(response.result)
                                .addClass('active')

                            return false;
                        }

                        localStorage.setItem('pin', form.serializeArray()[0]['value'])
                        localStorage.setItem('type', 'regular')
                        localStorage.setItem('key', response.keyHash)

                        let result = getForm(1, 0, 0, {
                            email: localStorage.getItem('pin')
                        })

                        result.then((data) => {
                            popupContainer.html(data.content)

                            $('#popup-code-1').focus()

                            let resender = $('#resender')
                            let timerTime = resender.data('timer')

                            let timer = setInterval(() => {
                                if (timerTime == 0) {
                                    resender.find('span').text('')
                                    // resender.addClass('button-black')
                                    resender.removeClass('button-disable')
                                    clearInterval(timer)
                                } else {
                                    resender.find('span').text(timerTime)
                                    timerTime -= 1
                                }
                            }, 1000)
                        })
                            .then(() => backList.push('restorePasswordCode'))
                    }
                })
            } else {
                form.find('.error-message')
                    .text('Некорректный E-mail')
                    .addClass('active')
            }
        })
        //endregion

        /*************Всякое разное****************/
        //Доп проверка для полей кода подтверждения, т.к. inputMask как-то коряво работет
        //Авто перемещение курсора при вводе кода
        $(document).on('input', '#popup-code-1, #popup-code-2, #popup-code-3, #popup-code-4, #popup-code-5', function (e) {
            let input = $(this)
            let val = input.val()

            if (val == '') {
                e.preventDefault()
                input.val('')
                return false
            }

            let digits = '0123456789'

            if (val.length > 1) {
                e.preventDefault()
                input.val(val[0])
            } else if (digits.indexOf(val) === -1) {
                e.preventDefault()
                input.val('')
            } else {
                let inputNum = input.attr('id').split('-')
                let nextNum = parseInt(inputNum[2]) + 1

                if (e.target.closest('#popup-code-1')) {
                    const errorMessage = e.target.closest('#verify-code').querySelector('.error-message')
                    
                    errorMessage.classList.remove('active')
                    errorMessage.innerText = ''
                }

                if (nextNum < 6) {
                    $('#popup-code-' + nextNum).focus()
                } else {
                    $('#verify-code').trigger('submit')
                }
            }

        })

        //Авто перемещение курсора при стирании кода
        $(document).on('keydown', '.popup__input-code', function (e) {
            let input = $(this)

            if (
                input.val() == ''
                && (e.keyCode == 8 || e.keyCode == 46)
            ) {
                let inputNum = input.attr('id').split('-')
                let prevNum = parseInt(inputNum[2]) - 1

                $('#popup-code-' + prevNum).focus()
            }
        });

        //Кнопка повторной отправки
        $(document).on('click', '#resender', function (e) {
            e.preventDefault()

            if (
                e.originalEvent.pointerType == 'mouse'
                || (e.screenX && e.screenX != 0 && e.screenY && e.screenY != 0) //Нужна петиция по удалению Firefox из истории человечества
            ) {
                if ($(this).hasClass('button-disable')) {
                    return false
                }

                let resender = $('#resender')
                let timerTime = resender.data('timer')
                let pin = localStorage.getItem('pin')
                let pinKey = pin.includes('@') ? 'email' : 'phone'

                resender.addClass('button-disable')

                let timer = setInterval(() => {
                    if (timerTime == 0) {
                        resender.find('span').text('')
                        resender.removeClass('button-disable')
                        clearInterval(timer)
                    } else {
                        resender.find('span').text(timerTime)
                        timerTime -= 1
                    }
                }, 1000)

                $.ajax({
                    method: "POST",
                    url: '/user/send-pin',
                    data: {
                        [param]: token,
                        [pinKey]: pin,
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.result === 'false') {
                            form.find('.error-message')
                                .text('Не удалось отправить код. Попробуйте позже.')
                                .addClass('active')
                        } else {
                            localStorage.setItem('key', response.keyHash)
                        }
                    }
                })
            } else {
                $('#verify-code').trigger('submit')
            }
        })

        //Переключение табов
        $(document).on('click', '.popup__tabs .b-nav-tab', (event) => {
            let targetEl = $(event.target)
            let activeEl = $('.popup__tabs .b-nav-tab.active')
            let formClass = targetEl.data('tab')

            activeEl.removeClass('active')
            targetEl.addClass('active')

            $(`.popup .${activeEl.data('tab')}`).removeClass('active').addClass('disabled')
            $(`.popup .${formClass}`).removeClass('disabled').addClass('active')
        })

        //Закрытие popup при клике вне формы
        $(document).on('click', '.popup__overlay, .js-popup-button-close', function (e) {
            clearStorage()
            $(e.target).closest('.js-popup-modal').remove()
            toggleHiddenBodyScroll('scroll')
        })

        //Закрытие popup при нажатии Esc
        $(document).keydown(function (e) {
            if (e.keyCode == 27) {
                popupContainer.html('')
                toggleHiddenBodyScroll('scroll')
                clearStorage()
            }
        });

        //Глаз для пароля
        $(document).on('click', '.popup__password-button.popup__password-button_on', function (e) {
            e.preventDefault()
            let btn = $(this)

            btn.removeClass('popup__password-button_on').addClass('popup__password-button_off')
            btn.parent().find('input').attr('type', 'password')
        })

        $(document).on('click', '.popup__password-button.popup__password-button_off', function (e) {
            e.preventDefault()
            let btn = $(this)

            btn.removeClass('popup__password-button_off').addClass('popup__password-button_on')
            btn.parent().find('input').attr('type', 'text')
        })

        $(document).on('keydown', '#popup-phone', function (e) {
            if (e.code == 'Backspace') {
                const input = $(this);
                const val = input.val();
                if (val.length <= 3 || val === '+7 (') {
                    input.val('+7')
                    e.preventDefault();
                }
            }
        });
    })
})
