const CALCULATOR_METHODS = ['get-services-calc', 'get-services-by-coordinates']

export const calculator = {
    data() {
        return {
            resultOfCalculator: null
        }
    },
    methods: {
        calculate(params, apiMethod = 'get-services-calc') {
            if (!CALCULATOR_METHODS.includes(apiMethod)) {
                throw 'Такого метода для калькулятора нет!'
            }

            return axios.post(`/api/${apiMethod}/`, {Calculator: params})
                .then(response => {
                    this.resultOfCalculator = null

                    if (response.data.error) {
                        throw {message: response.data.error, type: 'registered'}
                    } else {
                        this.resultOfCalculator = response.data
                    }

                    return response.data;
                }).catch((error) => {
                    if (error.type === 'registered') throw error.message
                    throw 'Ошибка при расчете превдарительной стоимости'
                });
        }
    }
};