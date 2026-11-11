<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/middleware/admin-auth.php';
require_once __DIR__ . '/../../app/config/database.php';

requireAdmin();

header('Cache-Control: no-store');

if (empty($_SESSION['admin_csrf'])) {
    $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
}

$adminName = htmlspecialchars(
    (string) ($_SESSION['admin_name'] ?? 'Administrator'),
    ENT_QUOTES,
    'UTF-8'
);

$stats = [
    'total' => 0,
    'new' => 0,
    'in_progress' => 0,
    'closed' => 0,
];

$recentEnquiries = [];
$databaseError = false;

try {
    $pdo = getDatabaseConnection();

    $stmt = $pdo->query(
        "SELECT
            COUNT(*) AS total,
            COUNT(CASE WHEN status = 'new' THEN 1 END) AS new_count,
            COUNT(CASE WHEN status = 'in_progress' THEN 1 END) AS progress_count,
            COUNT(CASE WHEN status = 'closed' THEN 1 END) AS closed_count
         FROM enquiries"
    );

    $result = $stmt->fetch();

    $stats = [
        'total' => (int) $result['total'],
        'new' => (int) $result['new_count'],
        'in_progress' => (int) $result['progress_count'],
        'closed' => (int) $result['closed_count'],
    ];

    $recentStmt = $pdo->query(
        'SELECT id, full_name, email, enquiry_type, status, created_at
         FROM enquiries
         ORDER BY created_at DESC, id DESC
         LIMIT 5'
    );

    $recentEnquiries = $recentStmt->fetchAll();

} catch (PDOException $e) {
    error_log('Dashboard query failed: ' . $e->getMessage());
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

    <title>Admin Dashboard | BusinessPro</title>

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
                <a href="/admin/dashboard.php" class="active">
                    Dashboard
                </a>

                <a href="/admin/enquiries.php">
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
                    <span class="admin-eyebrow">OVERVIEW</span>
                    <h1>Dashboard</h1>
                    <p>Welcome back, <?= $adminName ?>!</p>
                </div>

                <form action="/admin/logout.php" method="post">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= escapeHtml($_SESSION['admin_csrf']) ?>"
                    >

                    <button type="submit" class="btn btn-outline-danger">
                        Logout
                    </button>
                </form>
            </header>

            <?php if ($databaseError): ?>
                <div class="alert alert-danger" role="alert">
                    Dashboard data is temporarily unavailable.
                    Please try again later.
                </div>
            <?php else: ?>

                <!-- Statistics -->
                <section class="row g-4 mb-5" aria-label="Enquiry statistics">

                    <?php
                    $statCards = [
                        ['label' => 'Total Enquiries', 'value' => $stats['total']],
                        ['label' => 'New Enquiries', 'value' => $stats['new']],
                        ['label' => 'In Progress', 'value' => $stats['in_progress']],
                        ['label' => 'Closed Enquiries', 'value' => $stats['closed']],
                    ];
                    ?>

                    <?php foreach ($statCards as $card): ?>
                        <div class="col-xl-3 col-md-6">
                            <div class="admin-stat-card">
                                <p><?= escapeHtml($card['label']) ?></p>
                                <h2><?= (int) $card['value'] ?></h2>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </section>

                <!-- Recent Enquiries -->
                <section class="admin-table-card">
                    <div class="admin-section-heading">
                        <div>
                            <h2>Recent Enquiries</h2>
                            <p>Latest five contact form submissions</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (empty($recentEnquiries)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            No enquiries received yet.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentEnquiries as $enquiry): ?>
                                        <tr>
                                            <td>
                                                <?= escapeHtml($enquiry['full_name']) ?>
                                            </td>
                                            <td>
                                                <?= escapeHtml($enquiry['email']) ?>
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
