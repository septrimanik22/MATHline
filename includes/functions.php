<?php
// includes/functions.php
require_once 'config.php';
require_once 'db_connection.php';

/**
 * Fungsi helper untuk website MATHLine
 */

/**
 * Upload file dengan validasi
 */
function uploadFile($file, $directory, $allowed_types = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error uploading file'];
    }
    
    // Cek ukuran file
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return ['success' => false, 'message' => 'File too large. Max: ' . (UPLOAD_MAX_SIZE / 1024 / 1024) . 'MB'];
    }
    
    // Cek ekstensi file
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        return ['success' => false, 'message' => 'File type not allowed. Allowed: ' . implode(', ', $allowed_types)];
    }
    
    // Buat nama file unik
    $file_name = time() . '_' . uniqid() . '.' . $file_ext;
    $upload_path = UPLOAD_PATH . $directory . '/';
    
    // Buat directory jika belum ada
    if (!file_exists($upload_path)) {
        mkdir($upload_path, 0777, true);
    }
    
    $target_file = $upload_path . $file_name;
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return [
            'success' => true,
            'file_name' => $file_name,
            'file_path' => 'assets/uploads/' . $directory . '/' . $file_name,
            'full_path' => $target_file
        ];
    }
    
    return ['success' => false, 'message' => 'Failed to upload file'];
}

/**
 * Delete file
 */
function deleteFile($file_path) {
    $full_path = ROOT_PATH . '/' . $file_path;
    
    if (file_exists($full_path)) {
        return unlink($full_path);
    }
    
    return false;
}

/**
 * Format bytes to human readable
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Generate random password
 */
function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    
    return $password;
}

/**
 * Get user's display name
 */
function getDisplayName($user_id) {
    $db = Database::getInstance();
    $sql = "SELECT full_name FROM users WHERE id = ?";
    $user = $db->fetchOne($sql, [$user_id], 'i');
    
    return $user ? $user['full_name'] : 'Unknown User';
}

/**
 * Get student's class
 */
function getStudentClass($user_id) {
    $db = Database::getInstance();
    $sql = "SELECT class FROM students WHERE user_id = ?";
    $student = $db->fetchOne($sql, [$user_id], 'i');
    
    return $student ? $student['class'] : '';
}

/**
 * Log activity
 */
function logActivity($user_id, $action, $description = '') {
    $db = Database::getInstance();
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $sql = "INSERT INTO logs (user_id, action, description, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?)";
    
    return $db->insert($sql, [$user_id, $action, $description, $ip_address, $user_agent], 'issss');
}

/**
 * Send notification (placeholder untuk future development)
 */
function sendNotification($user_id, $type, $title, $message) {
    $db = Database::getInstance();
    
    $sql = "INSERT INTO notifications (user_id, type, title, message, is_read) 
            VALUES (?, ?, ?, ?, 0)";
    
    return $db->insert($sql, [$user_id, $type, $title, $message], 'isss');
}

/**
 * Get unread notifications count
 */
function getUnreadNotificationsCount($user_id) {
    $db = Database::getInstance();
    
    $sql = "SELECT COUNT(*) as count FROM notifications 
            WHERE user_id = ? AND is_read = 0";
    
    $result = $db->fetchOne($sql, [$user_id], 'i');
    return $result ? $result['count'] : 0;
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number
 */
function isValidPhone($phone) {
    return preg_match('/^[0-9]{10,15}$/', $phone);
}

/**
 * Get gravatar image
 */
function getGravatar($email, $size = 80) {
    $hash = md5(strtolower(trim($email)));
    return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d=mp";
}

/**
 * Generate pagination
 */
function generatePagination($current_page, $total_pages, $url) {
    if ($total_pages <= 1) {
        return '';
    }
    
    $pagination = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    // Previous button
    if ($current_page > 1) {
        $pagination .= '<li class="page-item">
            <a class="page-link" href="' . $url . '?page=' . ($current_page - 1) . '">Previous</a>
        </li>';
    }
    
    // Page numbers
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        $active = $i == $current_page ? 'active' : '';
        $pagination .= '<li class="page-item ' . $active . '">
            <a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a>
        </li>';
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $pagination .= '<li class="page-item">
            <a class="page-link" href="' . $url . '?page=' . ($current_page + 1) . '">Next</a>
        </li>';
    }
    
    $pagination .= '</ul></nav>';
    return $pagination;
}

/**
 * Calculate grade percentage
 */
function calculateGradePercentage($score, $max_points) {
    if ($max_points == 0) {
        return 0;
    }
    return round(($score / $max_points) * 100, 2);
}

/**
 * Get grade letter
 */
function getGradeLetter($percentage) {
    if ($percentage >= 85) return 'A';
    if ($percentage >= 70) return 'B';
    if ($percentage >= 60) return 'C';
    if ($percentage >= 50) return 'D';
    return 'E';
}

/**
 * Get grade color
 */
function getGradeColor($percentage) {
    if ($percentage >= 85) return 'success';
    if ($percentage >= 70) return 'primary';
    if ($percentage >= 60) return 'warning';
    return 'danger';
}
?>