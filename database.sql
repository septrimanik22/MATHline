-- ============================================
-- DATABASE MATHLine - Sistem PBL Matematika
-- Support Login Guru dengan Nama Saja
-- ============================================

DROP DATABASE IF EXISTS MATHLine;
CREATE DATABASE MATHLine CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE MATHLine;

-- ============================================
-- TABEL UTAMA
-- ============================================

-- Tabel users: menyimpan semua pengguna (admin, guru, siswa)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255), -- NULL untuk guru/admin (login dengan nama saja)
    role ENUM('admin', 'guru', 'siswa') NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    nim_nis VARCHAR(20),
    email VARCHAR(100),
    phone VARCHAR(20),
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_full_name (full_name),
    INDEX idx_email (email),
    INDEX idx_username (username)
) COMMENT = 'Tabel untuk menyimpan data semua pengguna sistem';

-- Tabel students: data khusus siswa
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    class VARCHAR(10) NOT NULL,
    year INT DEFAULT YEAR(CURRENT_DATE),
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_class (class),
    INDEX idx_status (status),
    INDEX idx_user (user_id)
) COMMENT = 'Tabel khusus untuk data siswa';

-- Tabel teachers: data khusus guru
CREATE TABLE teachers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    subject VARCHAR(100) NOT NULL DEFAULT 'Matematika',
    specialization TEXT,
    join_date DATE DEFAULT (CURRENT_DATE),
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_subject (subject),
    INDEX idx_active (is_active),
    INDEX idx_user (user_id)
) COMMENT = 'Tabel khusus untuk data guru';

-- Tabel materials: materi pembelajaran
CREATE TABLE materials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sub_bab VARCHAR(100) NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    video_url VARCHAR(255),
    interactive_url VARCHAR(255),
    file_path VARCHAR(255),
    created_by INT NOT NULL,
    views INT DEFAULT 0,
    is_published BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_sub_bab (sub_bab),
    INDEX idx_created_by (created_by),
    INDEX idx_published (is_published),
    INDEX idx_created_at (created_at DESC)
) COMMENT = 'Tabel untuk materi pembelajaran';

-- Tabel problems: masalah PBL
CREATE TABLE problems (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    phase VARCHAR(20) NOT NULL,
    material_id INT,
    points INT DEFAULT 100,
    due_date DATETIME,
    created_by INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (material_id) REFERENCES materials(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_phase (phase),
    INDEX idx_material (material_id),
    INDEX idx_active (is_active),
    INDEX idx_due_date (due_date),
    INDEX idx_created_by (created_by)
) COMMENT = 'Tabel untuk masalah PBL';

-- Tabel submissions: jawaban siswa
CREATE TABLE submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    problem_id INT NOT NULL,
    answer TEXT NOT NULL,
    file_path VARCHAR(255),
    score INT,
    feedback TEXT,
    status ENUM('submitted', 'graded', 'returned') DEFAULT 'submitted',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    graded_at DATETIME,
    graded_by INT,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (problem_id) REFERENCES problems(id),
    FOREIGN KEY (graded_by) REFERENCES users(id),
    UNIQUE KEY unique_submission (student_id, problem_id),
    INDEX idx_student (student_id),
    INDEX idx_problem (problem_id),
    INDEX idx_status (status),
    INDEX idx_submitted_at (submitted_at DESC),
    INDEX idx_graded_by (graded_by)
) COMMENT = 'Tabel untuk jawaban/submission siswa';

-- Tabel announcements: pengumuman
CREATE TABLE announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_by INT NOT NULL,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME,
    is_published BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_priority (priority),
    INDEX idx_expires (expires_at),
    INDEX idx_published (is_published),
    INDEX idx_created_at (created_at DESC)
) COMMENT = 'Tabel untuk pengumuman';

-- Tabel logs: log aktivitas sistem
CREATE TABLE logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    login_type VARCHAR(20), -- 'password', 'name_only', 'auto'
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_login_type (login_type),
    INDEX idx_created (created_at DESC)
) COMMENT = 'Tabel log aktivitas sistem';

-- ============================================
-- TABEL TAMBAHAN
-- ============================================

-- Tabel student_progress: tracking progress siswa
CREATE TABLE student_progress (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    material_id INT NOT NULL,
    progress_percentage INT DEFAULT 0,
    last_accessed DATETIME,
    completed_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE CASCADE,
    UNIQUE KEY unique_progress (student_id, material_id),
    INDEX idx_student_progress (student_id, material_id),
    INDEX idx_completed (completed_at),
    INDEX idx_progress (progress_percentage)
) COMMENT = 'Tabel untuk tracking progress belajar siswa';

