/**
 * Bapenda Riau - Dashboard Scripts (Green Theme)
 */
let deleteId = null;

document.addEventListener('DOMContentLoaded', function () {
    initDashboardCharts();
});

function initDashboardCharts() {
    // 1. Line Chart: Tren Penerimaan PAD 6 Bulan Terakhir
    const yieldEl = document.getElementById('yieldChart');
    if (yieldEl) {
        const yieldCtx = yieldEl.getContext('2d');
        new Chart(yieldCtx, {
            type: 'line',
            data: {
                labels: ['Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt'],
                datasets: [{
                    label: 'Realisasi (Miliar Rp)',
                    data: [45.2, 52.8, 48.5, 60.1, 55.4, 68.2],
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22, 163, 74, 0.12)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#16a34a',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                animation: false,
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return ' ' + context.parsed.y + ' Miliar Rp';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            color: '#6b7280',
                            font: { family: 'Poppins', size: 11 },
                            callback: function (val) { return val + ' M'; }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#6b7280',
                            font: { family: 'Poppins', size: 11 }
                        }
                    }
                }
            }
        });
    }

    // 2. Bar Chart: Capaian Target per Objek Pajak
    const growthEl = document.getElementById('growthChart');
    if (growthEl) {
        const growthCtx = growthEl.getContext('2d');
        new Chart(growthCtx, {
            type: 'bar',
            data: {
                labels: ['PKB', 'BBNKB', 'PBBKB', 'PAP', 'P. Rokok'],
                datasets: [{
                    label: 'Persentase Capaian (%)',
                    data: [94.5, 88.2, 91.0, 78.4, 96.8],
                    backgroundColor: [
                        '#16a34a',
                        '#22c55e',
                        '#15803d',
                        '#4ade80',
                        '#166534'
                    ],
                    borderRadius: 6,
                    barThickness: 28
                }]
            },
            options: {
                animation: false,
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return ' Capaian: ' + context.parsed.y + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            color: '#6b7280',
                            font: { family: 'Poppins', size: 11 },
                            callback: function (val) { return val + '%'; }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#6b7280',
                            font: { family: 'Poppins', size: 11 }
                        }
                    }
                }
            }
        });
    }

    // 3. Multi-Bar Chart: Analisis Performa Triwulan
    const seasonalEl = document.getElementById('seasonalChart');
    if (seasonalEl) {
        const seasonalCtx = seasonalEl.getContext('2d');
        new Chart(seasonalCtx, {
            type: 'bar',
            data: {
                labels: ['Triwulan I', 'Triwulan II', 'Triwulan III', 'Triwulan IV (Est)'],
                datasets: [
                    {
                        label: 'Pajak Daerah (Miliar Rp)',
                        data: [120, 145, 160, 175],
                        backgroundColor: '#16a34a',
                        borderRadius: 6,
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    },
                    {
                        label: 'Retribusi Daerah (Miliar Rp)',
                        data: [22, 28, 31, 35],
                        backgroundColor: '#86efac',
                        borderRadius: 6,
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    }
                ]
            },
            options: {
                animation: false,
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { family: 'Poppins', size: 12 },
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            color: '#6b7280',
                            font: { family: 'Poppins', size: 11 },
                            callback: function (val) { return val + ' M'; }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#6b7280',
                            font: { family: 'Poppins', size: 11 }
                        }
                    }
                }
            }
        });
    }
}

// Modal Handlers (Bootstrap 5)
function openAddModal() {
    const modal = new bootstrap.Modal(document.getElementById('addModal'));
    modal.show();
}

function addItem(e) {
    e.preventDefault();
    const modalEl = document.getElementById('addModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data pendapatan baru berhasil ditambahkan.',
            confirmButtonColor: '#16a34a'
        });
    } else {
        alert('Data berhasil ditambahkan');
    }
    e.target.reset();
}

function showDeleteModal(id) {
    deleteId = id;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function confirmDelete() {
    if (deleteId) {
        const item = document.querySelector(`tr[data-item-id="${deleteId}"]`);
        if (item) item.remove();

        const modalEl = document.getElementById('deleteModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: 'Data berhasil dihapus dari sistem.',
                confirmButtonColor: '#16a34a',
                timer: 1800,
                showConfirmButton: false
            });
        }

        deleteId = null;

        const remaining = document.querySelectorAll('tr[data-item-id]');
        const noRes = document.getElementById('noResults');
        if (remaining.length === 0 && noRes) {
            noRes.classList.remove('d-none');
        }
    }
}

function viewItem(id) {
    const modal = new bootstrap.Modal(document.getElementById('viewModal'));
    modal.show();
}

// Filter and Search Logic
function applyFilters() {
    const searchVal = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
    const catVal = document.getElementById('categoryFilter')?.value || 'all';
    const statusVal = document.getElementById('statusFilter')?.value || 'all';

    const rows = document.querySelectorAll('#cropsTableBody tr[data-item-id]');
    let visibleCount = 0;

    rows.forEach(row => {
        const rowSearchable = (row.getAttribute('data-searchable') || '').toLowerCase();
        const rowCategory = row.getAttribute('data-category') || '';
        const rowStatus = row.getAttribute('data-status') || '';

        const matchSearch = !searchVal || rowSearchable.includes(searchVal);
        const matchCategory = (catVal === 'all') || (rowCategory === catVal);
        const matchStatus = (statusVal === 'all') || (rowStatus === statusVal);

        if (matchSearch && matchCategory && matchStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const noRes = document.getElementById('noResults');
    if (noRes) {
        if (visibleCount === 0) {
            noRes.classList.remove('d-none');
        } else {
            noRes.classList.add('d-none');
        }
    }
}

function resetFilters() {
    const searchInput = document.getElementById('searchInput');
    const catFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');

    if (searchInput) searchInput.value = '';
    if (catFilter) catFilter.value = 'all';
    if (statusFilter) statusFilter.value = 'all';

    applyFilters();
}
