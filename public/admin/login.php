<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/middleware/admin-auth.php';

startAdminSession();

header('Cache-Control: no-store');

if (!empty($_SESSION['admin_id'])) {
    header('Location: /admin/dashboard.php');
    exit;
}

if (empty($_SESSION['admin_csrf'])) {
    $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
}

$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login | BusinessPro</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>

<body class="admin-login-body">

    <main class="admin-login-wrapper">

        <div class="admin-login-card">

            <div class="text-center mb-4">
                <div class="admin-logo">BP</div>

                <h1 class="admin-login-title mt-3">
                    BusinessPro
                </h1>

                <p class="text-muted">
                    Administrator Login
                </p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            
            <form action="/admin/authenticate.php" method="post">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['admin_csrf'], ENT_QUOTES, 'UTF-8') ?>"
                >

                <div class="mb-3">
                    <label for="email" class="form-label">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        autocomplete="username"
                        maxlength="254"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-admin-login w-100">
                    Login to Dashboard
                </button>

            </form>

            <p class="admin-login-footer mt-4">
                Authorized administrators only.
            </p>

        </div>

    </main>

</body>
</html>
