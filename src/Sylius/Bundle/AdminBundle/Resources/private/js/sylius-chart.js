import 'chart.js/dist/Chart.min';

const drawChart = function drawChart(canvas) {
  const labels = canvas.getAttribute('data-labels');
  const values = canvas.getAttribute('data-values');
  const currency = canvas.getAttribute('data-currency');

  const chartElement = new Chart(canvas, {
    type: 'bar',
    data: {
      labels: JSON.parse(labels),
      datasets: [{
        data: JSON.parse(values),
        backgroundColor: 'rgba(26, 187, 156, 0.3)',
        borderColor: 'rgba(26, 187, 156, 1)',
        borderWidth: 1,
      }],
    },
    options: {
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true,
            callback: function (value) {
              return currency + value;
            },
          },
        }],
        xAxes: [{
          gridLines: {
            display: false,
          },
        }],
      },
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        display: false,
      },
    },
  });
};

const canvas = document.getElementById('dashboard-chart');
if (canvas) {
  drawChart(canvas);
}
