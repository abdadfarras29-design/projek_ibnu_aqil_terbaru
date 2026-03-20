// ===========================
// DASHBOARD FUNCTIONS
// ===========================

/**
 * Show specific tab in dashboard
 */
function showTab(tabId) {
    // Hide all tabs
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => {
        tab.classList.remove('active');
    });

    // Remove active from all menu items
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        item.classList.remove('active');
    });

    // Show selected tab
    const selectedTab = document.getElementById(tabId);
    if (selectedTab) {
        selectedTab.classList.add('active');
    }

    // Set active menu item
    const activeMenuItem = document.querySelector(`[onclick="showTab('${tabId}')"]`);
    if (activeMenuItem) {
        activeMenuItem.classList.add('active');
    }

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // Load berita data when berita tab is opened
    if (tabId === 'berita' && typeof muatBerita === 'function') {
        setTimeout(muatBerita, 50);
    }
}

/**
 * Create backup
 */
async function createBackup() {
    if (await sweetConfirm('Buat Backup?', 'Apakah Anda yakin ingin membuat backup database saat ini?')) {
        await sweetAlert('Memproses...', 'Sistem sedang menyiapkan file backup. Mohon tunggu.', 'info');
        setTimeout(async () => {
            await sweetAlert('Berhasil!', 'Backup database berhasil dibuat!\n\nFile: backup-' + new Date().toISOString().split('T')[0] + '.sql');
        }, 1500);
    }
}

/**
 * Open add user modal
 */
async function openAddUserModal() {
    const username = await sweetPrompt('Username Baru', 'Masukkan username untuk akun baru:');
    if (!username) return;

    const name = await sweetPrompt('Nama Lengkap', 'Masukkan nama lengkap pemilik akun:');
    if (!name) return;

    const email = await sweetPrompt('Email', 'Masukkan alamat email aktif:');
    if (!email) return;

    const role = await sweetConfirm('Jabatan Akun', 'Apakah user ini adalah Super Admin? (Klik "Batal" untuk Admin Biasa)');

    await sweetAlert('User Ditambahkan', `User baru berhasil ditambahkan!\n\nUsername: ${username}\nRole: ${role ? 'Super Admin' : 'Admin'}\n\nPassword default: ${username}123`);
}

/**
 * Create announcement
 */
async function createAnnouncement() {
    const title = await sweetPrompt('Judul Pengumuman', 'Ketik judul pengumuman:');
    if (!title) return;

    const content = await sweetPrompt('Isi Pengumuman', 'Ketik isi pesan pengumuman:');
    if (!content) return;

    await sweetAlert('Dipublikasikan', `Pengumuman "${title}" berhasil dibuat dan akan ditampilkan di website.`);
}

/**
 * Toggle sidebar on mobile
 */
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.style.transform = sidebar.style.transform === 'translateX(0)'
            ? 'translateX(-100%)'
            : 'translateX(0)';
    }
}

/**
 * Initialize dashboard
 */
document.addEventListener('DOMContentLoaded', () => {
    // Load user statistics (in real app, fetch from API)
    loadDashboardStats();

    // Setup auto-refresh for activity logs
    setInterval(refreshActivityLogs, 30000); // Every 30 seconds

    // Buka tab dari URL parameter (misal: ?tab=messages)
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam) {
        showTab(tabParam);
    }
});

/**
 * Load dashboard statistics
 */
function loadDashboardStats() {
    // In real application, this would fetch from API
    console.log('Dashboard stats loaded');
}

/**
 * Refresh activity logs
 */
function refreshActivityLogs() {
    // In real application, this would fetch latest logs from API
    console.log('Activity logs refreshed');
}

/**
 * Export data
 */
async function exportData(type) {
    await sweetAlert('Mengekspor...', `Data ${type} sedang disiapkan. File Excel akan diunduh secara otomatis.`, 'info');
}

/**
 * Print page
 */
function printPage() {
    window.print();
}

/**
 * Search function
 */
function searchData(query) {
    console.log('Searching for:', query);
    // Implement search logic
}

/**
 * Filter data by category
 */
function filterData(category) {
    console.log('Filtering by:', category);
    // Implement filter logic
}

/**
 * Sort data
 */
function sortData(column, order) {
    console.log('Sorting by:', column, order);
    // Implement sort logic
}

