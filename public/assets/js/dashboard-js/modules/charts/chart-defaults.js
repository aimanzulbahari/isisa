/**
 * Tetapan lalai bersama untuk semua carta Chart.js (tema korporat hijau MBSA)
 */
export const chartFont = {
    family: "'Inter', sans-serif",
    size: 12
};
export const chartTooltip = {
    backgroundColor: '#1b261d',
    padding: 10,
    cornerRadius: 10
};

export const axisTicks = {
    color: '#5c6b5e',
    font: chartFont
};
export const gridStyle = {
    color: '#edf1ec',
    drawTicks: false
};
export const bottomLegend = {
    position: 'bottom',
    labels: {
        usePointStyle: true,
        pointStyle: 'circle',
        padding: 16,
        color: '#5c6b5e',
        font: {
            ...chartFont,
            weight: '500'
        }
    }
};
