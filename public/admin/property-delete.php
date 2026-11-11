<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/middleware/admin-auth.php';
require_once __DIR__ . '/../../app/config/database.php';

requireAdmin();

header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Method not allowed.');
}

$sessionToken = $_SESSION['admin_csrf'] ?? '';
$submittedToken = $_POST['csrf_token'] ?? '';

if (
    !is_string($sessionToken) ||
    $sessionToken === '' ||
    !is_string($submittedToken) ||
    !hash_equals($sessionToken, $submittedToken)
) {
    http_response_code(403);
    exit('Invalid security token.');
}

$rawId = $_POST['property_id'] ?? null;

if (!is_string($rawId) || !ctype_digit($rawId)) {
    http_response_code(400);
    exit('Invalid property ID.');
}

$id = filter_var(
    $rawId,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($id === false) {
    http_response_code(400);
    exit('Invalid property ID.');
}

try {
    $pdo = getDatabaseConnection();

    $stmt = $pdo->prepare(
        'DELETE FROM properties WHERE id = :id'
    );

    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() === 0) {
        header(
            'Location: /admin/properties.php?delete_error=not_found',
            true,
            303
        );
        exit;
    }

    header(
        'Location: /admin/properties.php?deleted=1',
        true,
        303
    );
    exit;

} catch (PDOException $e) {
    error_log('Property deletion failed: ' . $e->getMessage());

    header(
        'Location: /admin/properties.php?delete_error=database',
        true,
        303
    );
    exit;
}
