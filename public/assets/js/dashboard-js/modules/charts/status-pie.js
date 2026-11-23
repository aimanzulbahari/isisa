/**
 * Carta pai: status permohonan
 */
export function initStatusPie() {
    const pieCanvas = document.getElementById('isisaStatusPie');
    if (pieCanvas && typeof Chart !== 'undefined') {
        // PHP: Gantikan data dengan JSON dari backend
        new Chart(pieCanvas.getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Diluluskan', 'Semakan', 'Perlu Tindakan'],
                datasets: [{
                    data: [964, 142, 48],
                    backgroundColor: ['#386641', '#a7c957', '#e9c46a'],
                    borderColor: '#ffffff',
                    borderWidth: 3
                }]
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
                            padding: 16,
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
                        cornerRadius: 10
                    }
                }
            }
        });
    }
}