-- Tabel quizzes: kuis/short test
CREATE TABLE quizzes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    material_id INT NOT NULL,
    total_questions INT DEFAULT 5,
    time_limit INT DEFAULT 300,
    passing_score INT DEFAULT 60,
    created_by INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (material_id) REFERENCES materials(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_material (material_id),
    INDEX idx_active (is_active),
    INDEX idx_created_by (created_by)
) COMMENT = 'Tabel untuk quiz/short test';

-- Tabel quiz_questions: pertanyaan kuis
CREATE TABLE quiz_questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quiz_id INT NOT NULL,
    question TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255),
    option_d VARCHAR(255),
    correct_answer CHAR(1) NOT NULL,
    points INT DEFAULT 1,
    explanation TEXT,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    INDEX idx_quiz (quiz_id)
) COMMENT = 'Tabel untuk pertanyaan quiz';

-- Tabel quiz_results: hasil kuis siswa
CREATE TABLE quiz_results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT NOT NULL,
    correct_answers INT NOT NULL,
    total_questions INT NOT NULL,
    time_taken INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id),
    UNIQUE KEY unique_quiz_attempt (student_id, quiz_id),
    INDEX idx_student_quiz (student_id, quiz_id),
    INDEX idx_completed_at (completed_at DESC)
) COMMENT = 'Tabel untuk hasil quiz siswa';

-- Tabel discussions: forum diskusi
CREATE TABLE discussions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    problem_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    parent_id INT DEFAULT NULL,
    is_edited BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (parent_id) REFERENCES discussions(id) ON DELETE CASCADE,
    INDEX idx_problem (problem_id),
    INDEX idx_user (user_id),
    INDEX idx_parent (parent_id),
    INDEX idx_created (created_at DESC)
) COMMENT = 'Tabel untuk forum diskusi';

-- Tabel notifications: notifikasi sistem
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('assignment', 'announcement', 'grade', 'reminder', 'system') NOT NULL,
    reference_id INT,
    reference_type VARCHAR(50),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, is_read),
    INDEX idx_type (type),
    INDEX idx_created (created_at DESC),
    INDEX idx_reference (reference_type, reference_id)
) COMMENT = 'Tabel untuk notifikasi sistem';

-- Tabel attachments: lampiran file
CREATE TABLE attachments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(100),
    file_size INT,
    uploaded_by INT NOT NULL,
    related_to VARCHAR(50),
    related_id INT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id),
    INDEX idx_related (related_to, related_id),
    INDEX idx_uploader (uploaded_by),
    INDEX idx_file_type (file_type)
) COMMENT = 'Tabel untuk lampiran file';

-- Tabel achievements: badge/penghargaan
CREATE TABLE achievements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    badge_name VARCHAR(100) NOT NULL,
    badge_type ENUM('completion', 'excellence', 'participation', 'speed', 'consistency') NOT NULL,
    badge_image VARCHAR(255),
    description TEXT,
    awarded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_badge (student_id, badge_name),
    INDEX idx_student_badges (student_id),
    INDEX idx_badge_type (badge_type)
) COMMENT = 'Tabel untuk badge/penghargaan siswa';

-- Tabel system_settings: pengaturan sistem
CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
    category VARCHAR(50) DEFAULT 'general',
    description VARCHAR(255),
    is_public BOOLEAN DEFAULT FALSE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,
    FOREIGN KEY (updated_by) REFERENCES users(id),
    INDEX idx_key (setting_key),
    INDEX idx_category (category),
    INDEX idx_public (is_public)
) COMMENT = 'Tabel untuk pengaturan sistem';

-- Tabel login_activities: tracking aktivitas login
CREATE TABLE login_activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    login_method ENUM('password', 'name_only', 'auto') NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    success BOOLEAN DEFAULT TRUE,
    failure_reason VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_login (user_id, created_at DESC),
    INDEX idx_method (login_method),
    INDEX idx_success (success),
    INDEX idx_created_at (created_at DESC)
) COMMENT = 'Tabel khusus untuk aktivitas login';

-- ============================================
-- DATA AWAL
-- ============================================

-- 1. Insert Admin/Guru: Septriana Manik (login dengan nama saja)
INSERT INTO users (username, password, role, full_name, nim_nis, email) 
VALUES (
    'septriana.manik',
    NULL, -- Password NULL karena login dengan nama saja
    'admin',
    'Septriana Manik',
    '4222111005',
    'septriana.manik@mathline.sch.id'
);

-- Simpan ID Septriana untuk referensi
SET @septriana_id = LAST_INSERT_ID();

-- Insert data Septriana ke tabel teachers
INSERT INTO teachers (user_id, subject, specialization, join_date) 
VALUES (
    @septriana_id,
    'Matematika',
    'Aljabar, Geometri, PBL',
    '2024-01-01'
);

-- 2. Insert Guru Lain: Budi Santoso
INSERT INTO users (username, password, role, full_name, email) 
VALUES (
    'budi.guru',
    NULL, -- Login dengan nama saja
    'guru',
    'Budi Santoso',
    'budi.santoso@mathline.sch.id'
);

