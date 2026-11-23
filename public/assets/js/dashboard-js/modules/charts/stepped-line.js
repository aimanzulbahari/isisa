import { chartFont, chartTooltip, bottomLegend } from './chart-defaults.js';

/**
 * Carta garisan berperingkat (stepped line)
 */
export function initSteppedLine() {
    const steppedCanvas = document.getElementById('isisaSteppedLine');
    if (steppedCanvas && typeof Chart !== 'undefined') {
        // PHP: Gantikan data dengan JSON dari backend
        new Chart(steppedCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogos'],
                datasets: [{
                    label: 'Pegawai Bertugas',
                    data: [12, 12, 15, 15, 15, 18, 18, 20],
                    borderColor: '#386641',
                    backgroundColor: '#386641',
                    stepped: true,
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#386641',
                    pointBorderWidth: 2
                },
                {
                    label: 'Sasaran',
                    data: [14, 14, 14, 16, 16, 16, 20, 20],
                    borderColor: '#c9a227',
                    backgroundColor: '#c9a227',
                    stepped: true,
                    borderDash: [6, 4],
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 4
                }]
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
                        ticks: {
                            color: '#5c6b5e',
                            font: chartFont
                        }
                    },
                    y: {
                        beginAtZero: true,
                        border: {
                            display: false
                        },
                        grid: {
                            color: '#edf1ec',
                            drawTicks: false
                        },
                        ticks: {
                            color: '#5c6b5e',
                            padding: 8,
                            font: chartFont
                        }
                    }
                }
            }
        });
    }
}
