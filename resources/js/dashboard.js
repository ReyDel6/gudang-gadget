import Chart from 'chart.js/auto';

const dash = window.__dashData || null;
if (!dash || document.readyState !== 'loading') initCharts();
else document.addEventListener('DOMContentLoaded', initCharts);

function initCharts() {
    if (!dash) return;

    const catCanvas = document.getElementById('categoryChart');
    if (catCanvas) {
        new Chart(catCanvas, {
            type: 'doughnut',
            data: {
                labels: dash.categories.map(c => c.kategori),
                datasets: [{
                    data: dash.categories.map(c => c.jumlah),
                    backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444', '#06B6D4', '#EC4899'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#152A45', boxWidth: 12, padding: 14 } },
                },
            },
        });
    }

    const stockCanvas = document.getElementById('stockChart');
    if (stockCanvas) {
        new Chart(stockCanvas, {
            type: 'line',
            data: {
                labels: dash.categories.map(c => c.kategori),
                datasets: [{
                    label: 'Total Stok',
                    data: dash.categories.map(c => c.stok),
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.15)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3B82F6',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    borderWidth: 3,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: { beginAtZero: true, ticks: { color: '#274A70' }, grid: { color: '#EFF3F8' } },
                    x: { ticks: { color: '#274A70' } },
                },
            },
        });
    }
}