SET @budi_guru_id = LAST_INSERT_ID();

INSERT INTO teachers (user_id, subject, specialization, join_date) 
VALUES (
    @budi_guru_id,
    'Matematika',
    'Kalkulus, Statistika',
    '2024-01-01'
);

-- 3. Insert Guru Lain: Siti Nurhaliza
INSERT INTO users (username, password, role, full_name, email) 
VALUES (
    'siti.guru',
    NULL, -- Login dengan nama saja
    'guru',
    'Siti Nurhaliza',
    'siti.nurhaliza@mathline.sch.id'
);

SET @siti_guru_id = LAST_INSERT_ID();

INSERT INTO teachers (user_id, subject, specialization, join_date) 
VALUES (
    @siti_guru_id,
    'IPA',
    'Fisika, Kimia',
    '2024-01-01'
);

-- 4. Insert Siswa: Ahmad Budi Santoso
INSERT INTO users (username, password, role, full_name, nim_nis) 
VALUES (
    'ahmad.budi',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'siswa',
    'Ahmad Budi Santoso',
    'SIS001'
);

SET @ahmad_id = LAST_INSERT_ID();

-- 5. Insert Siswa: Siti Nurhaliza (siswa)
INSERT INTO users (username, password, role, full_name, nim_nis) 
VALUES (
    'siti.siswa',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'siswa',
    'Siti Nurhaliza',
    'SIS002'
);

SET @siti_siswa_id = LAST_INSERT_ID();

-- 6. Insert Siswa: Budi Santoso (siswa)
INSERT INTO users (username, password, role, full_name, nim_nis) 
VALUES (
    'budi.siswa',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'siswa',
    'Budi Santoso',
    'SIS003'
);

SET @budi_siswa_id = LAST_INSERT_ID();

-- 7. Insert Siswa: Dewi Lestari
INSERT INTO users (username, password, role, full_name, nim_nis) 
VALUES (
    'dewi.lestari',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'siswa',
    'Dewi Lestari',
    'SIS004'
);

SET @dewi_id = LAST_INSERT_ID();

-- 8. Insert Siswa: Fajar Ramadhan
INSERT INTO users (username, password, role, full_name, nim_nis) 
VALUES (
    'fajar.ramadhan',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'siswa',
    'Fajar Ramadhan',
    'SIS005'
);

SET @fajar_id = LAST_INSERT_ID();

-- 9. Insert data ke tabel students
INSERT INTO students (user_id, class, year, status) VALUES
(@ahmad_id, 'VIII', 2024, 'active'),
(@siti_siswa_id, 'VIII', 2024, 'active'),
(@budi_siswa_id, 'VIII', 2024, 'active'),
(@dewi_id, 'VIII', 2024, 'active'),
(@fajar_id, 'VIII', 2024, 'active');

-- ============================================
-- DATA MATERI DAN MASALAH PBL
-- ============================================

-- Materi pembelajaran (dibuat oleh Septriana)
INSERT INTO materials (sub_bab, title, content, created_by, is_published) VALUES
('Konsep Persamaan Garis Lurus', 'Pengertian Persamaan Garis Lurus', 'Materi tentang konsep dasar persamaan garis lurus...', @septriana_id, TRUE),
('Konsep Persamaan Garis Lurus', 'Bentuk Umum dan Eksplisit', 'Penjelasan tentang bentuk ax+by+c=0 dan y=mx+c...', @septriana_id, TRUE),
('Kemiringan (Gradien) Garis Lurus', 'Pengertian Gradien', 'Materi tentang konsep gradien sebagai kemiringan...', @septriana_id, TRUE),
('Kemiringan (Gradien) Garis Lurus', 'Menghitung Gradien', 'Cara menghitung gradien dari dua titik...', @septriana_id, TRUE),
('Garis Sejajar dan Tegak Lurus', 'Hubungan Gradien Garis Sejajar', 'Garis sejajar memiliki gradien yang sama...', @septriana_id, TRUE),
('Garis Sejajar dan Tegak Lurus', 'Hubungan Gradien Garis Tegak Lurus', 'Hasil kali gradien garis tegak lurus = -1...', @septriana_id, TRUE);

