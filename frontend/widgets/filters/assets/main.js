class Filters {
    constructor(submitCallbackFunction, refreshCallbackFunction) {
        this.acceptedFilters = ['levels', 'publishingHouseId', 'genres', 'words', 'ages', 'parentId']
        this.changed = false
        this.selectedValues = {}
        this.form = $('.js-filters-form')
        this.submitCallbackFunction = submitCallbackFunction
        this.refreshCallbackFunction = refreshCallbackFunction
        this.updateSelectedValues(true).then(() => {
            this.fillFields()
        })

        $(document).on('click', '.js-filters-submit', () => {
            if (this.changed) {
                this.filtersSubmit().then(() => {
                    this.setUrlSearch()
                    this.submitCallbackFunction()
                })
            }
        })
        $(document).on('click', '.js-filters-refresh', () => {
            if (_.keys(this.selectedValues).length) {
                this.filtersRefresh()
            }
        })
        $(document).on('click', '.js-filters-button', () => {
            $('.js-filters-block').toggleClass('active')
        })
        $(document).on('change', '.js-filters-checkboxes-input', () => {
            if (!this.changed) {
                this.changed = true
            }
        })
    }

    async updateSelectedValues(fromUrl = false) {
        if (fromUrl) {
            this.selectedValues = await this.getValues(getUrlSearch())
        } else {
            this.selectedValues = await this.getValues(this.clearedUrlSearch())
        }
    }

    async filtersSubmit() {
        await this.updateSelectedValues()
    }

    clearedUrlSearch() {
        let output = {}
        _.forIn(getUrlSearch(), (value, name) => {
            if (_.indexOf(this.acceptedFilters, name) === -1) {
                output[name] = value
            }
        })

        return output
    }

    filtersRefresh() {
        this.selectedValues = {}
        this.setUrlSearch()
        this.fillFields()
        this.refreshCallbackFunction()
    }

    fillFields() {
        $('.js-filters-checkboxes-input').prop('checked', false)

        _.forIn(this.selectedValues, (value, name) => {
            if (_.isArray(value)) {
                value.forEach((number) => {
                    $(`.js-filters-checkboxes #${name}-${number}`).prop('checked', true)
                })
            }
        })
    }

    getValues(output) {
        $('.js-filters-form').find('.js-filters-checkboxes').each(function () {
            let data = {
                name: $(this).data('name'),
                value: []
            }
            $(this).find('.js-filters-checkboxes-input').each(function () {
                if ($(this).prop('checked')) {
                    data.value.push($(this).val())
                }
            })

            if (data.value.length > 0) {
                output[data.name] = data.value
            }
        })

        return output;
    }
    setUrlSearch() {
        setUrlSearch(this.selectedValues)
        this.changed = false
    }
}
