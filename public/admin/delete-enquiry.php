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

// Validate CSRF token
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

// Validate enquiry ID
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

try {
    $pdo = getDatabaseConnection();

    $stmt = $pdo->prepare(
        'DELETE FROM enquiries WHERE id = :id'
    );

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        $_SESSION['enquiry_flash'] = [
            'type' => 'warning',
            'message' => 'Enquiry not found or already deleted.',
        ];
    } else {
        $_SESSION['enquiry_flash'] = [
            'type' => 'success',
            'message' => 'Enquiry deleted successfully.',
        ];
    }

} catch (PDOException $e) {
    error_log('Enquiry deletion error: ' . $e->getMessage());

    $_SESSION['enquiry_flash'] = [
        'type' => 'danger',
        'message' => 'Unable to delete enquiry. Please try again.',
    ];
}

header('Location: /admin/enquiries.php', true, 303);
exit;
