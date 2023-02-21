import Chart from 'chart.js/auto';

const drawChart = function drawChart(canvas, labels = [], values = [], currency) {
  return new Chart(canvas, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        data: values,
        backgroundColor: 'rgba(26, 187, 156, 0.3)',
        borderColor: 'rgba(26, 187, 156, 1)',
        borderWidth: 1,
      }],
    },
    options: {
      scales: {
        yAxes: [{
          gridLines: {
            color: 'rgba(0, 0, 0, 0.05)',
          },
          ticks: {
            beginAtZero: true,
            callback(value) {
              const prefix = currency && currency.prefix ? currency.prefix : '';
              const suffix = currency && currency.suffix ? currency.suffix : '';
              return prefix + value + suffix;
            },
          },
        }],
        xAxes: [{
          gridLines: {
            display: false,
          },
        }],
      },
      layout: {
        padding: {
          left: 20,
          right: 20,
        },
      },
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
      },
    },
  });
};

export default drawChart;
