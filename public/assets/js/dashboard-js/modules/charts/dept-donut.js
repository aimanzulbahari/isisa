import { chartFont, chartTooltip } from './chart-defaults.js';

/**
 * Carta donut: jabatan
 */
export function initDeptDonut() {
    const donutCanvas = document.getElementById('isisaDeptDonut');
    if (donutCanvas && typeof Chart !== 'undefined') {
        // PHP: Gantikan data dengan JSON dari backend
        new Chart(donutCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Kejuruteraan', 'Perancangan', 'Pelesenan', 'Landskap', 'Khidmat Pengurusan'],
                datasets: [{
                    data: [320, 255, 210, 140, 98],
                    backgroundColor: ['#386641', '#6a994e', '#a7c957', '#d4e0a8', '#e9c46a'],
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 14,
                            color: '#5c6b5e',
                            font: {
                                ...chartFont,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: chartTooltip
                }
            }
        });
    }
}
