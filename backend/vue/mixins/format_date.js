export const format_date = {
  methods: {
    shortFormatDate(value) {
      const actualDate = new Date(value)
      const month = `0${actualDate.getMonth() + 1}`.slice(-2)
      const day = `0${actualDate.getDate()}`.slice(-2)

      return `${day}.${month}.${actualDate.getFullYear()}`
    }
  }
}