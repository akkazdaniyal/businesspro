<?php
$pageTitle = 'BusinessPro | Real Estate in Karachi';

$properties = [
    [
        'title' => 'Modern Family Residence',
        'location' => 'DHA, Karachi',
        'price' => 'PKR 4.5 Crore',
        'beds' => 4,
        'baths' => 4,
        'area' => '240 Sq. Yards',
        'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=900&q=85'
    ],
    [
        'title' => 'Luxury Contemporary Villa',
        'location' => 'Bahria Town, Karachi',
        'price' => 'PKR 3.2 Crore',
        'beds' => 5,
        'baths' => 5,
        'area' => '300 Sq. Yards',
        'image' => 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=900&q=85'
    ],
    [
        'title' => 'Elegant Urban Apartment',
        'location' => 'Clifton, Karachi',
        'price' => 'PKR 1.8 Crore',
        'beds' => 3,
        'baths' => 3,
        'area' => '1,650 Sq. Ft.',
        'image' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=900&q=85'
    ]
];

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
    
    <section class="featured-properties py-5">
        <div class="container py-4">

            <div class="text-center mb-5">
                <span class="section-label">
                    EXPLORE OUR COLLECTION
                </span>

                <h2 class="section-title mt-2">
                    Featured Properties
                </h2>

                <p class="text-muted">
                    Discover our selection of sample properties
                    across Karachi.
                </p>
            </div>

            <div class="row g-4">

                <?php foreach ($properties as $property): ?>

                    <div class="col-lg-4 col-md-6">

                        <div class="property-card h-100">

                            <img
                                src="<?= htmlspecialchars($property['image'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($property['title'], ENT_QUOTES, 'UTF-8') ?>"
                                class="property-image"
                                loading="lazy"
                            >

                            <div class="property-content">

                                <span class="property-price">
                                    <?= htmlspecialchars($property['price'], ENT_QUOTES, 'UTF-8') ?>
                                </span>

                                <h4 class="property-title mt-3">
                                    <?= htmlspecialchars($property['title'], ENT_QUOTES, 'UTF-8') ?>
                                </h4>

                                <p class="property-location">
                                    <?= htmlspecialchars($property['location'], ENT_QUOTES, 'UTF-8') ?>
                                </p>

                                <div class="property-details">
                                    <span>
                                        <?= (int) $property['beds'] ?> Beds
                                    </span>

                                    <span>
                                        <?= (int) $property['baths'] ?> Baths
                                    </span>

                                    <span>
                                        <?= htmlspecialchars($property['area'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>

                                <a href="/contact.php" class="btn btn-outline-dark w-100 mt-4">
                                    Enquire Now
                                </a>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <p class="text-center text-muted small mt-4">
                All listings are fictional and shown for portfolio demonstration only.
            </p>

        </div>
    </section>
</main>

<?php
require_once __DIR__ . '/../app/includes/footer.php';
?>
