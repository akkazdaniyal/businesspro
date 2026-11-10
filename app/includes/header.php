
<?php
$pageTitle = $pageTitle ?? 'BusinessPro Real Estate';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/">
            Business<span class="brand-accent">Pro</span>
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                <li class="nav-item">
                    <a class="nav-link" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/services.php">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/portfolio.php">Properties</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-brand" href="/contact.php">Contact Us</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
