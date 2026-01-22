<?php
// api/check_notifications.php
require_once '../includes/config.php';
require_once '../includes/db_connection.php';
require_once '../includes/auth.php';

session_start();
header('Content-Type: application/json');

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$db = new Database();
$conn = $db->getConnection();

$notifications = [];

if ($role === 'siswa') {
    // Notifikasi untuk siswa
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM submissions 
        WHERE student_id = ? AND status = 'graded' AND created_at > DATE_SUB(NOW(), INTERVAL 1 DAY)
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $notifications[] = [
            'type' => 'new_grades',
            'count' => $row['count'],
            'message' => 'Anda memiliki ' . $row['count'] . ' tugas baru yang sudah dinilai'
        ];
    }
} elseif ($role === 'guru') {
    // Notifikasi untuk guru
    $result = $conn->query("
        SELECT COUNT(*) as count 
        FROM submissions 
        WHERE status = 'submitted' AND submitted_at > DATE_SUB(NOW(), INTERVAL 1 DAY)
    ");
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $notifications[] = [
            'type' => 'new_submissions',
            'count' => $row['count'],
            'message' => $row['count'] . ' tugas baru menunggu penilaian'
        ];
    }
}

echo json_encode([
    'success' => true,
    'notifications' => $notifications,
    'count' => count($notifications)
]);

$conn->close();
?>