<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/middleware/admin-auth.php';
require_once __DIR__ . '/../../app/config/database.php';

requireAdmin();

header('Cache-Control: no-store');

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$id = filter_input(
    INPUT_GET,
    'id',
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
        'SELECT
            id,
            full_name,
            email,
            phone,
            enquiry_type,
            message,
            status,
            created_at
         FROM enquiries
         WHERE id = :id
         LIMIT 1'
    );

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $enquiry = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Enquiry details error: ' . $e->getMessage());

    http_response_code(500);
    exit('Unable to load enquiry details.');
}

if (!$enquiry) {
    http_response_code(404);
    exit('Enquiry not found.');
}

$adminName = escapeHtml(
    (string) ($_SESSION['admin_name'] ?? 'Administrator')
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Enquiry #<?= (int) $enquiry['id'] ?> | BusinessPro Admin
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="/assets/css/admin.css">
</head>

<body class="admin-dashboard-body">

    <div class="admin-layout">

        <!-- Sidebar -->
        <aside class="admin-sidebar">

            <div class="admin-sidebar-brand">
                <div class="admin-logo">BP</div>
                <h2>BusinessPro</h2>
                <p>Administration</p>
            </div>

            <nav class="admin-sidebar-nav" aria-label="Admin navigation">

                <a href="/admin/dashboard.php">
                    Dashboard
                </a>

                <a href="/admin/enquiries.php" class="active">
                    Enquiries
                </a>

                <span class="admin-nav-disabled">
                    Properties (Coming Soon)
                </span>

                <a href="/" target="_blank" rel="noopener noreferrer">
                    View Website
                </a>

            </nav>

            <div class="admin-sidebar-footer">
                Logged in as<br>
                <strong><?= $adminName ?></strong>
            </div>

        </aside>

        <!-- Main Content -->
        <main class="admin-main">

            <header class="admin-topbar">

                <div>
                    <span class="admin-eyebrow">ENQUIRY DETAILS</span>

                    <h1>Enquiry #<?= (int) $enquiry['id'] ?></h1>

                    <p>View customer enquiry information.</p>
                </div>

                <a href="/admin/enquiries.php"
                   class="btn btn-outline-secondary">
                    Back to Enquiries
                </a>

            </header>

            <section class="admin-table-card">

                <div class="admin-section-heading">
                    <h2>Customer Information</h2>
                    <p>Details submitted through the contact form.</p>
                </div>

                <div class="p-4">

                    <div class="row g-4">

                        <div class="col-md-6">
                            <strong>Full Name</strong>
                            <p>
                                <?= escapeHtml($enquiry['full_name']) ?>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <strong>Email Address</strong>
                            <p>
                                <?= escapeHtml($enquiry['email']) ?>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <strong>Phone Number</strong>
                            <p>
                                <?= escapeHtml($enquiry['phone'] ?? 'Not provided') ?>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <strong>Enquiry Type</strong>
                            <p>
                                <?= escapeHtml(
                                    ucfirst($enquiry['enquiry_type'])
                                ) ?>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <strong>Status</strong>
                            <p>
                                <span class="admin-status">
                                    <?= escapeHtml(
                                        ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $enquiry['status']
                                            )
                                        )
                                    ) ?>
                                </span>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <strong>Submitted On</strong>
                            <p>
                                <?= escapeHtml($enquiry['created_at']) ?>
                            </p>
                        </div>

                    </div>

                    <hr class="my-4">

                    <h2 class="h5 mb-3">Customer Message</h2>

                    <div class="enquiry-message-box">
                        <?= nl2br(
                            escapeHtml($enquiry['message'])
                        ) ?>
                    </div>

                </div>

            </section>

        </main>
    </div>

</body>
</html>
