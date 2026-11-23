import { chartFont, chartTooltip, bottomLegend } from './chart-defaults.js';

/**
 * Carta radar
 */
export function initRadar() {
    const radarCanvas = document.getElementById('isisaRadar');
    if (radarCanvas && typeof Chart !== 'undefined') {
        // PHP: Gantikan data dengan JSON dari backend
        new Chart(radarCanvas.getContext('2d'), {
            type: 'radar',
            data: {
                labels: ['Kelajuan', 'Ketepatan', 'Pematuhan', 'Kepuasan', 'Kapasiti', 'Responsif'],
                datasets: [{
                    label: '2025',
                    data: [62, 70, 66, 58, 64, 60],
                    borderColor: '#c9a227',
                    backgroundColor: 'rgba(233, 196, 106, 0.25)',
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#c9a227',
                    borderWidth: 2
                },
                {
                    label: '2026',
                    data: [82, 88, 80, 86, 76, 84],
                    borderColor: '#386641',
                    backgroundColor: 'rgba(56, 102, 65, 0.2)',
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#386641',
                    borderWidth: 2
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
                    r: {
                        min: 0,
                        max: 100,
                        ticks: {
                            display: false,
                            stepSize: 25
                        },
                        grid: {
                            color: '#e4e9e2'
                        },
                        angleLines: {
                            color: '#e4e9e2'
                        },
                        pointLabels: {
                            color: '#5c6b5e',
                            font: {
                                ...chartFont,
                                weight: '500'
                            }
                        }
                    }
                }
            }
        });
    }
}
