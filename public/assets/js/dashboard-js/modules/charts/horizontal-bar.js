import { chartTooltip, axisTicks, gridStyle } from './chart-defaults.js';

/**
 * Carta bar mendatar
 */
export function initHorizontalBar() {
    const hBarCanvas = document.getElementById('isisaHorizontalBar');
    if (hBarCanvas && typeof Chart !== 'undefined') {
        // PHP: Gantikan data dengan JSON dari backend
        new Chart(hBarCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Lesen Premis', 'Kutipan Sisa', 'Aduan Awam', 'Permit Pembinaan', 'Kelestarian'],
                datasets: [{
                    label: 'Permohonan',
                    data: [412, 356, 281, 203, 144],
                    backgroundColor: ['#386641', '#4f7c46', '#6a994e', '#8db56a', '#a7c957'],
                    borderRadius: 8,
                    barThickness: 22
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: chartTooltip
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        border: {
                            display: false
                        },
                        grid: gridStyle,
                        ticks: {
                            ...axisTicks,
                            padding: 8
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        },
                        ticks: axisTicks
                    }
                }
            }
        });
    }
}