-- Masalah PBL
INSERT INTO problems (title, description, phase, material_id, created_by, is_active, due_date) VALUES
('Masalah 1: Jalan Desa Petani', 'Seorang petani ingin membuat pagar lurus dari titik A(2,3) ke titik B(6,7). Bagaimana cara menyatakan garis pagar tersebut dalam bentuk matematika yang umum?', 'Fase 1', 1, @septriana_id, TRUE, DATE_ADD(NOW(), INTERVAL 7 DAY)),
('Investigasi Pola Koordinat', 'Identifikasi hubungan antara perubahan koordinat x dan y pada garis lurus. Gunakan contoh-contoh untuk menemukan pola.', 'Fase 2', 1, @septriana_id, TRUE, DATE_ADD(NOW(), INTERVAL 10 DAY)),
('Buat Poster Konsep', 'Buat poster yang menjelaskan konsep persamaan garis lurus dengan bahasa Anda sendiri dan contoh penerapan.', 'Fase 3', 2, @septriana_id, TRUE, DATE_ADD(NOW(), INTERVAL 14 DAY)),
('Masalah 2: Tangga Darurat', 'Sebuah tangga darurat dipasang dengan ujung bawah di (1,1) dan ujung atas di (5,9). Apakah tangga ini terlalu curam? (Standar keselamatan: kemiringan ≤ 1.5)', 'Fase 1', 3, @septriana_id, TRUE, DATE_ADD(NOW(), INTERVAL 7 DAY)),
('Simulasi Gradien', 'Gunakan simulasi di website untuk memahami bagaimana perubahan koordinat mempengaruhi kemiringan garis.', 'Fase 2', 3, @septriana_id, TRUE, DATE_ADD(NOW(), INTERVAL 10 DAY)),
('Laporan Keselamatan Tangga', 'Buat laporan keselamatan tangga berdasarkan perhitungan gradien. Berikan rekomendasi jika tidak aman.', 'Fase 4', 4, @septriana_id, TRUE, DATE_ADD(NOW(), INTERVAL 14 DAY)),
('Masalah 3: Desain Taman Sekolah', 'Kalian ditugaskan mendesain jalur paving di taman. Syarat: jalur utama sejajar dengan jalan setapak yang sudah ada (gradien = ⅔), dan jalur penghubung harus tegak lurus dengan jalur utama.', 'Fase 1', 5, @septriana_id, TRUE, DATE_ADD(NOW(), INTERVAL 7 DAY)),
('Eksplorasi Hubungan Gradien', 'Gunakan tools digital untuk memanipulasi garis dan mengamati hubungan gradien garis sejajar dan tegak lurus.', 'Fase 3', 5, @septriana_id, TRUE, DATE_ADD(NOW(), INTERVAL 12 DAY)),
('Blueprint Taman Sekolah', 'Buat blueprint taman sekolah dengan memanfaatkan konsep garis sejajar dan tegak lurus.', 'Fase 4', 6, @septriana_id, TRUE, DATE_ADD(NOW(), INTERVAL 14 DAY));

-- ============================================
-- DATA LAINNYA
-- ============================================

-- Pengumuman
INSERT INTO announcements (title, content, created_by, priority, is_published) VALUES
('Selamat Datang di Sistem PBL', 'Selamat belajar menggunakan sistem pembelajaran Problem Based Learning. Jangan ragu untuk bertanya jika mengalami kesulitan.', @septriana_id, 'high', TRUE),
('Jadwal Pembelajaran', 'Pertemuan 1: Konsep Dasar, Pertemuan 2: Gradien, Pertemuan 3: Garis Sejajar & Tegak Lurus', @septriana_id, 'medium', TRUE),
('Pengumpulan Tugas', 'Tugas fase 1 harus dikumpulkan paling lambat Jumat depan. Perhatikan deadline masing-masing fase.', @septriana_id, 'high', TRUE);

-- Contoh submission siswa
INSERT INTO submissions (student_id, problem_id, answer, score, status, graded_by) VALUES
(@ahmad_id, 1, 'Saya telah menganalisis masalah ini dan mendapatkan persamaan garis...', 85, 'graded', @septriana_id),
(@siti_siswa_id, 1, 'Persamaan garis dari titik A ke B adalah...', 90, 'graded', @septriana_id),
(@ahmad_id, 4, 'Tangga ini memiliki gradien 2, melebihi standar keselamatan 1.5. Saran: Pindahkan titik atas menjadi (5,7.5) untuk mendapatkan gradien 1.625 yang lebih aman.', 92, 'graded', @septriana_id),
(@budi_siswa_id, 7, 'Untuk jalur utama sejajar: gradien tetap ⅔. Untuk jalur penghubung tegak lurus: gradien = -3/2. Saya telah membuat sketsa desain...', 88, 'graded', @septriana_id);

