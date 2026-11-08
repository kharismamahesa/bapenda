/**
 * BAPENDA Admin Dashboard Scripts
 * Mobile Drawer & UI Interactions
 */

// Initialize Lucide Icons if available
document.addEventListener('DOMContentLoaded', function () {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    initMobileSidebar();
});

// ============================================
// SIDEBAR LOGIC (Bapenda Mobile Drawer)
// ============================================
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar) return;

    const isOpen = sidebar.classList.contains('active');
    if (isOpen) {
        closeSidebar();
    } else {
        openSidebar();
    }
}

function openSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) sidebar.classList.add('active');
    if (overlay) overlay.classList.add('active');
    document.body.classList.add('sidebar-open');
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) sidebar.classList.remove('active');
    if (overlay) overlay.classList.remove('active');
    document.body.classList.remove('sidebar-open');
}

function initMobileSidebar() {
    // Close sidebar on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeSidebar();
        }
    });

    // Close sidebar when clicking any navigation link on mobile
    const sidebarLinks = document.querySelectorAll('#sidebar .nav-link');
    sidebarLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            if (link.matches('[data-bs-toggle="collapse"]')) {
                return;
            }

            if (window.innerWidth < 992) {
                closeSidebar();
            }
        });
    });

    // Close sidebar on window resize to desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992) {
            closeSidebar();
        }
    });
}

// ============================================
// MODAL LOGIC (Fallback)
// ============================================
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const bsModal = bootstrap.Modal.getOrCreateInstance(modal);
            bsModal.show();
        } else {
            modal.classList.remove('hidden');
        }
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) bsModal.hide();
        } else {
            modal.classList.add('hidden');
        }
    }
}

// ============================================
// TOAST NOTIFICATION
// ============================================
function showToast(message, type = 'success') {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        return;
    }

    const toast = document.getElementById('toast');
    const toastIcon = document.getElementById('toastIcon');
    const toastMessage = document.getElementById('toastMessage');

    if (!toast || !toastIcon || !toastMessage) return;

    toastMessage.textContent = message;

    if (type === 'success') {
        toastIcon.className = 'w-10 h-10 rounded-full flex items-center justify-center bg-success-light';
        toastIcon.innerHTML = '<i class="bi bi-check fs-5 text-success"></i>';
    } else {
        toastIcon.className = 'w-10 h-10 rounded-full flex items-center justify-center bg-danger-subtle';
        toastIcon.innerHTML = '<i class="bi bi-x fs-5 text-danger"></i>';
    }

    toast.classList.remove('hidden');
    setTimeout(hideToast, 3000);
}

function hideToast() {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.classList.add('hidden');
    }
}
