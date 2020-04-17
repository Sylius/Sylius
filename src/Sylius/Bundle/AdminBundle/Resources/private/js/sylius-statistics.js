import $ from 'jquery';
import drawChart from './sylius-chart';

class StatisticsComponent {
  constructor(wrapper) {
    if (!wrapper) return;

    this.wrapper = wrapper;
    this.chart = null;
    this.chartCanvas = this.wrapper.querySelector('#stats-graph');
    this.summaryBoxes = this.wrapper.querySelectorAll('[data-stats-summary]');
    this.buttons = this.wrapper.querySelectorAll('[data-stats-button]');
    this.loader = this.wrapper.querySelector('.stats-loader');

    this.buttons.forEach(button => button.addEventListener('click', this.fetchData.bind(this)));
    this.init();
  }

  init() {
    const labels = this.chartCanvas.getAttribute('data-labels') || '[]';
    const values = this.chartCanvas.getAttribute('data-values') || '[]';
    const currency = this.chartCanvas.getAttribute('data-currency') || '';

    this.chart = drawChart(this.chartCanvas, JSON.parse(labels), JSON.parse(values), { prefix: currency });
  }

  fetchData(e) {
    const url = e.target.getAttribute('data-stats-url');

    if (url) {
      this.toggleLoadingState(true);

      $.ajax({
        type: 'GET',
        url,
        dataType: 'json',
        accept: 'application/json'
      }).done((response) => {
        this.updateSummaryValues(response.summary);
        this.updateButtonsState(e.target);
        this.updateGraph(response.chart);
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
    this.chart.data.labels = data.labels;
    this.chart.data.datasets[0].data = data.values;
    this.chart.update();
  }

  updateButtonsState(activeButton) {
    this.buttons.forEach(button => button.classList.remove('active'));
    activeButton.classList.add('active');
  }

  toggleLoadingState(loading) {
    if (loading) {
      this.loader.classList.add('active');
    } else {
      this.loader.classList.remove('active');
    }
  }
}

export default StatisticsComponent;