-- Data system_settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, category, description, is_public) VALUES
('system_name', 'MATHLine - PBL Learning System', 'string', 'general', 'Nama sistem', TRUE),
('admin_name', 'Septriana Manik', 'string', 'general', 'Nama administrator', TRUE),
('teacher_login_method', 'name_only', 'string', 'security', 'Metode login guru', FALSE),
('student_login_method', 'name_only', 'string', 'security', 'Metode login siswa', TRUE),
('max_file_size', '10485760', 'integer', 'upload', 'Ukuran maksimal file (bytes)', TRUE),
('allowed_file_types', 'pdf,doc,docx,ppt,pptx,jpg,jpeg,png', 'string', 'upload', 'Tipe file yang diizinkan', TRUE),
('default_deadline_days', '7', 'integer', 'assignment', 'Default deadline tugas (hari)', FALSE),
('min_password_length', '8', 'integer', 'security', 'Panjang minimal password', FALSE),
('session_timeout', '1800', 'integer', 'security', 'Waktu timeout session (detik)', FALSE),
('maintenance_mode', 'false', 'boolean', 'system', 'Mode maintenance', TRUE);

-- Data quizzes
INSERT INTO quizzes (title, description, material_id, created_by, is_active) VALUES
('Quiz Konsep Dasar', 'Quiz tentang pengertian dasar persamaan garis lurus', 1, @septriana_id, TRUE),
('Quiz Gradien', 'Quiz tentang konsep dan perhitungan gradien', 3, @septriana_id, TRUE);

-- Data quiz_questions
INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, explanation) VALUES
(1, 'Persamaan garis lurus umumnya ditulis dalam bentuk?', 'ax + by = c', 'ax + by + c = 0', 'y = mx + b', 'Kedua bentuk benar', 'd', 'Kedua bentuk adalah bentuk umum dan eksplisit dari persamaan garis lurus'),
(1, 'Manakah yang merupakan persamaan garis lurus?', 'y = 2x + 3', 'y = x² + 1', 'x² + y² = 25', 'y = 1/x', 'a', 'Hanya persamaan dengan variabel berpangkat 1 yang merupakan garis lurus'),
(1, 'Gradien garis y = 3x + 5 adalah?', '3', '5', '3/5', '5/3', 'a', 'Dalam bentuk y = mx + c, m adalah gradien'),
(2, 'Gradien garis yang melalui titik (2,3) dan (4,7) adalah?', '1', '2', '3', '4', 'b', 'Gradien = (y2-y1)/(x2-x1) = (7-3)/(4-2) = 4/2 = 2'),
(2, 'Garis dengan persamaan y = -2x + 4 memiliki gradien?', '2', '-2', '4', '-4', 'b', 'Koefisien x adalah gradien, dalam hal ini -2'),
(2, 'Jika gradien garis A adalah 3/4, maka gradien garis yang sejajar adalah?', '-4/3', '-3/4', '4/3', '3/4', 'd', 'Garis sejajar memiliki gradien yang sama');

-- Data student_progress
INSERT INTO student_progress (student_id, material_id, progress_percentage, last_accessed) VALUES
(@ahmad_id, 1, 100, NOW()),
(@ahmad_id, 2, 80, NOW()),
(@ahmad_id, 3, 100, NOW()),
(@siti_siswa_id, 1, 100, NOW()),
(@siti_siswa_id, 2, 100, NOW()),
(@siti_siswa_id, 3, 60, NOW()),
(@budi_siswa_id, 1, 100, NOW()),
(@budi_siswa_id, 2, 40, NOW()),
(@budi_siswa_id, 3, 0, NOW());

-- Data achievements
INSERT INTO achievements (student_id, badge_name, badge_type, description) VALUES
(@ahmad_id, 'Problem Solver Level 1', 'excellence', 'Menyelesaikan 3 masalah dengan nilai di atas 85'),
(@ahmad_id, 'Fast Learner', 'speed', 'Menyelesaikan materi dalam waktu cepat'),
(@siti_siswa_id, 'Quiz Master', 'excellence', 'Mendapatkan nilai sempurna pada quiz'),
(@budi_siswa_id, 'Active Participant', 'participation', 'Aktif dalam diskusi dan tugas');

-- Data discussions
INSERT INTO discussions (problem_id, user_id, message) VALUES
(1, @ahmad_id, 'Saya bingung menentukan bentuk umum persamaan garis dari dua titik'),
(1, @siti_siswa_id, 'Coba gunakan rumus (y-y1)/(y2-y1) = (x-x1)/(x2-x1)'),
(1, @septriana_id, 'Bagus Budi! Itu adalah formula dua titik. Setelah itu, ubah ke bentuk ax+by+c=0'),
(4, @budi_siswa_id, 'Apakah gradien 2 terlalu berbahaya untuk tangga darurat?'),
(4, @septriana_id, 'Ya, standar keselamatan maksimal gradien 1.5. Gradien 2 berarti terlalu curam.');

-- Data notifications
INSERT INTO notifications (user_id, title, message, type) VALUES
(@ahmad_id, 'Tugas baru tersedia', 'Masalah PBL fase 1 telah tersedia. Deadline: 2 hari lagi', 'assignment'),
(@siti_siswa_id, 'Pengumuman penting', 'Jadwal pembelajaran minggu depan telah diupdate', 'announcement'),
(@budi_siswa_id, 'Reminder deadline', 'Tugas fase 2 akan segera berakhir. Segera kumpulkan!', 'reminder');

