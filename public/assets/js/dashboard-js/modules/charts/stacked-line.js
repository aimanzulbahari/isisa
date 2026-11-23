import { chartTooltip, axisTicks, gridStyle, bottomLegend } from './chart-defaults.js';

/**
 * Carta garisan bertindan
 */
export function initStackedLine() {
    const stackedLineCanvas = document.getElementById('isisaStackedLine');
    if (stackedLineCanvas && typeof Chart !== 'undefined') {
        // PHP: Gantikan data dengan JSON dari backend
        new Chart(stackedLineCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['M1', 'M2', 'M3', 'M4', 'M5', 'M6', 'M7', 'M8'],
                datasets: [{
                    label: 'Sisa Domestik',
                    data: [42, 45, 44, 48, 47, 51, 50, 54],
                    borderColor: '#386641',
                    backgroundColor: 'rgba(56, 102, 65, 0.55)'
                },
                {
                    label: 'Sisa Komersial',
                    data: [26, 28, 27, 30, 31, 33, 32, 35],
                    borderColor: '#6a994e',
                    backgroundColor: 'rgba(106, 153, 78, 0.55)'
                },
                {
                    label: 'Kitar Semula',
                    data: [10, 12, 14, 15, 17, 18, 20, 23],
                    borderColor: '#d4a63a',
                    backgroundColor: 'rgba(233, 196, 106, 0.6)'
                }
                ].map(ds => ({
                    ...ds,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 5
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: bottomLegend,
                    tooltip: chartTooltip
                },
                scales: {
                    x: {
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
                }
            }
        });
    }
}
