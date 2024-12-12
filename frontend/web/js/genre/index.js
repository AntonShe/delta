$(document).ready(function () {

    if (document.querySelector('.js-books-catalog')) {
        dataLayerList(document.querySelector('.js-books-catalog'), null)
    }

    if (!document.querySelector('.js-filters-block')) return;

    let getMainGenre = () => {
            return getUrlPathName().pop()
        },
        normalizeParams = (params) => {
            return Object.assign(params, {levels: [4, 5, 6]})
        },
        getGenres = (params) => {
            axios
                .get(`/catalog/ajax/${getMainGenre()}`, {
                    params: params
                })
                .then(response => {
                    if (!response.data.error) {
                        $('.catalog-courses__block').html(response.data.html)
                    }
                })
                .catch(error => {
                    throw new Error("Ошибка " + error)
                })
        },
        filterSubmitCallbackFunction = () => {
            getGenres(filters.selectedValues)
        },
        filterRefreshCallbackFunction = () => {
            getGenres({})
        }

    let filters = new Filters(filterSubmitCallbackFunction, filterRefreshCallbackFunction)
})