-- ============================================
-- VIEWS UNTUK REPORTING
-- ============================================

-- View untuk melihat progress siswa
CREATE VIEW student_progress_view AS
SELECT 
    u.id as student_id,
    u.full_name,
    s.class,
    COUNT(DISTINCT sp.material_id) as materials_completed,
    COUNT(DISTINCT m.id) as total_materials,
    ROUND(COUNT(DISTINCT sp.material_id) * 100.0 / COUNT(DISTINCT m.id), 1) as completion_rate,
    COUNT(DISTINCT sub.problem_id) as problems_submitted,
    COUNT(DISTINCT CASE WHEN sub.status = 'graded' THEN sub.problem_id END) as problems_graded,
    COALESCE(AVG(sub.score), 0) as average_score,
    MAX(u.last_login) as last_login
FROM users u
JOIN students s ON u.id = s.user_id
CROSS JOIN materials m
LEFT JOIN student_progress sp ON u.id = sp.student_id AND m.id = sp.material_id AND sp.progress_percentage = 100
LEFT JOIN submissions sub ON u.id = sub.student_id
WHERE u.role = 'siswa' AND m.is_published = TRUE
GROUP BY u.id, u.full_name, s.class;

-- View untuk melihat daftar guru/admin
CREATE VIEW teacher_admin_view AS
SELECT 
    u.id,
    u.username,
    u.role,
    u.full_name,
    u.email,
    u.last_login,
    t.subject,
    t.specialization,
    t.join_date,
    t.is_active,
    COUNT(DISTINCT m.id) as materials_created,
    COUNT(DISTINCT p.id) as problems_created,
    COUNT(DISTINCT a.id) as announcements_created
FROM users u
LEFT JOIN teachers t ON u.id = t.user_id
LEFT JOIN materials m ON u.id = m.created_by
LEFT JOIN problems p ON u.id = p.created_by
LEFT JOIN announcements a ON u.id = a.created_by
WHERE u.role IN ('admin', 'guru')
GROUP BY u.id, u.username, u.role, u.full_name;

-- View untuk leaderboard siswa
CREATE VIEW student_leaderboard AS
SELECT 
    ROW_NUMBER() OVER (ORDER BY COALESCE(SUM(sub.score), 0) DESC, u.full_name) as rank,
    u.id as student_id,
    u.full_name,
    s.class,
    COUNT(DISTINCT sub.problem_id) as problems_solved,
    COALESCE(SUM(sub.score), 0) as total_score,
    COALESCE(AVG(sub.score), 0) as average_score,
    COUNT(DISTINCT sp.material_id) as materials_completed,
    COUNT(DISTINCT a.id) as badges_count,
    MAX(sub.submitted_at) as last_activity
FROM users u
JOIN students s ON u.id = s.user_id
LEFT JOIN submissions sub ON u.id = sub.student_id AND sub.status = 'graded'
LEFT JOIN student_progress sp ON u.id = sp.student_id AND sp.progress_percentage = 100
LEFT JOIN achievements a ON u.id = a.student_id
WHERE u.role = 'siswa'
GROUP BY u.id, u.full_name, s.class
ORDER BY total_score DESC, average_score DESC, problems_solved DESC;

-- View untuk aktivitas login
CREATE VIEW login_activity_view AS
SELECT 
    la.id,
    u.full_name,
    u.role,
    la.login_method,
    la.ip_address,
    la.success,
    la.failure_reason,
    la.created_at
FROM login_activities la
JOIN users u ON la.user_id = u.id
ORDER BY la.created_at DESC;

-- ============================================
-- STORED PROCEDURES
-- ============================================

DELIMITER //

