import { chartTooltip, bottomLegend } from './chart-defaults.js';

/**
 * Carta polar area
 */
export function initPolarArea() {
    const polarCanvas = document.getElementById('isisaPolarArea');
    if (polarCanvas && typeof Chart !== 'undefined') {
        // PHP: Gantikan data dengan JSON dari backend
        new Chart(polarCanvas.getContext('2d'), {
            type: 'polarArea',
            data: {
                labels: ['Seksyen 7', 'Seksyen 9', 'Seksyen 13', 'Seksyen 15', 'Seksyen 24', 'Bukit Jelutong'],
                datasets: [{
                    label: 'Aduan',
                    data: [48, 62, 35, 54, 29, 41],
                    backgroundColor: [
                        'rgba(56, 102, 65, 0.78)',
                        'rgba(79, 124, 70, 0.78)',
                        'rgba(106, 153, 78, 0.78)',
                        'rgba(141, 181, 106, 0.78)',
                        'rgba(167, 201, 87, 0.78)',
                        'rgba(233, 196, 106, 0.8)'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
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
                        beginAtZero: true,
                        grid: {
                            color: '#edf1ec'
                        },
                        angleLines: {
                            color: '#edf1ec'
                        },
                        ticks: {
                            display: false
                        }
                    }
                }
            }
        });
    }
}
