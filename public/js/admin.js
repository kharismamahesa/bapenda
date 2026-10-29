/**
 * BAPENDA Admin Dashboard Scripts
 */

// Initialize Lucide Icons
document.addEventListener('DOMContentLoaded', function () {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});

// ============================================
// SIDEBAR LOGIC
// ============================================
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    if (sidebar) {
        sidebar.classList.toggle('-translate-x-full');
    }
    if (overlay) {
        overlay.classList.toggle('hidden');
    }
    document.body.classList.toggle('overflow-hidden');
}

// ============================================
// MODAL LOGIC
// ============================================
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('hidden');
    }
}

// ============================================
// TOAST NOTIFICATION
// ============================================
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastIcon = document.getElementById('toastIcon');
    const toastMessage = document.getElementById('toastMessage');

    if (!toast || !toastIcon || !toastMessage) return;

    toastMessage.textContent = message;

    if (type === 'success') {
        toastIcon.className = 'w-10 h-10 rounded-full flex items-center justify-center bg-success-light';
        toastIcon.innerHTML = '<i data-lucide="check" class="w-5 h-5 text-success-dark"></i>';
    } else {
        toastIcon.className = 'w-10 h-10 rounded-full flex items-center justify-center bg-error-light';
        toastIcon.innerHTML = '<i data-lucide="x" class="w-5 h-5 text-error-dark"></i>';
    }

    toast.classList.remove('hidden');
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    setTimeout(hideToast, 3000);
}

function hideToast() {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.classList.add('hidden');
    }
}

// ============================================
// FILTER LOGIC
// ============================================
function applyFilters() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');

    if (!searchInput) return;

    const searchTerm = searchInput.value.toLowerCase().trim();
    const categoryValue = categoryFilter ? categoryFilter.value : 'all';
    const statusValue = statusFilter ? statusFilter.value : 'all';

    const items = document.querySelectorAll('tr[data-item-id]');
    let visibleCount = 0;

    items.forEach(item => {
        const searchableText = item.dataset.searchable || '';
        const itemCategory = item.dataset.category;
        const itemStatus = item.dataset.status;

        const matchesSearch = searchTerm === '' || searchableText.includes(searchTerm);
        const matchesCategory = categoryValue === 'all' || itemCategory === categoryValue;
        const matchesStatus = statusValue === 'all' || itemStatus === statusValue;

        if (matchesSearch && matchesCategory && matchesStatus) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    const noResults = document.getElementById('noResults');
    if (noResults) {
        noResults.classList.toggle('hidden', visibleCount > 0);
    }
}

function resetFilters() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');

    if (searchInput) searchInput.value = '';
    if (categoryFilter) categoryFilter.value = 'all';
    if (statusFilter) statusFilter.value = 'all';

    applyFilters();
}

