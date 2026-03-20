# Sistem Login Admin - Website Sekolah 🔐

Dokumentasi lengkap sistem login dengan 2 level akses: Super Admin dan Admin.

## 📁 File Sistem Login

```
login-system/
│
├── login.html                  # Halaman login
├── dashboard-superadmin.html   # Dashboard Super Admin
├── dashboard-admin.html        # Dashboard Admin
├── auth.js                     # Sistem autentikasi
├── dashboard.js                # Fungsi dashboard
├── dashboard-style.css         # Style dashboard
└── README-LOGIN.md            # Dokumentasi (file ini)
```

## 🔑 Akun Demo

### Super Admin
- **Username:** `superadmin`
- **Password:** `super123`
- **Akses:** Full access ke semua fitur

### Admin
- **Username:** `admin`
- **Password:** `admin123`
- **Akses:** Limited access (konten, siswa, pengumuman)

## 🎯 Level Akses

### Super Admin
**Fitur yang dapat diakses:**
- ✅ Dashboard Overview dengan semua statistik
- ✅ Kelola User (tambah, edit, hapus admin)
- ✅ Kelola Konten Website
- ✅ Pengaturan Sistem
- ✅ Activity Logs (semua aktivitas)
- ✅ Backup & Restore Database
- ✅ Edit profil sendiri
- ✅ Lihat website publik

**Kemampuan khusus:**
- Menambah/menghapus admin lain
- Akses ke pengaturan keamanan
- Backup dan restore database
- Lihat semua activity logs
- Konfigurasi sistem

### Admin
**Fitur yang dapat diakses:**
- ✅ Dashboard Overview
- ✅ Kelola Konten Website
- ✅ Data Siswa (lihat, tambah, edit)
- ✅ Pengumuman (buat, edit, hapus)
- ✅ Edit profil sendiri
- ✅ Lihat website publik

**Pembatasan:**
- ❌ Tidak bisa mengelola user lain
- ❌ Tidak bisa akses pengaturan sistem
- ❌ Tidak bisa backup/restore database
- ❌ Tidak bisa lihat activity logs lengkap

## Cara Menggunakan

### 1. Login
1. Buka file `login.html`
2. Masukkan username dan password
3. Klik tombol "Login"
4. Sistem akan redirect ke dashboard sesuai role

### 2. Super Admin Dashboard
- URL: `dashboard-superadmin.html`
- Akses penuh ke semua menu
- Panel warna kuning (Super Admin badge)

### 3. Admin Dashboard
- URL: `dashboard-admin.html`
- Akses terbatas sesuai role
- Panel warna biru (Admin badge)

### 4. Logout
- Klik tombol "Logout" di pojok kanan atas
- Session akan dihapus
- Redirect ke halaman login

## 🔒 Sistem Keamanan

### Session Management
```javascript
// Login dengan "Remember Me"
localStorage.setItem('userSession', userData);

// Login tanpa "Remember Me"
sessionStorage.setItem('userSession', userData);
```

### Protected Pages
Setiap halaman dashboard dilindungi dengan:
```javascript
requireRole('super_admin'); // Untuk Super Admin only
requireRole('admin');        // Untuk Admin dan Super Admin
```

### Auto Redirect
- Jika belum login → redirect ke login.html
- Jika sudah login → redirect ke dashboard sesuai role
- Jika akses halaman tanpa permission → redirect ke login

## 📝 Fitur Login Page

### 1. Form Login
- Input username dengan validation
- Input password dengan toggle visibility (👁️)
- Remember me checkbox
- Lupa password link
- Auto-focus dan enter key submit

### 2. Security Features
- Password masking
- Input validation
- Error handling dengan visual feedback
- Rate limiting (untuk production)

### 3. User Experience
- Animated loading
- Success/error alerts
- Auto-redirect setelah login
- Responsive design
- Demo credentials ditampilkan

## 🎨 Fitur Dashboard

