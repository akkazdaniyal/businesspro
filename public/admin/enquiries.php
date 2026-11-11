<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/middleware/admin-auth.php';
require_once __DIR__ . '/../../app/config/database.php';

requireAdmin();

header('Cache-Control: no-store');

$adminName = htmlspecialchars(
    (string) ($_SESSION['admin_name'] ?? 'Administrator'),
    ENT_QUOTES,
    'UTF-8'
);

$enquiries = [];
$databaseError = false;

try {
    $pdo = getDatabaseConnection();

    $stmt = $pdo->query(
        'SELECT
            id,
            full_name,
            email,
            phone,
            enquiry_type,
            status,
            created_at
         FROM enquiries
         ORDER BY created_at DESC, id DESC'
    );

    $enquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Enquiry list error: ' . $e->getMessage());
    $databaseError = true;
}

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Enquiries | BusinessPro Admin</title>

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

        <!-- Main -->
        <main class="admin-main">

            <header class="admin-topbar">
                <div>
                    <span class="admin-eyebrow">MANAGEMENT</span>
                    <h1>Enquiries</h1>
                    <p>Manage customer enquiries received through the website.</p>
                </div>

                <a href="/admin/dashboard.php" class="btn btn-outline-secondary">
                    Back to Dashboard
                </a>
            </header>

            <?php if ($databaseError): ?>

                <div class="alert alert-danger" role="alert">
                    Unable to load enquiries. Please try again later.
                </div>

            <?php else: ?>

                <section class="admin-table-card">

                    <div class="admin-section-heading">
                        <h2>All Enquiries</h2>
                        <p>
                            Total records:
                            <strong><?= count($enquiries) ?></strong>
                        </p>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">

                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if (empty($enquiries)): ?>

                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            No enquiries found.
                                        </td>
                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($enquiries as $enquiry): ?>

                                        <tr>
                                            <td>
                                                #<?= (int) $enquiry['id'] ?>
                                            </td>

                                            <td>
                                                <?= escapeHtml($enquiry['full_name']) ?>
                                            </td>

                                            <td>
                                                <?= escapeHtml($enquiry['email']) ?>
                                            </td>

                                            <td>
                                                <?= escapeHtml($enquiry['phone'] ?? '—') ?>
                                            </td>

                                            <td>
                                                <?= escapeHtml(ucfirst($enquiry['enquiry_type'])) ?>
                                            </td>

                                            <td>
                                                <span class="admin-status">
                                                    <?= escapeHtml(
                                                        ucwords(str_replace('_', ' ', $enquiry['status']))
                                                    ) ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?= escapeHtml($enquiry['created_at']) ?>
                                            </td>
                                    
                                            <td>
                                                <a
                                                    href="/admin/enquiry-view.php?id=<?= (int) $enquiry['id'] ?>"
                                                    class="btn btn-sm btn-outline-primary"
                                                >
                                                    View Details
                                                </a>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                            </tbody>

                        </table>
                    </div>

                </section>

            <?php endif; ?>

        </main>
    </div>

</body>
</html>
