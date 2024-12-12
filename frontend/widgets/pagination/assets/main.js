class Pagination {
    constructor(callbackFunction) {
        this.currentPage = 1
        this.pageCount = 1
        this.startPage = 1
        this.endPage = 1
        this.template = ''
        this.callback = callbackFunction
        this.paginationBlock = $('#pagination')

        const pageTypes = [
            'person',
            'publisher',
            'series'
        ]

        if (this.paginationBlock.length) {
            let that = this
            const backButton = document.querySelector('.js-pagination-back')

            $(document).on('click', '.js-pagination-button:not(.pagination__button_disabled)', function() {
                if ($(this).data('page')) {
                    that.currentPage = $(this).data('page')
                    that.callback(true)
                    window.scrollTo(0, 0)
                }
            })

            backButton?.addEventListener('click', () => {window.history.back()})

            window.addEventListener("popstate", () => {
                let pageParam = ''

                setTimeout(() => {
                    const searchParams = new URL(location.href).searchParams
                    
                    if (searchParams.has('page')) {
                        pageParam = searchParams.get('page')
                    } else if (pageTypes.includes(location.pathname.split('/')[1])) {
                        pageParam = 1
                    }
                    
                    if (pageParam) {
                        that.currentPage = pageParam    
                        that.callback(false)
                        window.scrollTo(0, 0)
                    }
                }, 10)
            })
        }
    }
    setParams(data, isNotHistory) {
        this.currentPage = data.currentPage
        this.pageCount = data.pageCount
        this.startPage = data.startPage
        this.endPage = data.endPage
        let url = new URL(location)
        url.searchParams.set('page', data.currentPage)
        if (isNotHistory) history.pushState({}, '', url)
        this.renderPagination()
    }
    renderPagination() {
        this.template = ''
        if (this.pageCount > 1) {
            this.template += `
            <button class="js-pagination-button pagination__button pagination__button-back button-back
                ${(this.currentPage - 1) < 1 ? ' pagination__button_disabled' : ''}"
                data-page="${this.currentPage - 1}"
            ></button>
            ${this.startPage > 1 ? '<a class="js-pagination-button pagination__number" data-page="1">1</a>' +
                '<div class="pagination__number">...</div>' : ''}
        `
            for (let i = this.startPage; i <= this.endPage; i++) {
                this.template += `
                    <a class="js-pagination-button pagination__number${this.currentPage === i ? ' pagination__number_active' : ''}"
                       data-page="${i}"
                    >
                        ${i}
                    </a>
                `
            }
            this.template += `
            ${this.endPage < this.pageCount ?
                `<div class="pagination__number">...</div>
                    <a class="js-pagination-button pagination__number"
                        data-page="${this.pageCount}">${this.pageCount}</a>` : ''}
                <button class="js-pagination-button pagination__button pagination__button-next button-back
                    ${(this.currentPage + 1) > this.pageCount ? ' pagination__button_disabled' : ''}"
                    data-page="${this.currentPage + 1}"
                ></button>
            `;
        }
        this.paginationBlock.html(this.template)
    }
}