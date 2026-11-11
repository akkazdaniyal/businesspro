<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/middleware/admin-auth.php';
require_once __DIR__ . '/../../app/config/database.php';

requireAdmin();

header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

$submittedToken = $_POST['csrf_token'] ?? '';
$sessionToken = $_SESSION['admin_csrf'] ?? '';

if (
    !is_string($submittedToken) ||
    !is_string($sessionToken) ||
    $sessionToken === '' ||
    !hash_equals($sessionToken, $submittedToken)
) {
    http_response_code(403);
    exit('Invalid form request.');
}

$id = filter_input(
    INPUT_POST,
    'enquiry_id',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($id === false || $id === null) {
    http_response_code(400);
    exit('Invalid enquiry ID.');
}

$status = $_POST['status'] ?? '';

$allowedStatuses = ['new', 'in_progress', 'closed'];

if (
    !is_string($status) ||
    !in_array($status, $allowedStatuses, true)
) {
    header(
        'Location: /admin/enquiry-view.php?id=' .
        $id . '&result=invalid',
        true,
        303
    );
    exit;
}

try {
    $pdo = getDatabaseConnection();

    $stmt = $pdo->prepare(
        'UPDATE enquiries
         SET status = :status
         WHERE id = :id'
    );

    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    $stmt->execute();

    $result = 'updated';

} catch (PDOException $e) {
    error_log('Enquiry status update error: ' . $e->getMessage());
    $result = 'error';
}

header(
    'Location: /admin/enquiry-view.php?id=' .
    $id . '&result=' . $result,
    true,
    303
);
exit;
