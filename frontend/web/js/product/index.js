$(document).ready(function () {
    let preloader = new Preloader()
        getMainGenre = () => {
            return getUrlPathName().pop()
        },
        normalizeParams = (params) => {
            let additionalParams = {
                page: pagination.currentPage
            }

            if (sorting.sort !== '') {
                additionalParams.sort = sorting.sort
                additionalParams.order = sorting.order
            }
            return Object.assign(params, additionalParams)
        },
        getProducts = (params, isNotHistory = true) => {
            preloader.show()
            const path = document.location.pathname.split('/')[1]
            
            axios
                .get(`/${path}/ajax/${getMainGenre()}`, {
                    params: params
                })
                .then(response => {
                    if (!response.data.error) {
                        $('.catalog-books__grid').html(response.data.html)

                        pagination.setParams(response.data.pagination, isNotHistory)

                        if (window.pageYOffset > 100) {
                            document.addEventListener('scroll', checkScrollTop);
                            function checkScrollTop () {
                                if (window.pageYOffset <= 100) {
                                    addDataLayerProduct();
                                    document.removeEventListener('scroll', checkScrollTop);
                                }
                            }
                        } else {
                            addDataLayerProduct();
                        }
                    }
                })
                .catch(error => {
                    throw new Error("Ошибка " + error)
                })
                .finally(() => {
                    preloader.hide()
                })
        },
        paginationCallbackFunction = (isNotHistory) => {
            getProducts(normalizeParams(getUrlSearch()), isNotHistory)
        },
        filterSubmitCallbackFunction = () => {
            getProducts(normalizeParams(filters.selectedValues))
        },
        filterRefreshCallbackFunction = () => {
            getProducts(normalizeParams({}))
            sorting.clearSort()
        },
        sortingCallbackFunction = () => {
            getProducts(normalizeParams(filters.selectedValues))
        }

    let pagination = new Pagination(paginationCallbackFunction),
        filters = new Filters(filterSubmitCallbackFunction, filterRefreshCallbackFunction),
        sorting = new Sorting(sortingCallbackFunction)

    addDataLayerProduct()
})

function addDataLayerProduct () {
    if (document.querySelector('.js-catalog-books') && document.querySelector('.js-catalog-books').hasAttributes()) {
        const containerList = document.querySelectorAll('.js-catalog-books');
        containerList.forEach(elList => {
            dataLayerList(elList);
        })
    }
}