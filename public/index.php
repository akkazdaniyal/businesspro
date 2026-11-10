
<?php
$pageTitle = 'BusinessPro | Real Estate in Karachi';
require_once __DIR__ . '/../app/includes/header.php';
?>

<main>
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-75 gy-5">
                <div class="col-lg-6">
                    <span class="hero-eyebrow">
                        REAL ESTATE SOLUTIONS
                    </span>

                    <h1 class="hero-title mt-3">
                        Find a Place You'll
                        <span>Love to Call Home.</span>
                    </h1>

                    <p class="hero-description mt-4">
                        Discover exceptional residential and
                        commercial properties with a real estate
                        partner focused on your needs.
                    </p>

                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="/portfolio.php" class="btn btn-brand btn-lg">
                            Explore Properties
                        </a>

                        <a href="/contact.php" class="btn btn-outline-dark btn-lg">
                            Talk to Us
                        </a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <img
                        src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=85"
                        alt="Modern luxury residential property"
                        class="img-fluid hero-image"
                    >
                </div>
            </div>
        </div>
    </section>

    <section class="services-section py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="section-label">WHAT WE OFFER</span>
                <h2 class="section-title mt-2">
                    Our Real Estate Services
                </h2>
                <p class="text-muted">
                    Property solutions tailored to your goals.
                </p>
            </div>

            <div class="row g-4">
                <?php
                $services = [
                    [
                        'title' => 'Buy Property',
                        'description' => 'Explore residential and commercial property opportunities.'
                    ],
                    [
                        'title' => 'Sell Property',
                        'description' => 'Present your property to prospective buyers.'
                    ],
                    [
                        'title' => 'Rent Property',
                        'description' => 'Find rental options that suit your needs and budget.'
                    ]
                ];

                foreach ($services as $service):
                ?>
                    <div class="col-md-4">
                        <div class="service-card h-100">
                            <h4>
                                <?= htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8') ?>
                            </h4>
                            <p>
                                <?= htmlspecialchars($service['description'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php
require_once __DIR__ . '/../app/includes/footer.php';
?>
