<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/middleware/admin-auth.php';
require_once __DIR__ . '/../../app/config/database.php';

requireAdmin();

if (empty($_SESSION['admin_csrf'])) {
    $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
}

$allowedStatuses = ['new', 'in_progress', 'closed'];

$statusMessages = [
    'updated' => 'Enquiry status updated successfully.',
    'invalid' => 'Invalid status selected.',
    'error' => 'Unable to update enquiry status.',
];

$statusMessage = $_GET['result'] ?? '';
$statusMessage = is_string($statusMessage) ? $statusMessage : '';


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

                <a href="/admin/properties.php">
                    Properties
                </a>

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

                    <h2 class="h5 mb-3">Update Enquiry Status</h2>

                    <?php if (isset($statusMessages[$statusMessage])): ?>
                        <div
                            class="alert <?= $statusMessage === 'updated'
                                ? 'alert-success'
                                : 'alert-danger' ?>"
                            role="alert"
                        >
                            <?= escapeHtml($statusMessages[$statusMessage]) ?>
                        </div>
                    <?php endif; ?>

                    <form action="/admin/update-enquiry-status.php" method="post">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= escapeHtml($_SESSION['admin_csrf']) ?>"
                        >

                        <input
                            type="hidden"
                            name="enquiry_id"
                            value="<?= (int) $enquiry['id'] ?>"
                        >

                        <div class="row g-3 align-items-end">

                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold">
                                    Enquiry Status
                                </label>

                                <select
                                    id="status"
                                    name="status"
                                    class="form-select"
                                    required
                                >
                                    <?php foreach ($allowedStatuses as $option): ?>
                                        <option
                                            value="<?= escapeHtml($option) ?>"
                                            <?= $enquiry['status'] === $option
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            <?= escapeHtml(
                                                ucwords(str_replace('_', ' ', $option))
                                            ) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    Save Status
                                </button>
                            </div>

                        </div>

                    </form>

                    <hr class="my-4">

                    <h2 class="h5 mb-3">Customer Message</h2>

                    <div class="enquiry-message-box">
                        <?= nl2br(
                            escapeHtml($enquiry['message'])
                        ) ?>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h2 class="h5 text-danger mb-1">Delete Enquiry</h2>
                            <p class="text-muted mb-0">
                                Permanently remove this enquiry from the database.
                            </p>
                        </div>

                        <form
                            action="/admin/delete-enquiry.php"
                            method="post"
                            onsubmit="return confirm('Are you sure you want to permanently delete this enquiry? This action cannot be undone.');"
                        >
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= escapeHtml($_SESSION['admin_csrf']) ?>"
                            >

                            <input
                                type="hidden"
                                name="enquiry_id"
                                value="<?= (int) $enquiry['id'] ?>"
                            >

                            <button type="submit" class="btn btn-outline-danger">
                                Delete Enquiry
                            </button>
                        </form>
                    </div>

                </div>

            </section>

        </main>
    </div>

</body>
</html>
