<?php
// api/get_submission.php
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

// Set header JSON
header('Content-Type: application/json');

// Cek apakah request method GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Cek parameter id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid submission ID']);
    exit();
}

$submission_id = intval($_GET['id']);

// Koneksi database
$db = new Database();
$conn = $db->getConnection();

// Get submission data
$stmt = $conn->prepare("
    SELECT s.*, p.points as max_points 
    FROM submissions s 
    JOIN problems p ON s.problem_id = p.id 
    WHERE s.id = ?
");
$stmt->bind_param("i", $submission_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Submission not found']);
    exit();
}

$submission = $result->fetch_assoc();

// Return data as JSON
echo json_encode([
    'success' => true,
    'answer' => nl2br(htmlspecialchars($submission['answer'])),
    'file_path' => $submission['file_path'],
    'status' => $submission['status'],
    'score' => $submission['score'],
    'max_points' => $submission['max_points'],
    'feedback' => $submission['feedback'] ? nl2br(htmlspecialchars($submission['feedback'])) : null
]);

$conn->close();
?>