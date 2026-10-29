/**
 * Dashboard Charts & Interactive Handlers
 */
let deleteId = null;

document.addEventListener('DOMContentLoaded', function () {
    initCharts();
});

function initCharts() {
    const yieldEl = document.getElementById('yieldChart');
    if (yieldEl) {
        const yieldCtx = yieldEl.getContext('2d');
        new Chart(yieldCtx, {
            type: 'line',
            data: {
                labels: ['Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep'],
                datasets: [{
                    label: 'Realisasi (Miliar Rp)',
                    data: [45, 52, 48, 60, 55, 68],
                    borderColor: '#22C55E',
                    backgroundColor: 'rgba(34, 197, 94, 0.15)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#22C55E',
                    pointRadius: 4
                }]
            },
            options: {
                animation: false,
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#F3F4F6' },
                        ticks: { color: '#6B7280' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6B7280' }
                    }
                }
            }
        });
    }

    const growthEl = document.getElementById('growthChart');
    if (growthEl) {
        const growthCtx = growthEl.getContext('2d');
        new Chart(growthCtx, {
            type: 'bar',
            data: {
                labels: ['PBB', 'BPHTB', 'Restoran', 'Hotel', 'Parkir'],
                datasets: [{
                    label: 'Pencapaian Target (%)',
                    data: [85, 92, 78, 95, 88],
                    backgroundColor: [
                        '#C5E151',
                        '#82D9D7',
                        '#FAAC7B',
                        '#16A34A',
                        '#22C55E'
                    ],
                    borderRadius: 8,
                    barThickness: 30
                }]
            },
            options: {
                animation: false,
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: '#F3F4F6' },
                        ticks: { callback: function (value) { return value + '%'; } }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    const seasonalEl = document.getElementById('seasonalChart');
    if (seasonalEl) {
        const seasonalCtx = seasonalEl.getContext('2d');
        new Chart(seasonalCtx, {
            type: 'bar',
            data: {
                labels: ['Triwulan I', 'Triwulan II', 'Triwulan III', 'Triwulan IV'],
                datasets: [
                    {
                        label: 'Pajak Daerah',
                        data: [25, 60, 85, 45],
                        backgroundColor: '#FAAC7B',
                        borderRadius: 4
                    },
                    {
                        label: 'Retribusi Daerah',
                        data: [40, 55, 30, 40],
                        backgroundColor: '#82D9D7',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                animation: false,
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#F3F4F6' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
}

// Modal Handlers
function openAddModal() {
    openModal('addModal');
}

function addItem(e) {
    e.preventDefault();
    closeModal('addModal');
    showToast('Data berhasil ditambahkan', 'success');
}

function showDeleteModal(id) {
    deleteId = id;
    openModal('deleteModal');
}

function confirmDelete() {
    if (deleteId) {
        const item = document.querySelector(`tr[data-item-id="${deleteId}"]`);
        if (item) item.remove();
        closeModal('deleteModal');
        showToast('Data berhasil dihapus', 'success');
        deleteId = null;

        const remaining = document.querySelectorAll('tr[data-item-id]');
        if (remaining.length === 0) {
            const noRes = document.getElementById('noResults');
            if (noRes) noRes.classList.remove('hidden');
        }
    }
}

function viewItem(id) {
    openModal('viewModal');
}

