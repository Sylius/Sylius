export default class YearDateObject {
  constructor(date) {
    const dateNow = new Date();
    this.startDate = new Date(date.getFullYear(), 0, 1);
    this.endDate = new Date(date.getFullYear() + 1, 0, 0);
    this.prevDate = new Date(date.getFullYear() - 1, 1, 1);
    this.nextDate = new Date(date.getFullYear() + 1, 1, 1);
    this.maxGraphDate = new Date(dateNow.getFullYear() + 1, 0, 1);
    this.interval = 'month';
  }
}
