import { chartFont, chartTooltip } from './chart-defaults.js';

/**
 * Carta garisan mingguan
 */
export function initWeeklyLine() {
    const lineCanvas = document.getElementById('isisaWeeklyLine');
    if (lineCanvas && typeof Chart !== 'undefined') {
        const lctx = lineCanvas.getContext('2d');
        const areaGradient = lctx.createLinearGradient(0, 0, 0, 280);
        areaGradient.addColorStop(0, 'rgba(106, 153, 78, 0.28)');
        areaGradient.addColorStop(1, 'rgba(106, 153, 78, 0)');

        // PHP: Gantikan data dengan JSON dari backend
        new Chart(lctx, {
            type: 'line',
            data: {
                labels: ['M1', 'M2', 'M3', 'M4', 'M5', 'M6', 'M7', 'M8'],
                datasets: [{
                    label: 'Permohonan Diterima',
                    data: [48, 62, 55, 71, 66, 84, 78, 92],
                    borderColor: '#386641',
                    backgroundColor: areaGradient,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#386641',
                    pointBorderWidth: 2
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
                    legend: {
                        display: false
                    },
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
