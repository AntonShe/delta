axios.defaults.headers.common = {
    'X-Requested-With': document.querySelector('meta[name="csrf-param"]').getAttribute("content"),
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute("content")
}

$( document ).on( "ajaxError", function( event, request, settings ) {
    const {status, responseJSON} = request;
    const desc = responseJSON.name + '. ' + responseJSON.message;
    const pathname =  window.location.pathname;
    dataLayerError(status.toString(), desc, pathname, settings.url)
});

const getUrlSearch = function () {
        let output = {}

        if (!window.location.search) {
            return output
        }
        window.location.search
            .replace('?', '')
            .split('&')
            .forEach((item) => {
                let array = item.split('='),
                    field = decodeURIComponent(array[0]),
                    value = decodeURIComponent(array[1])

                if (field.indexOf('[]') !== -1) {
                    field = field.replace('[]', '')

                    if (!output[field]) {
                        output[field] = []
                    }
                    output[field].push(value)
                } else {
                    output[field] = value
                }
            })
        return output
    },
    getUrlPathName = function () {
        return window.location.pathname.split('/')
    },
    encodeUrlSearch = function (params) {
        let array = []

        _.forIn(params, (value, name) => {
            if (_.isArray(value)) {
                value.forEach((number) => {
                    array.push(`${name}[]=${number}`)
                })
            } else {
                array.push(`${name}=${value}`)
            }
        })

        return array.join('&')
    },
    setUrlSearch = function (params) {
        if (history.pushState) {
            let url = new URL(location)
            url.search = encodeUrlSearch(params)
            history.pushState({}, '', url)
        } else {
            console.warn('History API не поддерживается');
        }
    }