-- Procedure untuk login guru dengan nama saja
CREATE PROCEDURE TeacherLoginByName(
    IN p_full_name VARCHAR(100),
    OUT p_user_id INT,
    OUT p_username VARCHAR(50),
    OUT p_role VARCHAR(20),
    OUT p_success BOOLEAN,
    OUT p_message VARCHAR(200)
)
BEGIN
    DECLARE user_count INT;
    DECLARE v_user_id INT;
    DECLARE v_username VARCHAR(50);
    DECLARE v_role VARCHAR(20);
    
    -- Cek apakah user ada
    SELECT COUNT(*), id, username, role
    INTO user_count, v_user_id, v_username, v_role
    FROM users 
    WHERE full_name = p_full_name 
    AND role IN ('admin', 'guru');
    
    IF user_count > 0 THEN
        -- Update last login
        UPDATE users SET last_login = NOW() WHERE id = v_user_id;
        
        -- Log aktivitas
        INSERT INTO login_activities (user_id, login_method, success)
        VALUES (v_user_id, 'name_only', TRUE);
        
        INSERT INTO logs (user_id, action, login_type, description)
        VALUES (v_user_id, 'login', 'name_only', CONCAT('Login berhasil: ', p_full_name));
        
        SET p_user_id = v_user_id;
        SET p_username = v_username;
        SET p_role = v_role;
        SET p_success = TRUE;
        SET p_message = 'Login berhasil';
    ELSE
        -- Log attempt failed
        INSERT INTO login_activities (user_id, login_method, success, failure_reason)
        VALUES (NULL, 'name_only', FALSE, CONCAT('Nama tidak ditemukan: ', p_full_name));
        
        INSERT INTO logs (user_id, action, login_type, description)
        VALUES (NULL, 'login_failed', 'name_only', CONCAT('Login gagal: Nama ', p_full_name, ' tidak terdaftar'));
        
        SET p_user_id = NULL;
        SET p_username = NULL;
        SET p_role = NULL;
        SET p_success = FALSE;
        SET p_message = CONCAT('Nama "', p_full_name, '" tidak terdaftar sebagai guru/admin');
    END IF;
END //

-- Procedure untuk update progress siswa
CREATE PROCEDURE UpdateStudentProgress(
    IN p_student_id INT,
    IN p_material_id INT,
    IN p_progress_percentage INT
)
BEGIN
    DECLARE existing_count INT;
    DECLARE old_progress INT;
    
    SELECT COUNT(*), COALESCE(progress_percentage, 0)
    INTO existing_count, old_progress
    FROM student_progress 
    WHERE student_id = p_student_id AND material_id = p_material_id;
    
    IF existing_count > 0 THEN
        UPDATE student_progress 
        SET progress_percentage = GREATEST(old_progress, p_progress_percentage),
            last_accessed = NOW(),
            updated_at = NOW(),
            completed_at = CASE 
                WHEN p_progress_percentage = 100 AND old_progress < 100 THEN NOW()
                ELSE completed_at 
            END
        WHERE student_id = p_student_id AND material_id = p_material_id;
        
        -- Buat notifikasi jika selesai 100%
        IF p_progress_percentage = 100 AND old_progress < 100 THEN
            INSERT INTO notifications (user_id, title, message, type, reference_id, reference_type)
            SELECT 
                p_student_id,
                'Materi Selesai',
                CONCAT('Anda telah menyelesaikan materi: ', (SELECT title FROM materials WHERE id = p_material_id)),
                'system',
                p_material_id,
                'material'
            FROM dual;
        END IF;
    ELSE
        INSERT INTO student_progress (student_id, material_id, progress_percentage, last_accessed, completed_at)
        VALUES (p_student_id, p_material_id, p_progress_percentage, NOW(),
                CASE WHEN p_progress_percentage = 100 THEN NOW() ELSE NULL END);
    END IF;
    
    -- Update view count material
    UPDATE materials SET views = views + 1 WHERE id = p_material_id;
END //

-- Procedure untuk menambahkan guru baru
CREATE PROCEDURE AddTeacher(
    IN p_full_name VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_subject VARCHAR(100),
    IN p_specialization TEXT,
    IN p_added_by INT
)
BEGIN
    DECLARE v_username VARCHAR(50);
    DECLARE v_user_id INT;
    
    -- Generate username dari nama
    SET v_username = LOWER(REPLACE(REPLACE(p_full_name, ' ', '.'), "'", ""));
    
    -- Cek jika username sudah ada
    WHILE EXISTS (SELECT 1 FROM users WHERE username = v_username) DO
        SET v_username = CONCAT(v_username, FLOOR(RAND() * 1000));
    END WHILE;
    
    -- Insert user
    INSERT INTO users (username, password, role, full_name, email, created_at)
    VALUES (v_username, NULL, 'guru', p_full_name, p_email, NOW());
    
    SET v_user_id = LAST_INSERT_ID();
    
    -- Insert ke tabel teachers
    INSERT INTO teachers (user_id, subject, specialization, join_date)
    VALUES (v_user_id, p_subject, p_specialization, CURDATE());
    
    -- Log activity
    INSERT INTO logs (user_id, action, description)
    VALUES (p_added_by, 'add_teacher', CONCAT('Menambahkan guru baru: ', p_full_name));
    
    SELECT v_user_id as new_teacher_id, v_username as generated_username;
END //

DELIMITER ;

-- ============================================
-- TRIGGERS
-- ============================================

DELIMITER //

-- Trigger untuk auto-create notification saat tugas baru dikumpulkan
CREATE TRIGGER after_submission_insert
AFTER INSERT ON submissions
FOR EACH ROW
BEGIN
    -- Notifikasi untuk guru bahwa ada submission baru
    INSERT INTO notifications (user_id, title, message, type, reference_id, reference_type)
    SELECT 
        p.created_by,
        CONCAT('Tugas baru dari ', u.full_name),
        CONCAT('Siswa ', u.full_name, ' telah mengumpulkan tugas: ', p.title),
        'assignment',
        NEW.id,
        'submission'
    FROM users u
    JOIN problems p ON NEW.problem_id = p.id
    WHERE u.id = NEW.student_id;
