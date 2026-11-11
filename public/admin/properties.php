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

$properties = [];
$databaseError = false;

try {
    $pdo = getDatabaseConnection();

    $stmt = $pdo->query(
        "SELECT
            id,
            title,
            price,
            location,
            city,
            property_type,
            purpose,
            status,
            is_featured,
            created_at
         FROM properties
         ORDER BY created_at DESC, id DESC"
    );

    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Properties query failed: ' . $e->getMessage());
    $databaseError = true;
}

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function formatPrice(string $price): string
{
    return 'PKR ' . number_format((float) $price, 0);
}

function propertyStatusClass(string $status): string
{
    return match ($status) {
        'published' => 'bg-success',
        'draft' => 'bg-secondary',
        'archived' => 'bg-warning text-dark',
        default => 'bg-secondary',
    };
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Properties | BusinessPro</title>

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

                <a href="/admin/enquiries.php">
                    Enquiries
                </a>

                <a href="/admin/properties.php" class="active" aria-current="page">
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

            <!-- Topbar -->
            <header class="admin-topbar">
                <div>
                    <span class="admin-eyebrow">PROPERTY MANAGEMENT</span>
                    <h1>Properties</h1>
                    <p>Manage your real estate property listings.</p>
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

            <?php if (
                !$databaseError &&
                isset($_GET['created']) &&
                $_GET['created'] === '1'
            ): ?>
                <div class="alert alert-success" role="status">
                    Property created successfully!
                </div>
            <?php endif; ?>

            <?php if (
                !$databaseError &&
                isset($_GET['updated']) &&
                $_GET['updated'] === '1'
            ): ?>
                <div class="alert alert-success" role="status">
                    Property updated successfully!
                </div>
            <?php endif; ?>

            <?php if (
                !$databaseError &&
                isset($_GET['deleted']) &&
                $_GET['deleted'] === '1'
            ): ?>
                <div class="alert alert-success" role="status">
                    Property deleted successfully!
                </div>
            <?php endif; ?>

            <?php if (
                isset($_GET['delete_error']) &&
                $_GET['delete_error'] === 'not_found'
            ): ?>
                <div class="alert alert-warning" role="alert">
                    Property not found. It may have already been deleted.
                </div>
            <?php elseif (
                isset($_GET['delete_error']) &&
                $_GET['delete_error'] === 'database'
            ): ?>
                <div class="alert alert-danger" role="alert">
                    Unable to delete the property. Please try again.
                </div>
            <?php endif; ?>

            <?php if ($databaseError): ?>

                <div class="alert alert-danger" role="alert">
                    Property data is temporarily unavailable.
                    Please try again later.
                </div>

            <?php else: ?>

                <!-- Property Summary -->
                <section class="row g-4 mb-4" aria-label="Property statistics">

                    <?php
                    $totalProperties = count($properties);

                    $publishedProperties = 0;
                    $draftProperties = 0;
                    $featuredProperties = 0;

                    foreach ($properties as $property) {
                        if ($property['status'] === 'published') {
                            $publishedProperties++;
                        }

                        if ($property['status'] === 'draft') {
                            $draftProperties++;
                        }

                        if ((int) $property['is_featured'] === 1) {
                            $featuredProperties++;
                        }
                    }

                    $statCards = [
                        [
                            'label' => 'Total Properties',
                            'value' => $totalProperties
                        ],
                        [
                            'label' => 'Published Properties',
                            'value' => $publishedProperties
                        ],
                        [
                            'label' => 'Draft Properties',
                            'value' => $draftProperties
                        ],
                        [
                            'label' => 'Featured Properties',
                            'value' => $featuredProperties
                        ],
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

                <!-- Properties Table -->
                <section class="admin-table-card">

                    <div class="admin-section-heading">
                        <div>
                            <h2>All Properties</h2>
                            <p>View and manage your property records</p>
                        </div>

                        <a href="/admin/property-create.php" class="btn btn-primary">
                            + Add Property
                        </a>
                    </div>

                    <div class="table-responsive">

                        <table class="table align-middle mb-0">

                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Property</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Purpose</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Featured</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if (empty($properties)): ?>

                                    <tr>
                                        <td colspan="8" class="text-center py-5">

                                            <h3 class="h5 mb-2">
                                                No properties found
                                            </h3>

                                            <p class="text-muted mb-0">
                                                Your properties will appear here
                                                once they are added.
                                            </p>

                                        </td>
                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($properties as $property): ?>

                                        <tr>

                                            <td>
                                                <?= (int) $property['id'] ?>
                                            </td>

                                            <td>
                                                <strong>
                                                    <?= escapeHtml((string) $property['title']) ?>
                                                </strong>

                                                <div class="small text-muted">
                                                    <?= escapeHtml((string) $property['location']) ?>,
                                                    <?= escapeHtml((string) $property['city']) ?>
                                                </div>
                                            </td>

                                            <td>
                                                <?= escapeHtml(
                                                    formatPrice((string) $property['price'])
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= escapeHtml((string) $property['property_type']) ?>
                                            </td>

                                            <td>
                                                <?= escapeHtml(
                                                    ucfirst((string) $property['purpose'])
                                                ) ?>
                                            </td>

                                            <td>
                                                <span class="badge <?= propertyStatusClass((string) $property['status']) ?>">
                                                    <?= escapeHtml(
                                                        ucfirst((string) $property['status'])
                                                    ) ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?php if ((int) $property['is_featured'] === 1): ?>
                                                    <span class="badge bg-primary">
                                                        Yes
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">
                                                        No
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <div class="d-flex gap-2">
                                                    
                                                    <a
                                                        href="/admin/property-edit.php?id=<?= (int) $property['id'] ?>"
                                                        class="btn btn-sm btn-outline-primary"
                                                    >
                                                        Edit
                                                    </a>
                                                    
                                                    <form
                                                        action="/admin/property-delete.php"
                                                        method="post"
                                                        onsubmit="return confirm('Are you sure you want to permanently delete this property? This action cannot be undone.');"
                                                    >
                                                        <input
                                                            type="hidden"
                                                            name="csrf_token"
                                                            value="<?= escapeHtml($_SESSION['admin_csrf']) ?>"
                                                        >

                                                        <input
                                                            type="hidden"
                                                            name="property_id"
                                                            value="<?= (int) $property['id'] ?>"
                                                        >

                                                        <button
                                                            type="submit"
                                                            class="btn btn-sm btn-outline-danger"
                                                        >
                                                            Delete
                                                        </button>
                                                    </form>

                                                </div>
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
