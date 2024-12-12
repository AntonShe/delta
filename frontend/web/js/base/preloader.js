class Preloader {
    constructor(container = '.main') {
        this.container = $(container).length > 0 ? container : '.main'
    }
    show() {
        $('<div class="preloader js-preloader"></div>').appendTo(this.container)
    }
    hide() {
        $('.js-preloader').remove()
    }
}