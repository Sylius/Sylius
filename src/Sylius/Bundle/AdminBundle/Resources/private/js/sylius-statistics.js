import $ from 'jquery';
import drawChart from './sylius-chart';

class StatisticsComponent {
  constructor(wrapper) {
    if (!wrapper) return;

    this.weekInMiliSeconds = 604800000;
    this.wrapper = wrapper;
    this.chart = null;
    this.chartCanvas = this.wrapper.querySelector('#stats-graph');
    this.summaryBoxes = this.wrapper.querySelectorAll('[data-stats-summary]');
    this.buttons = this.wrapper.querySelectorAll('[data-stats-button]');
    this.loader = this.wrapper.querySelector('.stats-loader');

    this.init();
  }

  init() {
    const defaultInterval = 'year';

    this.buttons.forEach((button) => {
      button.addEventListener('click', this.fetchData.bind(this));
      if (button.getAttribute('data-stats-button') === defaultInterval) {
        button.classList.add('active');
      }
    });

    this.initializeNavButtons(defaultInterval);

    const labels = this.chartCanvas.getAttribute('data-labels') || '[]';
    const values = this.chartCanvas.getAttribute('data-values') || '[]';
    const currency = this.chartCanvas.getAttribute('data-currency') || '';

    this.chart = drawChart(this.chartCanvas, JSON.parse(labels), JSON.parse(values), { prefix: currency });
  }

  initializeNavButtons(defaultInterval) {
    this.prevButton = document.getElementById('navigation-prev');
    this.nextButton = document.getElementById('navigation-next');

    this.prevButton.addEventListener('click', this.fetchData.bind(this));
    this.nextButton.addEventListener('click', this.fetchData.bind(this));

    const date = new Date();

    this.updateNavButtons(
      defaultInterval,
      new Date(date.getFullYear(), 0, 1),
      new Date(date.getFullYear() + 1, 0, 0)
    );
  }

  fetchData(e) {
    let date = new Date();
    if (e.target.getAttribute('date')) {
      date = new Date(e.target.getAttribute('date'));
    }

    let interval = e.target.getAttribute('data-stats-button') || e.target.getAttribute('interval');
    let startDate;
    let endDate;
    let prevDate;
    let nextDate;

    switch (interval) {
      case 'year':
        startDate = new Date(date.getFullYear(), 0, 1);
        endDate = new Date(date.getFullYear() + 1, 0, 0);
        prevDate = this.formatDate(new Date(date.getFullYear() - 1, date.getMonth(), 1));
        nextDate = this.formatDate(new Date(date.getFullYear() + 1, date.getMonth(), 1));
        this.updateNavButtons(interval, prevDate, nextDate);
        interval = 'month';
        break;
      case 'month':
        startDate = new Date(date.getFullYear(), date.getMonth(), 1);
        endDate = new Date(date.getFullYear(), date.getMonth() + 1, 1);
        prevDate = this.formatDate(new Date(date.getFullYear(), date.getMonth() - 1, 1));
        nextDate = this.formatDate(new Date(date.getFullYear(), date.getMonth() + 1, 1));
        this.updateNavButtons(interval, prevDate, nextDate);
        interval = 'day';
        break;
      case 'week':
        startDate = new Date(date.getTime() - this.weekInMiliSeconds);
        endDate = new Date(date.getTime() + this.weekInMiliSeconds);
        prevDate = this.formatDate(new Date(date.getTime() - (2 * this.weekInMiliSeconds)));
        nextDate = this.formatDate(new Date(date.getTime() + (3 * this.weekInMiliSeconds)));
        this.updateNavButtons(interval, prevDate, nextDate);
        interval = 'day';
        break;
    }

    const url = `${e.target.getAttribute('data-stats-url')
    }&interval=${interval
    }&startDate=${this.formatDate(startDate)
    }&endDate=${this.formatDate(endDate)}`;

    if (url) {
      this.toggleLoadingState(true);

      $.ajax({
        type: 'GET',
        url,
        dataType: 'json',
        accept: 'application/json',
      }).done((response) => {
        this.updateSummaryValues(response.statistics);
        this.updateButtonsState(e.target);
        this.updateGraph(response.sales_summary);
      }).always(() => {
        this.toggleLoadingState(false);
      });
    }
  }

  updateSummaryValues(data) {
    this.summaryBoxes.forEach((box) => {
      const name = box.getAttribute('data-stats-summary');
      if (name in data) {
        box.innerHTML = data[name];
      }
    });
  }

  updateGraph(data) {
    this.chart.data.labels = data.intervals;
    this.chart.data.datasets[0].data = data.sales;
    this.chart.update();
  }

  updateButtonsState(activeButton) {
    const interval = activeButton.getAttribute('data-stats-button') ? activeButton.getAttribute('data-stats-button')
      : activeButton.getAttribute('interval');

    this.buttons.forEach((button) => {
      button.classList.remove('active');
      if (button.getAttribute('data-stats-button') === interval) {
        button.classList.add('active');
      }
    });
  }

  toggleLoadingState(loading) {
    if (loading) {
      this.loader.classList.add('active');
    } else {
      this.loader.classList.remove('active');
    }
  }

  formatDate(date) {
    let month = `${(date.getMonth() + 1)}`;
    let day = `${date.getDate()}`;
    const year = `${date.getFullYear()}`;

    if (month.length < 2) month = `0${month}`;
    if (day.length < 2) day = `0${day}`;

    return [year, month, day].join('-');
  }

  setInterval(element, interval) {
    element.setAttribute('interval', interval);
  }

  updateNavButtons(interval, prevDate, nextDate) {
    this.prevButton.setAttribute('interval', interval);
    this.nextButton.setAttribute('interval', interval);

    this.prevButton.setAttribute('date', prevDate);
    this.nextButton.setAttribute('date', nextDate);
  }
}

export default StatisticsComponent;
