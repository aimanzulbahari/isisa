import { chartTooltip, axisTicks, gridStyle, bottomLegend } from './chart-defaults.js';

/**
 * Carta bar bertindan
 */
export function initStackedBar() {
    const stackedBarCanvas = document.getElementById('isisaStackedBar');
    if (stackedBarCanvas && typeof Chart !== 'undefined') {
        // PHP: Gantikan data dengan JSON dari backend
        new Chart(stackedBarCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Jun', 'Jul', 'Ogos', 'Sep', 'Okt', 'Nov'],
                datasets: [{
                    label: 'Diluluskan',
                    data: [120, 138, 126, 158, 176, 164],
                    backgroundColor: '#386641'
                },
                {
                    label: 'Semakan',
                    data: [28, 30, 32, 34, 36, 32],
                    backgroundColor: '#a7c957'
                },
                {
                    label: 'Perlu Tindakan',
                    data: [12, 14, 12, 13, 16, 18],
                    backgroundColor: '#e9c46a',
                    borderRadius: {
                        topLeft: 8,
                        topRight: 8
                    }
                }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: bottomLegend,
                    tooltip: chartTooltip
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            display: false
                        },
                        ticks: axisTicks
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        border: {
                            display: false
                        },
                        grid: gridStyle,
                        ticks: {
                            ...axisTicks,
                            padding: 8
                        }
                    }
                },
                datasets: {
                    bar: {
                        barPercentage: 0.55,
                        categoryPercentage: 0.7
                    }
                }
            }
        });
    }
}
