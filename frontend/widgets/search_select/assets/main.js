const lettersTranslit = {
    "f": 'а',
    ",": 'б',
    "d": 'в',
    "u": 'г',
    "l": 'д',
    "t": 'е',
    "`": 'ё',
    ";": 'ж',
    "p": 'з',
    "b": 'и',
    "q": 'й',
    "r": 'к',
    "k": 'л',
    "v": 'м',
    "y": 'н',
    "j": 'о',
    "g": 'п',
    "h": 'р',
    "c": 'с',
    "n": 'т',
    "e": 'у',
    "a": 'ф',
    "[": 'х',
    "w": 'ц',
    "x": 'ч',
    "i": 'ш',
    "o": 'щ',
    "]": 'ъ',
    "s": 'ы',
    "m": 'ь',
    "'": 'э',
    ".": 'ю',
    "z": 'я',
}

function getMatchesList(list, search) {
    let parsedSearch = ''
    let result = []

    search = search.toLowerCase()

    for (let i = 0; i < search.length; i++)
    {
        parsedSearch += lettersTranslit[search[i]] !== undefined
            ? lettersTranslit[search[i]]
            : search[i]
    }

    list.forEach((item, index) => {
        if (
            (item == parsedSearch || (item.indexOf(parsedSearch) >= 0))
            && result.length < 5
        ) {
            result.push(index)
        }
    })

    return result
}

function getActiveItem() {
    return $('.search-select .search-dropdown .search-dropdown__item.active')
}

function getCountItems() {
    return $('.search-select .search-dropdown .search-dropdown__item').length
}

/**
 * direct = 0 - up
 * direct = 1 - down
 */
function setActiveItem(direct) {
    let currentItem = getActiveItem()
    let index = currentItem.length == 0 ? 0 : currentItem.data('index')
    let max = getCountItems()

    if (direct === 1 && index < max) {
        index++
        currentItem.removeClass('active')
        $('.search-select .search-dropdown .search-dropdown__item[data-index="'+index+'"]').addClass('active')
    }

    if (direct === 0 && index > 1) {
        index--
        currentItem.removeClass('active')
        $('.search-select .search-dropdown .search-dropdown__item[data-index="'+index+'"]').addClass('active')
    }
}

function setSearchedList(list) {
    const wrapper = $('.search-select .search-dropdown')
    let newHtml = ''
    let index = 0

    for (const item of list) {
        index++
        newHtml += '<div class="search-dropdown__item" data-index="'+index+'">' + item + '</div>'
    }

    wrapper.html(newHtml)
    wrapper.removeClass('disable')
}

$(document).ready(function (){
    let valuesList = []
    let visibleList = []

    $('input[id^="search-select-item"]').each(function (index, item) {
        let valItem = $(item).val()
        valuesList.push(valItem.toLowerCase())
        visibleList.push(valItem)
    })

    $(document).on('input', '.search-select .search-select__input', function (e) {
        let searchedList = getMatchesList(valuesList, $(this).val())
        let resultList = []

        for (const item of searchedList) {
            resultList.push(visibleList[item])
        }

        setSearchedList(resultList)
    })

    $(document).on('blur', '.search-select .search-select__input', function (e, data) {
        if (data == undefined) {
            setTimeout(() => {
                $('.search-select .search-dropdown').addClass('disable')
            }, 200)
        } else {
            $('.search-select .search-dropdown').addClass('disable')
        }
    })

    $(document).on('focus', '.search-select .search-select__input', function (e) {
        let item = $(this)

        if (item.val().length > 0) item.trigger('input')
    })

    $(document).on('click', '.search-select .search-dropdown .search-dropdown__item', function (e) {
        $('.search-select .search-select__input').val($(this).text())
        $('.search-select .search-select__input').trigger('blur', [{isCustom:true}])
        $('.search-select .search-select__input').trigger('change')
    })

    $(document).on('keydown', '.search-select .search-select__input', function (e) {
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault()
                setActiveItem(1)
                break
            case 'ArrowUp':
                e.preventDefault()
                setActiveItem(0)
                break
            case 'Enter':
                e.preventDefault()
                let activeItem = getActiveItem()
                let self = $(this)

                self.val(activeItem.text())
                self.trigger('blur', [{isCustom:true}])
                break
        }
    })
})