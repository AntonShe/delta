class Sorting {
    constructor(callbackFunction) {
        this.sort = ''
        this.order = ''
        this.callback = callbackFunction

        if ($('#sorting').length) {
            let that = this
            $(document).on('click', '.js-sorting-button:not(.sort__item_active)', function() {
                $('.js-sorting-button').removeClass('sorting__item_active')
                $(this).addClass('sorting__item_active')
                that.sort = $(this).data('sort')
                that.order = $(this).data('order')
                that.setSort()
                that.callback()
            })
        }
    }
    setSort() {
        let url = new URL(location)
        url.searchParams.set('sort', this.sort)
        url.searchParams.set('order', this.order)
        history.pushState({}, '', url)
    }
    clearSort() {
        this.sort = ''
        this.order = ''
        $('.js-sorting-button').removeClass('sorting__item_active')
    }
}