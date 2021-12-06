export default class MonthDateObject {
  constructor(date) {
    const dateNow = new Date();
    this.startDate = new Date(date.getFullYear(), date.getMonth(), 1);
    this.endDate = new Date(date.getFullYear(), date.getMonth() + 1, 1);
    this.prevDate = new Date(date.getFullYear(), date.getMonth() - 1, 1);
    this.nextDate = new Date(date.getFullYear(), date.getMonth() + 1, 1);
    this.maxGraphDate = new Date(dateNow.getFullYear(), dateNow.getMonth() + 1, 0);
    this.interval = 'day';
  }
}