// ===========================
// UTILITY FUNCTIONS
// ===========================

/**
 * Format date
 */
function formatDate(date) {
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(date).toLocaleDateString('id-ID', options);
}

/**
 * Format file size
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 90px;
        right: 20px;
        background: ${type === 'success' ? '#d1fae5' : type === 'error' ? '#fee2e2' : '#dbeafe'};
        color: ${type === 'success' ? '#065f46' : type === 'error' ? '#991b1b' : '#1e40af'};
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideInRight 0.3s ease;
    `;

    document.body.appendChild(notification);

    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

/**
 * Confirm action
 */
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// ===========================
// KEYBOARD SHORTCUTS
// ===========================

document.addEventListener('keydown', (e) => {
    // Ctrl/Cmd + K for search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.querySelector('input[type="text"][placeholder*="Cari"]');
        if (searchInput) searchInput.focus();
    }

    // Ctrl/Cmd + S for save (if in edit mode)
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        const saveButton = document.querySelector('.btn-primary');
        if (saveButton && saveButton.textContent.includes('Simpan')) {
            saveButton.click();
        }
    }
});

// ===========================
// CUSTOM PREMIUM ALERTS (SweetAlert Style)
// ===========================
function sweetAlert(title, text, type = 'success') {
    return new Promise(resolve => {
        const overlay = document.createElement('div');
        overlay.className = 'sweet-overlay';
        overlay.innerHTML = `
            <div class="sweet-alert">
                <div class="sweet-icon ${type}">${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}</div>
                <div class="sweet-title">${title}</div>
                <div class="sweet-text">${text}</div>
                <div class="sweet-buttons">
                    <button class="sweet-btn sweet-btn-confirm">OK</button>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
        const btn = overlay.querySelector('.sweet-btn-confirm');
        btn.focus();
        btn.onclick = () => {
            overlay.remove();
            resolve(true);
        };
    });
}

function sweetConfirm(title, text) {
    return new Promise(resolve => {
        const overlay = document.createElement('div');
        overlay.className = 'sweet-overlay';
        overlay.innerHTML = `
            <div class="sweet-alert">
                <div class="sweet-icon warning">❓</div>
                <div class="sweet-title">${title}</div>
                <div class="sweet-text">${text}</div>
                <div class="sweet-buttons">
                    <button class="sweet-btn sweet-btn-cancel">Batal</button>
                    <button class="sweet-btn sweet-btn-confirm">Ya, Hapus!</button>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
        overlay.querySelector('.sweet-btn-cancel').onclick = () => {
            overlay.remove();
            resolve(false);
        };
        overlay.querySelector('.sweet-btn-confirm').onclick = () => {
            overlay.remove();
            resolve(true);
        };
    });
}

// Global helper for link confirmation
async function confirmHapus(event, title, text) {
    event.preventDefault();
    const href = event.currentTarget.getAttribute('href');
    const result = await sweetConfirm(title || 'Konfirmasi Hapus', text || 'Apakah Anda yakin ingin menghapus data ini?');
    if (result) {
        window.location.href = href;
    }
}

function sweetPrompt(title, text, placeholder = '') {
    return new Promise(resolve => {
        const overlay = document.createElement('div');
        overlay.className = 'sweet-overlay';
        overlay.innerHTML = `
            <div class="sweet-alert">
                <div class="sweet-icon info">🖊️</div>
                <div class="sweet-title">${title}</div>
                <div class="sweet-text">${text}</div>
                <input type="text" id="sweetInput" class="form-control" placeholder="${placeholder}" style="margin-bottom:1.5rem; text-align:center;">
                <div class="sweet-buttons">
                    <button class="sweet-btn sweet-btn-cancel">Batal</button>
                    <button class="sweet-btn sweet-btn-confirm">Lanjut</button>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
        const input = overlay.querySelector('#sweetInput');
        input.focus();
        overlay.querySelector('.sweet-btn-cancel').onclick = () => { overlay.remove(); resolve(null); };
        overlay.querySelector('.sweet-btn-confirm').onclick = () => {
            const val = input.value;
            overlay.remove();
            resolve(val);
        };
        input.onkeydown = (e) => { if(e.key === 'Enter') overlay.querySelector('.sweet-btn-confirm').click(); };
    });
}
