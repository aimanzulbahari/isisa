/**
 * Carta bar bulanan
 */
export function initMonthlyBar() {
    const chartCanvas = document.getElementById('isisaMonthlyChart');
    if (chartCanvas && typeof Chart !== 'undefined') {
        const ctx = chartCanvas.getContext('2d');

        // Gradient hijau lembut untuk bar/kawasan carta
        const primaryGradient = ctx.createLinearGradient(0, 0, 0, 280);
        primaryGradient.addColorStop(0, 'rgba(56, 102, 65, 0.88)');
        primaryGradient.addColorStop(1, 'rgba(106, 153, 78, 0.72)');

        // PHP: Gantikan data labels & datasets di bawah dengan data JSON dari backend
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jun', 'Jul', 'Ogos', 'Sep', 'Okt', 'Nov'],
                datasets: [{
                    label: 'Permohonan Selesai',
                    data: [145, 168, 152, 189, 210, 196],
                    backgroundColor: primaryGradient,
                    borderRadius: 8,
                    barPercentage: 0.55,
                    categoryPercentage: 0.7
                },
                {
                    label: 'Permohonan Diterima',
                    data: [160, 182, 170, 205, 228, 214],
                    backgroundColor: '#dce6d9',
                    borderRadius: 8,
                    barPercentage: 0.55,
                    categoryPercentage: 0.7
                }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 18,
                            color: '#5c6b5e',
                            font: {
                                family: "'Inter', sans-serif",
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1b261d',
                        padding: 10,
                        cornerRadius: 10,
                        titleFont: {
                            family: "'Inter', sans-serif",
                            size: 12
                        },
                        bodyFont: {
                            family: "'Inter', sans-serif",
                            size: 12
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#5c6b5e',
                            font: {
                                family: "'Inter', sans-serif",
                                size: 12
                            }
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
                            font: {
                                family: "'Inter', sans-serif",
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    }
}
