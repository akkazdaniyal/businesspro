<?php
declare(strict_types=1);

function startAdminSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    session_name('businesspro_admin');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/admin',
        'secure' => !empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function requireAdmin(): void
{
    startAdminSession();

    if (
        !isset($_SESSION['admin_id']) ||
        !is_int($_SESSION['admin_id'])
    ) {
        header('Location: /admin/login.php', true, 303);
        exit;
    }
}
