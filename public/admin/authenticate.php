<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/middleware/admin-auth.php';
require_once __DIR__ . '/../../app/config/database.php';

startAdminSession();

header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/login.php', true, 303);
    exit;
}

function loginFailed(): never
{
    $_SESSION['login_error'] = 'Invalid email or password.';
    header('Location: /admin/login.php', true, 303);
    exit;
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

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (
    !is_string($email) ||
    !is_string($password) ||
    strlen($email) > 254 ||
    strlen($password) > 1024 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    loginFailed();
}

try {
    $pdo = getDatabaseConnection();

    $stmt = $pdo->prepare(
        'SELECT id, full_name, password_hash
         FROM admin_users
         WHERE email = :email
         LIMIT 1'
    );

    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch();

    if (
        !$admin ||
        !password_verify($password, $admin['password_hash'])
    ) {
        loginFailed();
    }

    session_regenerate_id(true);

    $_SESSION['admin_id'] = (int) $admin['id'];
    $_SESSION['admin_name'] = $admin['full_name'];

    unset($_SESSION['admin_csrf']);

    header('Location: /admin/dashboard.php', true, 303);
    exit;

} catch (PDOException $e) {
    error_log('Admin authentication error: ' . $e->getMessage());

    $_SESSION['login_error'] =
        'Login is temporarily unavailable. Please try again.';

    header('Location: /admin/login.php', true, 303);
    exit;
}
