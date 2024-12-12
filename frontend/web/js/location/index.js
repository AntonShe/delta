document.addEventListener('DOMContentLoaded', () => {
    const param = $('meta[name="csrf-param"]').attr("content")
    const token = $('meta[name="csrf-token"]').attr("content")
    let location = 'Москва'
    const citySmartList = [
        'Москва',
        'Красноярск',        
        'Санкт-Петербург',
        'Нижний Новгород',        
        'Владивосток',
        'Самара',
        'Воронеж',
        'Саратов',        
        'Екатеринбург',
        'Тверь',        
        'Казань',
        'Челябинск'
    ]
    
    const popupContainer = document.querySelector('.js-location-container')

    // закрытие попапов
    function closePopup() {
        popupContainer.innerHTML = ''
        if (document.body.classList.contains('overflow-hidden')) document.body.classList.remove('overflow-hidden')
    }

    // город выбран из списка
    function userLocationChosen() {
        setUserLocation()
        closePopup()
    }    

    // общая инициализация попапов
    function popupInit() {
        document.querySelector('.popup__overlay')?.addEventListener('click', closePopup)
        document.querySelector('.js-popup-button-close')?.addEventListener('click', closePopup)
        document.querySelector('.js-city-list-close')?.addEventListener('click', closePopup)
    }

    // инициализация первого попапа с городом
    function popupCityInit() {
        const changeLocationBtn = document.querySelector('.js-location-change-btn')
        const closeLocationBtns = document.querySelectorAll('.js-location-close')

        changeLocationBtn?.addEventListener('click', showCityList)
        closeLocationBtns?.forEach(button => {
            button.addEventListener('click', userLocationChosen)
        })
    }    

    // инициализация попапа со списком городов
    function popupCityListInit() {
        const citiesListWrapper = document.querySelector('.js-cities-list')

        const citiesListString = citySmartList.map(item => `<div class="popup-cities__item js-cities-item" data-city="${item}">${item}</div>`).join("")
        citiesListWrapper.innerHTML = citiesListString

        citiesListWrapper.addEventListener('click', (e) => {
            if (e.target.closest('.js-cities-item')) {
                const cityChosen = e.target.closest('.js-cities-item').dataset.city

                location = cityChosen
                userLocationChosen()
            }
        })

        if (!document.body.classList.contains('overflow-hidden')) document.body.classList.add('overflow-hidden')
    }

    // первый попап
    function showUserCity() {
        popupContainer.innerHTML = 
            `<div class="popup-location js-popup-modal-location">
                <div class="popup-location__container">
                    <div class="popup__header popup-location__header">
                        <div class="popup-location__cross js-location-close"></div>
                        <div class="popup-location__title">Ваш город</div>
                        <div>${location}?</div>
                    </div>
                    <div class="popup-location__body">
                        От выбранного города зависят способы доставки
                    </div>
                    <div class="popup-location__buttons flex">
                        <button class="popup__button button-red-200 b-nav-tab js-location-change-btn">Нет, изменить</button>
                        <button class="popup__button button-black b-nav-tab js-location-close">Да, я здесь</button>
                    </div>
                </div>
            </div>`

        popupInit()
        popupCityInit()
    }

    // второй попап
    function showCityList() {
        popupContainer.innerHTML = 
            `<div class="popup modal">
                <div class="popup__overlay"></div>
                <div class="popup__container">
                    <div class="flex popup__header popup__header--desk">
                        <div class="popup__header-title">Выберите город</div>
                    </div>
                    <div class="popup__header-mobile">
                        <div class="popup__header-logo">
                            <img src="/img/logo-header.svg" alt="Дельтабук">
                        </div>                    
                        <div class="popup__header-mobile-wrapper">
                            <button class="popup__header-button button-back js-city-list-close"></button>
                            <div class="popup__header-title">Выберите город</div>                   
                        </div>
                    </div>
                    <div class="popup-cities__body">
                        <div class="popup-cities__input">
                            <div class="input-autocomplete__container">
                                <input class="input-autocomplete js-autocomplete" type="text" placeholder="Название города" autocomplete="off" />
                                <div class="input-autocomplete__close js-input-autocomplete__close"></div>
                            </div>
                            <div class="input-autocomplete__list js-input-autocomplete__list"></div>
                        </div>
                        <div class="popup-cities__list js-cities-list"></div>
                    </div>
                </div>
            </div>`

        popupInit()
        popupCityListInit()
        addAutocomplete()
    }

    // объект для автозамены английских букв
    const keyRel = {
        "q": "й",
        "w": "ц",
        "e": "у",
        "r": "к",
        "t": "е",
        "y": "н",
        "u": "г",
        "i": "ш",
        "o": "щ",
        "p": "з",
        "[": "х",
        "{": "х",
        "]": "ъ",
        "a": "ф",
        "s": "ы",
        "d": "в",
        "f": "а",
        "g": "п",
        "h": "р",
        "j": "о",
        "k": "л",
        "l": "д",
        ";": "ж",
        ":": "ж",
        "'": "э",
        '"': "э",
        "z": "я",
        "x": "ч",
        "c": "с",
        "v": "м",
        "b": "и",
        "n": "т",
        "m": "ь",
        ",": "б",
        "<": "б",
        ".": "ю",
        ">": "ю",
        "`": "ё"
    }

    function searchInput (event) {
        if (!document.querySelector('.js-autocomplete')) {
            return;
        }

        let search = event.target.value.toLowerCase();

        for (let i = 0; i < search.length; i++) {
            if (keyRel[search[i]] !== undefined) {
                search = search.replace(search[i], keyRel[search[i]])
            }
        }

        $(".js-autocomplete").autocomplete("search", search);
    }

    function addAutocomplete() {
        if (!document.querySelector('.js-autocomplete') || !localStorage.getItem('cityList')) {
            return;
        }

        const citiesList = JSON.parse(localStorage.getItem('cityList'));
        const autocomplete = document.querySelector('.js-autocomplete');

        document.querySelector('.js-input-autocomplete__close').addEventListener('click', () => {
            autocomplete.value = '';
            autocomplete.focus();
        })

        $(".js-autocomplete").autocomplete({
            source: citiesList,
            appendTo: ".js-input-autocomplete__list",
            select: function( event, ui ) {
                location = ui.item.value
                userLocationChosen()
            }
        });

        autocomplete.addEventListener('input', searchInput)
    }

    // получение списка городов
    function setCitiesList() {
        if (localStorage.getItem('cityList')) {
            return;
        }

        axios
            .get('/delivery-profile/get-city-list')
            .then(response => {
                if(!response.data.status) {
                    localStorage.setItem('cityList', JSON.stringify(response.data))
                }
            })
            .catch(error => {
                throw new Error("Ошибка " + error)
            })
    }

    // отправка выбранного города
    function setUserLocation() {
        localStorage.setItem('userLocation', location)
        changeDisplayLocation()

        axios
            .post('/user/set-user-city', {
                city: location,
                [param]: token
            })
            .catch(error => {
                throw new Error("Ошибка " + error)
            })
    }

    // визуализация выбранного города на десктопе, мобилке и в лк
    function changeDisplayLocation() {
        const userLocationWrappers = document.querySelectorAll('.js-user-location')
        const pathName = window.location.pathname
        
        userLocationWrappers.forEach(wrapper => {
            wrapper.textContent = location
        })

        if (pathName.split('/')[1] === 'profile' && pathName !== '/profile/favourite') {
            const userBadgeLocation = document.querySelector('.js-user-badge-city')

            if (!userBadgeLocation) return

            userBadgeLocation.textContent = location
        }
    }

    // получение города пользователя
    function getUserLocation() {
        if (localStorage.getItem('userLocation')) {
            location = localStorage.getItem('userLocation')
            changeDisplayLocation()
        }

        axios
            .get('/user/get-user-city', {
                params: {
                    [param]: token
                }
            })
            .then(response => {
                if (!response.data.isGuest) {
                    const userCity = localStorage.getItem('userLocation')

                    if (userCity && userCity !== response.data.city) {
                        setUserLocation()
                    } else if (response.data.city) {
                        location = response.data.city
                        localStorage.setItem('userLocation', location)
                        changeDisplayLocation()
                    } else {
                        changeDisplayLocation()
                        showUserCity()                        
                    }
                } else if (!localStorage.getItem('userLocation')) {
                    changeDisplayLocation()
                    showUserCity()
                }
            })
            .catch(error => {
                throw new Error("Ошибка " + error)
            })        
    }

    function userLocationInit() {
        const userLocationWrappers = document.querySelectorAll('.js-user-location')

        userLocationWrappers.forEach(wrapper => {
            wrapper.addEventListener('click', showCityList)
        })
        
        getUserLocation()
        setCitiesList()
    }

    userLocationInit()
})