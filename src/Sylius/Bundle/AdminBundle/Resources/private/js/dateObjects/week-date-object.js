export default class WeekDateObject {
  constructor(date) {
    const weekInMilliseconds = 604800000;
    const dateNow = new Date();
    this.startDate = new Date(date.getTime() - weekInMilliseconds);
    this.endDate = new Date(date.getTime() + weekInMilliseconds);
    this.prevDate = new Date(date.getTime() - (2 * weekInMilliseconds));
    this.nextDate = new Date(date.getTime() + (3 * weekInMilliseconds));
    this.maxGraphDate = new Date(dateNow.getTime() + (2 * weekInMilliseconds));
    this.interval = 'day';
  }
}