END //

-- Trigger untuk auto-create notification saat nilai diberikan
CREATE TRIGGER after_submission_graded
AFTER UPDATE ON submissions
FOR EACH ROW
BEGIN
    IF OLD.status != 'graded' AND NEW.status = 'graded' THEN
        -- Notifikasi untuk siswa bahwa tugas telah dinilai
        INSERT INTO notifications (user_id, title, message, type, reference_id, reference_type)
        VALUES (
            NEW.student_id,
            'Tugas Anda telah dinilai',
            CONCAT('Tugas "', (SELECT title FROM problems WHERE id = NEW.problem_id), 
                   '" telah dinilai. Nilai: ', NEW.score),
            'grade',
            NEW.id,
            'submission'
        );
        
        -- Beri achievement jika nilai sempurna
        IF NEW.score >= 95 THEN
            INSERT INTO achievements (student_id, badge_name, badge_type, description)
            VALUES (NEW.student_id, 'Perfect Score', 'excellence', 
                   CONCAT('Mendapatkan nilai sempurna pada tugas: ', 
                         (SELECT title FROM problems WHERE id = NEW.problem_id)))
            ON DUPLICATE KEY UPDATE awarded_at = NOW();
        END IF;
    END IF;
END //

DELIMITER ;

-- ============================================
-- INDEX TAMBAHAN UNTUK PERFORMANCE
-- ============================================

CREATE INDEX idx_users_role_name ON users(role, full_name);
CREATE INDEX idx_teachers_active ON teachers(is_active, user_id);
CREATE INDEX idx_materials_published ON materials(is_published, created_at DESC);
CREATE INDEX idx_problems_active_due ON problems(is_active, due_date, created_by);
CREATE INDEX idx_submissions_student_status ON submissions(student_id, status, submitted_at DESC);
CREATE INDEX idx_submissions_problem_status ON submissions(problem_id, status, score DESC);
CREATE INDEX idx_announcements_active ON announcements(is_published, expires_at, priority);
CREATE INDEX idx_logs_user_date ON logs(user_id, created_at DESC);
CREATE INDEX idx_notifications_unread ON notifications(user_id, is_read, created_at DESC);
CREATE INDEX idx_login_activities_user ON login_activities(user_id, created_at DESC);

-- ============================================
-- VERIFIKASI DATABASE
-- ============================================

SELECT '=== VERIFIKASI DATABASE MATHLine ===' as '';
SELECT '=== Created by: Septriana Manik ===' as '';
SELECT '' as '';

SELECT '1. DAFTAR GURU/ADMIN:' as '';
SELECT id, username, role, full_name, email FROM users WHERE role IN ('admin', 'guru');

SELECT '' as '';
SELECT '2. DAFTAR SISWA:' as '';
SELECT u.id, u.username, u.full_name, s.class, s.status 
FROM users u 
JOIN students s ON u.id = s.user_id 
WHERE u.role = 'siswa';

SELECT '' as '';
SELECT '3. TEST LOGIN GURU DENGAN NAMA:' as '';
SELECT 'Septriana Manik' as nama_guru,
       IF(EXISTS(SELECT 1 FROM users WHERE full_name = 'Septriana Manik' AND role IN ('admin', 'guru')), 
          '✓ TERDAFTAR - BISA LOGIN DENGAN NAMA', '✗ TIDAK TERDAFTAR') as status;

SELECT '' as '';
SELECT '4. STATISTIK DATABASE:' as '';
SELECT 
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM users WHERE role = 'admin') as admin_count,
    (SELECT COUNT(*) FROM users WHERE role = 'guru') as guru_count,
    (SELECT COUNT(*) FROM users WHERE role = 'siswa') as siswa_count,
    (SELECT COUNT(*) FROM materials) as materi_count,
    (SELECT COUNT(*) FROM problems) as masalah_count,
    (SELECT COUNT(*) FROM submissions) as submission_count;

SELECT '' as '';
SELECT '5. LOGIN INFORMATION:' as '';
SELECT 'Guru/Admin: Login dengan nama lengkap (contoh: "Septriana Manik") - TANPA PASSWORD' as info;
SELECT 'Siswa: Login dengan nama lengkap (contoh: "Ahmad Budi Santoso") - TANPA PASSWORD' as info;

SELECT '' as '';
SELECT '=== DATABASE BERHASIL DIBUAT ===' as '';
SELECT '=== MATHLine PBL Learning System ===' as '';
SELECT '=== Ready to use! ===' as '';