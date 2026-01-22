<?php
// api/get_related_problems.php
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

header('Content-Type: application/json');

// Cek parameter
if (!isset($_GET['material_id']) || !is_numeric($_GET['material_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid material ID']);
    exit();
}

$material_id = intval($_GET['material_id']);

$db = new Database();
$conn = $db->getConnection();

// Get problems related to material
$stmt = $conn->prepare("
    SELECT p.id, p.title, p.phase 
    FROM problems p 
    WHERE p.material_id = ? 
    ORDER BY p.phase
");
$stmt->bind_param("i", $material_id);
$stmt->execute();
$result = $stmt->get_result();

$problems = [];
while ($row = $result->fetch_assoc()) {
    $problems[] = $row;
}

echo json_encode([
    'success' => true,
    'problems' => $problems
]);

$conn->close();
?>