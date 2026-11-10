
<?php
$pageTitle = 'Our Services | BusinessPro Real Estate';

$services = [
    [
        'number' => '01',
        'title' => 'Buy Property',
        'description' => 'Explore residential and commercial properties that match your preferences and budget.',
        'features' => [
            'Residential properties',
            'Commercial properties',
            'Property comparisons'
        ]
    ],
    [
        'number' => '02',
        'title' => 'Sell Property',
        'description' => 'Present your property professionally and connect with prospective buyers.',
        'features' => [
            'Property presentation',
            'Listing assistance',
            'Buyer enquiries'
        ]
    ],
    [
        'number' => '03',
        'title' => 'Rent Property',
        'description' => 'Discover rental properties suited to your lifestyle and business requirements.',
        'features' => [
            'Residential rentals',
            'Commercial rentals',
            'Rental enquiries'
        ]
    ],
    [
        'number' => '04',
        'title' => 'Property Consultation',
        'description' => 'Explore property options and understand important considerations before making decisions.',
        'features' => [
            'Property requirements',
            'Location comparisons',
            'General property guidance'
        ]
    ]
];

require_once __DIR__ . '/../app/includes/header.php';
?>

<main>

    <!-- Page Header -->
    <section class="page-header py-5">
        <div class="container text-center py-4">
            <span class="section-label">
                WHAT WE OFFER
            </span>

            <h1 class="page-title mt-3">
                Our Real Estate Services
            </h1>

            <p class="page-description mt-3">
                Explore property services designed to make
                buying, selling and renting more convenient.
            </p>
        </div>
    </section>

    <!-- Services -->
    <section class="services-page py-5">
        <div class="container py-4">

            <div class="row g-4">

                <?php foreach ($services as $service): ?>

                    <div class="col-lg-6">

                        <div class="service-detail-card h-100">

                            <span class="service-number">
                                <?= htmlspecialchars($service['number'], ENT_QUOTES, 'UTF-8') ?>
                            </span>

                            <h3 class="mt-3">
                                <?= htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>

                            <p class="service-description">
                                <?= htmlspecialchars($service['description'], ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <ul class="service-features">
                                <?php foreach ($service['features'] as $feature): ?>
                                    <li>
                                        <?= htmlspecialchars($feature, ENT_QUOTES, 'UTF-8') ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <a href="/contact.php" class="btn btn-outline-dark mt-3">
                                Enquire Now
                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>
    </section>

    <!-- CTA -->
    <section class="contact-cta py-5">
        <div class="container py-5 text-center">

            <span class="cta-label">
                NEED PROPERTY ASSISTANCE?
            </span>

            <h2 class="cta-title mt-3">
                Let's Discuss Your Property Goals
            </h2>

            <p class="cta-description mt-3">
                Tell us what you're looking for and
                explore the next steps.
            </p>

            <a href="/contact.php" class="btn btn-cta mt-4">
                Contact Us
            </a>

        </div>
    </section>

</main>

<?php
require_once __DIR__ . '/../app/includes/footer.php';
?>
