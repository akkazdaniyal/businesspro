<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/middleware/admin-auth.php';

requireAdmin();

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

$_SESSION = [];

session_destroy();

header('Location: /admin/login.php', true, 303);
exit;
