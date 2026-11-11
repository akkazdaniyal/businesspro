<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/middleware/admin-auth.php';
require_once __DIR__ . '/../../app/config/database.php';

requireAdmin();

$enquiryFlash = $_SESSION['enquiry_flash'] ?? null;
unset($_SESSION['enquiry_flash']);

header('Cache-Control: no-store');

$adminName = htmlspecialchars(
    (string) ($_SESSION['admin_name'] ?? 'Administrator'),
    ENT_QUOTES,
    'UTF-8'
);

$enquiries = [];
$databaseError = false;

$perPage = 10;
$currentPage = 1;
$totalPages = 1;
$totalRecords = 0;

// Read search and filter inputs
$search = trim((string) ($_GET['search'] ?? ''));
$status = (string) ($_GET['status'] ?? '');
$type = (string) ($_GET['type'] ?? '');

// Limit input size
$search = mb_substr($search, 0, 100);

// Allowed status values
$allowedStatuses = ['new', 'in_progress', 'closed'];

if (!in_array($status, $allowedStatuses, true)) {
    $status = '';
}

// Build query safely
$sql = 'SELECT
            id,
            full_name,
            email,
            phone,
            enquiry_type,
            status,
            created_at
        FROM enquiries
        WHERE 1 = 1';

$params = [];

if ($search !== '') {
    $sql .= ' AND (full_name LIKE :search_name
                  OR email LIKE :search_email)';

    $params['search_name'] = '%' . $search . '%';
    $params['search_email'] = '%' . $search . '%';
}

if ($status !== '') {
    $sql .= ' AND status = :status';
    $params['status'] = $status;
}

// Read available enquiry types from database
$availableTypes = [];

