-- Jalankan di database: ibnu_aqil
USE ibnu_aqil;

CREATE TABLE IF NOT EXISTS berita (
    id        INT(11)       NOT NULL AUTO_INCREMENT,
    judul     VARCHAR(200)  NOT NULL,
    foto      VARCHAR(200)  NOT NULL DEFAULT '',
    kategori  ENUM('pengumuman','prestasi','pilihan utama') NOT NULL,
    tanggal   DATE          NOT NULL,
    deskripsi VARCHAR(500)  NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample data
INSERT INTO berita (judul, foto, kategori, tanggal, deskripsi) VALUES
('SMP IBNU AQIL Meresmikan Laboratorium Komputer Generasi Terbaru',
 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?q=80&w=2069&auto=format&fit=crop',
 'pilihan utama', '2026-02-22',
 'Dalam upaya meningkatkan literasi digital siswa, SMP IBNU AQIL resmi membuka fasilitas laboratorium komputer tercanggih yang dilengkapi dengan 40 unit PC terbaru. Laboratorium ini diharapkan menjadi pusat inovasi dan kreativitas bagi seluruh peserta didik.'),

('Siswa SMP IBNU AQIL Juara 1 Olimpiade Matematika Nasional',
 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?q=80&w=2071&auto=format&fit=crop',
 'prestasi', '2026-02-20',
 'Prestasi membanggakan kembali diraih oleh salah satu siswa didik kami, Ahmad Rizki, yang berhasil menyabet medali emas dalam ajang Olimpiade Matematika tingkat Nasional.'),

('Kegiatan Field Trip: Mengenal Ekosistem Hutan Mangrove',
 'https://images.unsplash.com/photo-1523050335456-cbb6e0b20152?q=80&w=2070&auto=format&fit=crop',
 'pengumuman', '2026-02-15',
 'Siswa kelas VIII melakukan kunjungan edukatif ke kawasan konservasi mangrove. Kegiatan ini bertujuan untuk menanamkan kepedulian terhadap lingkungan sejak dini.');
