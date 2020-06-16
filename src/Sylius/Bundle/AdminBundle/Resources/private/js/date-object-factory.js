import YearDateObject from './dateObjects/year-date-object';
import MonthDateObject from './dateObjects/month-date-object';
import WeekDateObject from './dateObjects/week-date-object';

function DateObjectFactory() {}

DateObjectFactory.prototype.createDateObject = function (interval, date) {
  if (interval === 'month') {
    return new MonthDateObject(date);
  }

  if (interval === 'week') {
    return new WeekDateObject(date);
  }
  
  return new YearDateObject(date);
};

export default DateObjectFactory;
