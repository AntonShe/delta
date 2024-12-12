export const auxiliary_tools = {
    methods: {
        wordEndings(	// Возвращает слово в нужной форме (с правильным окончанием)
            n,			// Число
            thing1, 	// Форма для числа 1
            thing2, 	// Форма для числа 2
            thing5 	    // Форма для числа 5
        ) {
            if (n === 0) return thing5

            let n1 = n % 100,
                n2 = n % 10

            if ((n1 >= 11) && (n1 <= 19)) return thing5
            if ((n2 >= 2) && (n2 <= 4)) return thing2
            if (n2 === 1) return thing1

            return thing5
        },
        diffDates(dayOne, dayTwo) {
            return Math.round((dayOne - dayTwo) / (60 * 60 * 24 * 1000));
        }
    }
}