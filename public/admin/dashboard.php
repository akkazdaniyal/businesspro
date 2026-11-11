<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/middleware/admin-auth.php';

requireAdmin();

if (empty($_SESSION['admin_csrf'])) {
    $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
}

header('Cache-Control: no-store');

$adminName = htmlspecialchars(
    (string) ($_SESSION['admin_name'] ?? 'Administrator'),
    ENT_QUOTES,
    'UTF-8'
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | BusinessPro</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="/assets/css/admin.css">
</head>

<body class="bg-light">

    <main class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>BusinessPro Dashboard</h1>

            <form action="/admin/logout.php" method="post">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['admin_csrf'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >
                <button class="btn btn-outline-danger" type="submit">
                    Logout
                </button>
            </form>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h2 class="h4">Welcome, <?= $adminName ?>!</h2>
                <p class="text-muted mb-0">
                    You have successfully logged in.
                    Enquiry management will be added next.
                </p>
            </div>
        </div>

    </main>

</body>
</html>
