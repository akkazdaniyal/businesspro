<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/middleware/admin-auth.php';
require_once __DIR__ . '/../../app/config/database.php';

requireAdmin();

header('Cache-Control: no-store');

if (empty($_SESSION['admin_csrf'])) {
    $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
}

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$adminName = escapeHtml(
    (string) ($_SESSION['admin_name'] ?? 'Administrator')
);

$errors = [];

$values = [
    'title' => '',
    'description' => '',
    'price' => '',
    'city' => 'Karachi',
    'location' => '',
    'property_type' => '',
    'purpose' => '',
    'area_sqft' => '',
    'bedrooms' => '',
    'bathrooms' => '',
    'status' => 'draft',
    'is_featured' => '0',
];

function createSlug(string $title): string
{
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');

    return $slug !== '' ? substr($slug, 0, 180) : 'property';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $submittedToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($submittedToken) ||
        !hash_equals($_SESSION['admin_csrf'], $submittedToken)
    ) {
        http_response_code(403);
        exit('Invalid security token. Please refresh and try again.');
    }

    foreach ($values as $key => $default) {
        if ($key === 'is_featured') {
            $values[$key] = isset($_POST[$key]) ? '1' : '0';
            continue;
        }

        $input = $_POST[$key] ?? '';

        $values[$key] = is_string($input) ? trim($input) : '';
    }

    $requiredFields = [
        'title',
        'description',
        'price',
        'city',
        'location',
        'property_type',
        'purpose',
        'status',
    ];

    foreach ($requiredFields as $field) {
        if ($values[$field] === '') {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }

    if (strlen($values['title']) > 200) {
        $errors[] = 'Property title must not exceed 200 characters.';
    }

    if (strlen($values['city']) > 100) {
        $errors[] = 'City must not exceed 100 characters.';
    }

    if (strlen($values['location']) > 150) {
        $errors[] = 'Location must not exceed 150 characters.';
    }

    if (
        $values['price'] !== '' &&
        (
            !is_numeric($values['price']) ||
            (float) $values['price'] <= 0 ||
            (float) $values['price'] > 9999999999999.99
        )
    ) {
        $errors[] = 'Enter a valid positive property price.';
    }

    if (
        $values['area_sqft'] !== '' &&
        (
            !is_numeric($values['area_sqft']) ||
            (float) $values['area_sqft'] <= 0
        )
    ) {
        $errors[] = 'Area must be greater than zero.';
    }

    foreach (['bedrooms', 'bathrooms'] as $field) {
        if (
            $values[$field] !== '' &&
            (
                filter_var($values[$field], FILTER_VALIDATE_INT) === false ||
                (int) $values[$field] < 0
            )
        ) {
            $errors[] = ucfirst($field) . ' must be a valid non-negative number.';
        }
    }

    $allowedTypes = ['house', 'apartment', 'plot', 'commercial'];
    $allowedPurposes = ['sale', 'rent'];
    $allowedStatuses = ['draft', 'published', 'archived'];

    if (
        $values['property_type'] !== '' &&
        !in_array($values['property_type'], $allowedTypes, true)
    ) {
        $errors[] = 'Invalid property type.';
    }

    if (
        $values['purpose'] !== '' &&
        !in_array($values['purpose'], $allowedPurposes, true)
    ) {
        $errors[] = 'Invalid property purpose.';
    }

    if (
        $values['status'] !== '' &&
        !in_array($values['status'], $allowedStatuses, true)
    ) {
        $errors[] = 'Invalid property status.';
    }

    if (empty($errors)) {
        try {
            $pdo = getDatabaseConnection();

            $baseSlug = createSlug($values['title']);

            // Reserve room for a unique random suffix.
            $slug = substr($baseSlug, 0, 180)
                . '-' . bin2hex(random_bytes(6));

            $stmt = $pdo->prepare(
                "INSERT INTO properties (
                    title,
                    slug,
                    description,
                    price,
                    location,
                    city,
                    property_type,
                    purpose,
                    bedrooms,
                    bathrooms,
                    area_sqft,
                    status,
                    is_featured
                ) VALUES (
                    :title,
                    :slug,
                    :description,
                    :price,
                    :location,
                    :city,
                    :property_type,
                    :purpose,
                    :bedrooms,
                    :bathrooms,
                    :area_sqft,
                    :status,
                    :is_featured
                )"
            );

            $stmt->execute([
                ':title' => $values['title'],
                ':slug' => $slug,
                ':description' => $values['description'],
                ':price' => $values['price'],
                ':location' => $values['location'],
                ':city' => $values['city'],
                ':property_type' => $values['property_type'],
                ':purpose' => $values['purpose'],
                ':bedrooms' => $values['bedrooms'] === ''
                    ? null : (int) $values['bedrooms'],
                ':bathrooms' => $values['bathrooms'] === ''
                    ? null : (int) $values['bathrooms'],
                ':area_sqft' => $values['area_sqft'] === ''
                    ? null : $values['area_sqft'],
                ':status' => $values['status'],
                ':is_featured' => (int) $values['is_featured'],
            ]);

            header('Location: /admin/properties.php?created=1', true, 303);
            exit;

        } catch (PDOException $e) {
            error_log('Property creation failed: ' . $e->getMessage());
            $errors[] = 'Unable to save the property. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Property | BusinessPro Admin</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="/assets/css/admin.css">
</head>

<body class="admin-dashboard-body">

<div class="admin-layout">

    <aside class="admin-sidebar">

        <div class="admin-sidebar-brand">
            <div class="admin-logo">BP</div>
            <h2>BusinessPro</h2>
            <p>Administration</p>
        </div>

        <nav class="admin-sidebar-nav" aria-label="Admin navigation">

            <a href="/admin/dashboard.php">Dashboard</a>
            <a href="/admin/enquiries.php">Enquiries</a>

            <a href="/admin/properties.php" class="active">
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

    <main class="admin-main">

        <header class="admin-topbar">

            <div>
                <span class="admin-eyebrow">PROPERTY MANAGEMENT</span>
                <h1>Add New Property</h1>
                <p>Create a new real estate property listing.</p>
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

        <div class="mb-4">
            <a href="/admin/properties.php"
               class="btn btn-outline-secondary">
                &larr; Back to Properties
            </a>
        </div>

        <?php if (!empty($errors)): ?>

            <div class="alert alert-danger" role="alert">
                <strong>Please correct the following:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errors as $error): ?>
                        <li><?= escapeHtml($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

        <?php endif; ?>

        <section class="admin-table-card">

            <div class="admin-section-heading">
                <div>
                    <h2>Property Information</h2>
                    <p>Enter the details of your new property.</p>
                </div>
            </div>

            <form action="/admin/property-create.php" method="post">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= escapeHtml($_SESSION['admin_csrf']) ?>"
                >

                <div class="row g-4">

                    <div class="col-12">
                        <label for="title" class="form-label">
                            Property Title *
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="form-control"
                            maxlength="200"
                            value="<?= escapeHtml($values['title']) ?>"
                            required
                        >
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">
                            Description *
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            class="form-control"
                            rows="5"
                            required
                        ><?= escapeHtml($values['description']) ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="price" class="form-label">
                            Price (PKR) *
                        </label>

                        <input
                            type="number"
                            id="price"
                            name="price"
                            class="form-control"
                            min="0.01"
                            max="9999999999999.99"
                            step="0.01"
                            value="<?= escapeHtml($values['price']) ?>"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="city" class="form-label">
                            City *
                        </label>

                        <input
                            type="text"
                            id="city"
                            name="city"
                            class="form-control"
                            maxlength="100"
                            value="<?= escapeHtml($values['city']) ?>"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="location" class="form-label">
                            Location / Area *
                        </label>

                        <input
                            type="text"
                            id="location"
                            name="location"
                            class="form-control"
                            maxlength="150"
                            value="<?= escapeHtml($values['location']) ?>"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="property_type" class="form-label">
                            Property Type *
                        </label>

                        <select
                            id="property_type"
                            name="property_type"
                            class="form-select"
                            required
                        >
                            <option value="">Select property type</option>

                            <?php foreach (['house', 'apartment', 'plot', 'commercial'] as $type): ?>
                                <option
                                    value="<?= escapeHtml($type) ?>"
                                    <?= $values['property_type'] === $type ? 'selected' : '' ?>
                                >
                                    <?= escapeHtml(ucfirst($type)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="purpose" class="form-label">
                            Purpose *
                        </label>

                        <select
                            id="purpose"
                            name="purpose"
                            class="form-select"
                            required
                        >
                            <option value="">Select purpose</option>
                            <option value="sale" <?= $values['purpose'] === 'sale' ? 'selected' : '' ?>>
                                For Sale
                            </option>
                            <option value="rent" <?= $values['purpose'] === 'rent' ? 'selected' : '' ?>>
                                For Rent
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="area_sqft" class="form-label">
                            Area (Square Feet)
                        </label>

                        <input
                            type="number"
                            id="area_sqft"
                            name="area_sqft"
                            class="form-control"
                            min="0.01"
                            max="9999999999.99"
                            step="0.01"
                            value="<?= escapeHtml($values['area_sqft']) ?>"
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="bedrooms" class="form-label">
                            Bedrooms
                        </label>

                        <input
                            type="number"
                            id="bedrooms"
                            name="bedrooms"
                            class="form-control"
                            min="0"
                            step="1"
                            value="<?= escapeHtml($values['bedrooms']) ?>"
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="bathrooms" class="form-label">
                            Bathrooms
                        </label>

                        <input
                            type="number"
                            id="bathrooms"
                            name="bathrooms"
                            class="form-control"
                            min="0"
                            step="1"
                            value="<?= escapeHtml($values['bathrooms']) ?>"
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label">
                            Property Status *
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="form-select"
                            required
                        >
                            <?php foreach (['draft', 'published', 'archived'] as $status): ?>
                                <option
                                    value="<?= escapeHtml($status) ?>"
                                    <?= $values['status'] === $status ? 'selected' : '' ?>
                                >
                                    <?= escapeHtml(ucfirst($status)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check mb-2">

                            <input
                                type="checkbox"
                                id="is_featured"
                                name="is_featured"
                                value="1"
                                class="form-check-input"
                                <?= $values['is_featured'] === '1' ? 'checked' : '' ?>
                            >

                            <label for="is_featured" class="form-check-label">
                                Mark as Featured Property
                            </label>

                        </div>
                    </div>

                </div>

                <div class="d-flex gap-3 mt-4">

                    <button type="submit" class="btn btn-primary">
                        Save Property
                    </button>

                    <a
                        href="/admin/properties.php"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </section>

    </main>

</div>

</body>
</html>
