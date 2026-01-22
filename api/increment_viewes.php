<?php
// api/increment_views.php
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

header('Content-Type: application/json');

// Cek apakah request method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Cek parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid material ID']);
    exit();
}

$material_id = intval($_GET['id']);

$db = new Database();
$conn = $db->getConnection();

// Update view count
$stmt = $conn->prepare("UPDATE materials SET views = views + 1 WHERE id = ?");
$stmt->bind_param("i", $material_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'View count updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update view count']);
}

$conn->close();
?>