### Super Admin Features
1. **Overview**
   - Statistik lengkap sekolah
   - Quick actions
   - Recent activity

2. **Kelola User**
   - Tabel daftar user
   - Tambah user baru
   - Edit/hapus user
   - Role management

3. **Kelola Konten**
   - Edit profil sekolah
   - Edit visi & misi
   - Kelola fasilitas
   - Kelola ekstrakurikuler
   - Update lokasi & kontak

4. **Pengaturan**
   - Konfigurasi website
   - Pengaturan keamanan
   - Two-factor authentication
   - Login notifications

5. **Activity Logs**
   - Riwayat aktivitas semua user
   - Filter by user/date
   - Export logs

6. **Backup & Restore**
   - Buat backup manual
   - Auto backup schedule
   - Download backup
   - Restore dari backup

### Admin Features
1. **Overview**
   - Statistik sekolah
   - Quick actions
   - Activity pribadi

2. **Kelola Konten**
   - Edit konten website
   - Update informasi

3. **Data Siswa**
   - Daftar siswa
   - Tambah siswa baru
   - Edit data siswa
   - Filter by kelas

4. **Pengumuman**
   - Buat pengumuman baru
   - Edit pengumuman
   - Hapus pengumuman

5. **Profil**
   - Edit informasi pribadi
   - Ubah password

## 🔧 Kustomisasi

### Menambah User Baru

Edit file `auth.js`:

```javascript
const users = [
    {
        id: 1,
        username: 'superadmin',
        password: 'super123',
        role: 'super_admin',
        name: 'Super Administrator',
        email: 'superadmin@sekolahkita.sch.id'
    },
    {
        id: 2,
        username: 'admin',
        password: 'admin123',
        role: 'admin',
        name: 'Administrator',
        email: 'admin@sekolahkita.sch.id'
    },
    // Tambah user baru di sini
    {
        id: 3,
        username: 'admin2',
        password: 'admin2123',
        role: 'admin',
        name: 'Administrator 2',
        email: 'admin2@sekolahkita.sch.id'
    }
];
```

### Mengubah Warna Dashboard

Edit file `dashboard-style.css`:

```css
/* Warna badge Super Admin */
.user-role.super-admin {
    background: #fbbf24;  /* Kuning */
    color: #78350f;
}

/* Warna badge Admin */
.user-role.admin {
    background: #60a5fa;  /* Biru */
    color: #1e3a8a;
}
```

### Menambah Menu Dashboard

1. Tambahkan menu item di sidebar:
```html
<a href="#" class="menu-item" onclick="showTab('new-menu')">
    <span class="icon">🆕</span>
    <span>Menu Baru</span>
</a>
```

2. Tambahkan tab content:
```html
<div id="new-menu" class="tab-content">
    <h1>Menu Baru</h1>
    <!-- Konten menu -->
</div>
```

## ⚠️ PENTING untuk Production

### 1. Keamanan Password
**JANGAN** simpan password dalam plaintext seperti demo ini!

Gunakan hashing:
```javascript
// Backend (Node.js example)
const bcrypt = require('bcrypt');
const hashedPassword = await bcrypt.hash(password, 10);

// Verify
const match = await bcrypt.compare(password, hashedPassword);
```

### 2. Backend Integration
Sistem ini menggunakan localStorage untuk demo. Untuk production:

```javascript
// Login API call
async function handleLogin(event) {
    event.preventDefault();
    
    const response = await fetch('/api/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            username: document.getElementById('username').value,
            password: document.getElementById('password').value
        })
    });
    
    const data = await response.json();
    
    if (data.success) {
        localStorage.setItem('token', data.token);
        window.location.href = data.redirectUrl;
    }
}
```

### 3. JWT Token
Gunakan JWT untuk session yang lebih aman:

```javascript
// Backend
const jwt = require('jsonwebtoken');
const token = jwt.sign(
    { userId: user.id, role: user.role },
    process.env.JWT_SECRET,
    { expiresIn: '24h' }
);

// Frontend
const token = localStorage.getItem('token');
fetch('/api/protected', {
    headers: { 'Authorization': `Bearer ${token}` }
});
```

### 4. HTTPS
**WAJIB** menggunakan HTTPS di production!

### 5. Rate Limiting
Implementasi rate limiting untuk mencegah brute force:

```javascript
// Express.js example
const rateLimit = require('express-rate-limit');

const loginLimiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 minutes
    max: 5, // 5 attempts
    message: 'Terlalu banyak percobaan login'
});

app.post('/api/login', loginLimiter, handleLogin);
```

### 6. Input Validation
Selalu validasi input di backend:

```javascript
const { body, validationResult } = require('express-validator');

app.post('/api/login',
    body('username').isLength({ min: 3 }).trim().escape(),
    body('password').isLength({ min: 6 }),
    (req, res) => {
        const errors = validationResult(req);
        if (!errors.isEmpty()) {
            return res.status(400).json({ errors: errors.array() });
        }
        // Process login
    }
);
```

## 🔐 Session Management

### Auto Logout
Tambahkan auto logout setelah inactive:

```javascript
let inactivityTimer;

function resetTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(() => {
        alert('Session expired karena tidak ada aktivitas');
        logout();
    }, 30 * 60 * 1000); // 30 minutes
}

document.addEventListener('mousemove', resetTimer);
document.addEventListener('keypress', resetTimer);
```

### Multiple Device Detection
Track login dari multiple devices:

```javascript
// Simpan device info saat login
const deviceInfo = {
    userAgent: navigator.userAgent,
    loginTime: new Date().toISOString(),
    sessionId: generateSessionId()
};
```

## 📊 Activity Logging

Untuk production, log semua aktivitas penting:

```javascript
function logActivity(action, details) {
    const user = getCurrentUser();
    const log = {
        userId: user.id,
        username: user.username,
        action: action,
        details: details,
        timestamp: new Date().toISOString(),
        ipAddress: getUserIP(),
        userAgent: navigator.userAgent
    };
    
    // Send to backend
    fetch('/api/logs', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(log)
    });
}

// Usage
logActivity('LOGIN', 'User logged in successfully');
logActivity('EDIT_CONTENT', 'Edited Fasilitas page');
logActivity('DELETE_USER', { userId: 5, username: 'oldadmin' });
```

## 🌐 Browser Support

- ✅ Chrome (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Edge (latest)
- ⚠️ IE11 (limited support)

## 📱 Mobile Responsive

Dashboard sudah responsive untuk:
- 📱 Mobile (< 768px)
- 📱 Tablet (768px - 1024px)
- 💻 Desktop (> 1024px)

## 🎓 Demo Flow

1. Buka `login.html`
2. Login sebagai **Super Admin** (superadmin / super123)
3. Explore semua menu
4. Logout
5. Login sebagai **Admin** (admin / admin123)
6. Notice perbedaan akses menu
7. Try accessing Super Admin features (will be blocked)

## 📞 Support

Untuk pertanyaan atau bantuan:
- Email: dev@sekolahkita.sch.id
- Dokumentasi: README-LOGIN.md

---

**⚠️ DISCLAIMER:** Sistem ini adalah DEMO untuk development. Untuk production, implementasikan proper security measures seperti yang dijelaskan di bagian "PENTING untuk Production".

**🔒 Security Checklist untuk Production:**
- [ ] Hash passwords dengan bcrypt/argon2
- [ ] Implementasi JWT untuk session
- [ ] Gunakan HTTPS
- [ ] Rate limiting untuk login attempts
- [ ] Input validation di backend
- [ ] SQL injection protection
- [ ] XSS protection
- [ ] CSRF protection
- [ ] Activity logging
- [ ] Regular security audits

**Happy Coding! 🚀**
