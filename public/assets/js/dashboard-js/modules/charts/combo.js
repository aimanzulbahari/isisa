import { chartFont, chartTooltip, axisTicks, gridStyle, bottomLegend } from './chart-defaults.js';

/**
 * Carta gabungan bar & garisan
 */
export function initCombo() {
    const comboCanvas = document.getElementById('isisaComboChart');
    if (comboCanvas && typeof Chart !== 'undefined') {
        // PHP: Gantikan data dengan JSON dari backend
        new Chart(comboCanvas.getContext('2d'), {
            data: {
                labels: ['Dis', 'Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogos', 'Sep', 'Okt', 'Nov'],
                datasets: [{
                    type: 'bar',
                    label: 'Permohonan Diterima',
                    data: [182, 164, 171, 198, 187, 205, 214, 226, 219, 241, 258, 247],
                    backgroundColor: 'rgba(56, 102, 65, 0.85)',
                    borderRadius: 8,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7,
                    yAxisID: 'y',
                    order: 2
                },
                {
                    type: 'line',
                    label: 'Kadar Kelulusan (%)',
                    data: [78, 74, 76, 81, 79, 83, 85, 84, 88, 86, 90, 89],
                    borderColor: '#d4a63a',
                    backgroundColor: '#ffffff',
                    borderWidth: 2.5,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#d4a63a',
                    pointBorderWidth: 2,
                    yAxisID: 'y1',
                    order: 1
                }
                ]
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
                        position: 'left',
                        beginAtZero: true,
                        border: {
                            display: false
                        },
                        grid: gridStyle,
                        ticks: {
                            ...axisTicks,
                            padding: 8
                        },
                        title: {
                            display: true,
                            text: 'Permohonan',
                            color: '#5c6b5e',
                            font: {
                                ...chartFont,
                                weight: '500'
                            }
                        }
                    },
                    y1: {
                        position: 'right',
                        min: 50,
                        max: 100,
                        border: {
                            display: false
                        },
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            ...axisTicks,
                            padding: 8,
                            callback: v => v + '%'
                        },
                        title: {
                            display: true,
                            text: 'Kelulusan',
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