try {
    $pdo = getDatabaseConnection();

    $typeStmt = $pdo->query(
        'SELECT DISTINCT enquiry_type
         FROM enquiries
         ORDER BY enquiry_type ASC'
    );

    $availableTypes = $typeStmt->fetchAll(PDO::FETCH_COLUMN);

    if ($type !== '' && in_array($type, $availableTypes, true)) {
        $sql .= ' AND enquiry_type = :type';
        $params['type'] = $type;
    } else {
        $type = '';
    }

    // Pagination settings
    $perPage = 10;

    $pageInput = filter_input(
        INPUT_GET,
        'page',
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    $currentPage = $pageInput ?: 1;

    // Count records matching the current filters
    $countSql = 'SELECT COUNT(*) FROM enquiries WHERE 1 = 1';

    if ($search !== '') {
        $countSql .= ' AND (
            full_name LIKE :search_name
            OR email LIKE :search_email
        )';
    }

    if ($status !== '') {
        $countSql .= ' AND status = :status';
    }

    if ($type !== '') {
        $countSql .= ' AND enquiry_type = :type';
    }

    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);

    $totalRecords = (int) $countStmt->fetchColumn();

    $totalPages = max(1, (int) ceil($totalRecords / $perPage));
    $currentPage = min($currentPage, $totalPages);

    $offset = ($currentPage - 1) * $perPage;

    // Fetch only records for the current page
    $sql .= ' ORDER BY created_at DESC, id DESC
            LIMIT :limit OFFSET :offset';

    $stmt = $pdo->prepare($sql);

    // Bind filter parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
    }

    // Bind pagination parameters as integers
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    $enquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Enquiry search error: ' . $e->getMessage());
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

            <?php if (
                is_array($enquiryFlash) &&
                in_array(
                    $enquiryFlash['type'] ?? '',
                    ['success', 'warning', 'danger'],
                    true
                ) &&
                is_string($enquiryFlash['message'] ?? null)
            ): ?>
                <div
                    class="alert alert-<?= escapeHtml($enquiryFlash['type']) ?>"
                    role="alert"
                >
                    <?= escapeHtml($enquiryFlash['message']) ?>
                </div>
            <?php endif; ?>

                <!-- Search and Filters -->
                <section class="admin-filter-card mb-4">

                    <form action="/admin/enquiries.php" method="get">

                        <div class="row g-3 align-items-end">

                            <div class="col-lg-5">
                                <label for="search" class="form-label">
                                    Search Customer
                                </label>

                                <input
                                    type="search"
                                    id="search"
                                    name="search"
                                    class="form-control"
                                    placeholder="Search by name or email"
                                    maxlength="100"
                                    value="<?= escapeHtml($search) ?>"
                                >
                            </div>

                            <div class="col-lg-3">
                                <label for="status" class="form-label">
                                    Status
                                </label>

                                <select
                                    id="status"
                                    name="status"
                                    class="form-select"
                                >
                                    <option value="">All Statuses</option>

                                    <?php foreach ($allowedStatuses as $option): ?>
                                        <option
                                            value="<?= escapeHtml($option) ?>"
                                            <?= $status === $option ? 'selected' : '' ?>
                                        >
                                            <?= escapeHtml(
                                                ucwords(str_replace('_', ' ', $option))
                                            ) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <label for="type" class="form-label">
                                    Enquiry Type
                                </label>

                                <select
                                    id="type"
                                    name="type"
                                    class="form-select"
                                >
                                    <option value="">All Types</option>

                                    <?php foreach ($availableTypes as $option): ?>
                                        <option
                                            value="<?= escapeHtml($option) ?>"
                                            <?= $type === $option ? 'selected' : '' ?>
                                        >
                                            <?= escapeHtml(ucfirst($option)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-lg-2 d-flex gap-2">
                                <button
                                    type="submit"
                                    class="btn btn-primary flex-grow-1"
                                >
                                    Filter
                                </button>

                                <a
                                    href="/admin/enquiries.php"
                                    class="btn btn-outline-secondary"
                                >
                                    Reset
                                </a>
                            </div>

                        </div>

                    </form>

                </section>

                <section class="admin-table-card">

                    <div class="admin-section-heading">
                        <h2>All Enquiries</h2>
                        <p>
                            Total records:
                            <strong><?= $totalRecords ?></strong>
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

                    <?php if (!$databaseError && $totalPages > 1): ?>

                        <nav
                            class="admin-pagination"
                            aria-label="Enquiry pagination"
                        >

                            <p class="mb-0 text-muted">
                                Page <?= $currentPage ?> of <?= $totalPages ?>
                            </p>

                            <ul class="pagination mb-0">

                                <?php
                                // Preserve active search and filters
                                $queryParams = [
                                    'search' => $search,
                                    'status' => $status,
                                    'type' => $type,
                                ];

                                $pageUrl = function (int $page) use ($queryParams): string {
                                    return '/admin/enquiries.php?' . http_build_query(
                                        array_merge($queryParams, ['page' => $page])
                                    );
                                };
                                ?>

                                <!-- Previous -->
                                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                    <?php if ($currentPage > 1): ?>
                                        <a
                                            class="page-link"
                                            href="<?= escapeHtml($pageUrl($currentPage - 1)) ?>"
                                        >
                                            Previous
                                        </a>
                                    <?php else: ?>
                                        <span class="page-link">Previous</span>
                                    <?php endif; ?>
                                </li>

                                <!-- Page Numbers -->
                                <?php
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $currentPage + 2);
                                ?>

                                <?php for ($page = $startPage; $page <= $endPage; $page++): ?>
                                    <li class="page-item <?= $page === $currentPage ? 'active' : '' ?>">
                                        <a
                                            class="page-link"
                                            href="<?= escapeHtml($pageUrl($page)) ?>"
                                            <?= $page === $currentPage ? 'aria-current="page"' : '' ?>
                                        >
                                            <?= $page ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <!-- Next -->
                                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                    <?php if ($currentPage < $totalPages): ?>
                                        <a
                                            class="page-link"
                                            href="<?= escapeHtml($pageUrl($currentPage + 1)) ?>"
                                        >
                                            Next
                                        </a>
                                    <?php else: ?>
                                        <span class="page-link">Next</span>
                                    <?php endif; ?>
                                </li>

                            </ul>

                        </nav>

                    <?php endif; ?>

                </section>

            <?php endif; ?>

        </main>
    </div>

</body>
</html>
