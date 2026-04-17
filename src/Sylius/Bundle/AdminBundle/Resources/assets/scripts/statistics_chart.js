/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import ApexCharts from 'apexcharts';

let chart = null;
function renderChart() {
    // eslint-disable-next-line no-undef
    const statisticsChart = document.querySelector('#statistics-chart');

    if (!statisticsChart) {
        return;
    }

    const styles = getComputedStyle(document.documentElement);
    const labelColor = styles.getPropertyValue('--tblr-body-color').trim();
    const primaryColor = styles.getPropertyValue('--tblr-primary').trim();
    const primaryDarken = styles.getPropertyValue('--sylius-primary-darken').trim();
    const secondaryColor = styles.getPropertyValue('--tblr-info').trim();
    const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';

    const options = {
        theme: {
            mode: currentTheme
        },
        colors: [primaryColor, secondaryColor],
        fill: {
            colors: [primaryColor]
        },
        series: [{
            name: 'Sales',
            data: JSON.parse(statisticsChart.dataset.sales)
        }, {
            name: 'Paid orders count',
            data: JSON.parse(statisticsChart.dataset.paidOrdersCount)
        }],
        chart: {
            background: 'transparent',
            toolbar: {
                show: false
            },
            height: 350,
            type: 'line'
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        xaxis: {
            categories: JSON.parse(statisticsChart.dataset.intervals),
            position: 'top',
            labels: {
                style: {
                    colors: labelColor
                }
            },
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            crosshairs: {
                fill: {
                    type: 'gradient',
                    gradient: {
                        colorFrom: primaryColor,
                        colorTo: primaryDarken,
                        stops: [0, 100],
                        opacityFrom: 0.4,
                        opacityTo: 0.5
                    }
                }
            },
            tooltip: {
                enabled: true
            }
        },
        yaxis: [{
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            labels: {
                style: {
                    colors: labelColor
                },
                formatter(val) {
                    const { currency } = statisticsChart.dataset;
                    return `${currency}${val}`;
                }
            }
        }, {
            opposite: true,
            labels: {
                style: {
                    colors: labelColor
                },
                formatter(val) {
                    return val;
                }
            }
        }],
        title: {
            floating: true,
            offsetY: 330,
            align: 'center',
            style: {
                color: labelColor
            }
        }
    };

    chart = new ApexCharts(statisticsChart, options);
    chart.render();
}

renderChart();

const element = document.querySelector('#statistics-chart');

if (element) {
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'data-sales' || mutation.attributeName === 'data-intervals') {
                if (chart) {
                    chart.destroy();
                }
                renderChart();
            }
        });
    });

    observer.observe(element, {
        attributes: true
    });

    const themeObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'data-bs-theme') {
                if (!document.querySelector('#statistics-chart')) {
                    themeObserver.disconnect();
                    return;
                }
                if (chart) {
                    chart.destroy();
                }
                renderChart();
            }
        });
    });

    themeObserver.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-bs-theme']
    });
}
