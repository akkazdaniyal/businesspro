
<?php
$pageTitle = 'Properties | BusinessPro Real Estate';

$properties = [
    [
        'title' => 'Modern Family Residence',
        'location' => 'DHA',
        'type' => 'House',
        'purpose' => 'Buy',
        'price' => 'PKR 4.5 Crore',
        'beds' => 4,
        'baths' => 4,
        'area' => '240 Sq. Yards',
        'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=900&q=85'
    ],
    [
        'title' => 'Luxury Contemporary Villa',
        'location' => 'Bahria Town',
        'type' => 'Villa',
        'purpose' => 'Buy',
        'price' => 'PKR 3.2 Crore',
        'beds' => 5,
        'baths' => 5,
        'area' => '300 Sq. Yards',
        'image' => 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=900&q=85'
    ],
    [
        'title' => 'Elegant Urban Apartment',
        'location' => 'Clifton',
        'type' => 'Apartment',
        'purpose' => 'Buy',
        'price' => 'PKR 1.8 Crore',
        'beds' => 3,
        'baths' => 3,
        'area' => '1,650 Sq. Ft.',
        'image' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=900&q=85'
    ],
    [
        'title' => 'Premium Rental Apartment',
        'location' => 'DHA',
        'type' => 'Apartment',
        'purpose' => 'Rent',
        'price' => 'PKR 120,000 / Month',
        'beds' => 3,
        'baths' => 3,
        'area' => '1,500 Sq. Ft.',
        'image' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=900&q=85'
    ],
    [
        'title' => 'Spacious Family House',
        'location' => 'Gulshan-e-Iqbal',
        'type' => 'House',
        'purpose' => 'Rent',
        'price' => 'PKR 180,000 / Month',
        'beds' => 4,
        'baths' => 4,
        'area' => '240 Sq. Yards',
        'image' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=900&q=85'
    ],
    [
        'title' => 'Contemporary Rental Villa',
        'location' => 'Bahria Town',
        'type' => 'Villa',
        'purpose' => 'Rent',
        'price' => 'PKR 250,000 / Month',
        'beds' => 5,
        'baths' => 5,
        'area' => '350 Sq. Yards',
        'image' => 'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?auto=format&fit=crop&w=900&q=85'
    ]
];

require_once __DIR__ . '/../app/includes/header.php';
?>

<main>

    <!-- Page Header -->
    <section class="page-header py-5">
        <div class="container text-center py-4">
            <span class="section-label">EXPLORE PROPERTIES</span>

            <h1 class="page-title mt-3">
                Find Your Ideal Property
            </h1>

            <p class="page-description mt-3">
                Browse our fictional property listings
                across Karachi.
            </p>
        </div>
    </section>

    <!-- Property Listings -->
    <section class="properties-page py-5">
        <div class="container py-4">

            <!-- Filters -->
            <div class="property-filters mb-5">
                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="purposeFilter" class="form-label">
                            Purpose
                        </label>
                        <select id="purposeFilter" class="form-select">
                            <option value="all">Buy or Rent</option>
                            <option value="Buy">Buy</option>
                            <option value="Rent">Rent</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="locationFilter" class="form-label">
                            Location
                        </label>
                        <select id="locationFilter" class="form-select">
                            <option value="all">All Locations</option>
                            <option value="DHA">DHA</option>
                            <option value="Clifton">Clifton</option>
                            <option value="Bahria Town">Bahria Town</option>
                            <option value="Gulshan-e-Iqbal">Gulshan-e-Iqbal</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="typeFilter" class="form-label">
                            Property Type
                        </label>
                        <select id="typeFilter" class="form-select">
                            <option value="all">All Types</option>
                            <option value="House">House</option>
                            <option value="Apartment">Apartment</option>
                            <option value="Villa">Villa</option>
                        </select>
                    </div>

                </div>
            </div>

            <p id="resultsCount" class="text-muted mb-4" aria-live="polite"></p>

            <!-- Cards -->
            <div class="row g-4" id="propertyGrid">

                <?php foreach ($properties as $property): ?>

                    <div
                        class="col-lg-4 col-md-6 property-item"
                        data-purpose="<?= htmlspecialchars($property['purpose'], ENT_QUOTES, 'UTF-8') ?>"
                        data-location="<?= htmlspecialchars($property['location'], ENT_QUOTES, 'UTF-8') ?>"
                        data-type="<?= htmlspecialchars($property['type'], ENT_QUOTES, 'UTF-8') ?>"
                    >

                        <div class="property-card h-100">

                            <img
                                src="<?= htmlspecialchars($property['image'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($property['title'], ENT_QUOTES, 'UTF-8') ?>"
                                class="property-image"
                                loading="lazy"
                            >

                            <div class="property-content">

                                <span class="property-badge">
                                    <?= htmlspecialchars($property['purpose'], ENT_QUOTES, 'UTF-8') ?>
                                </span>

                                <div class="property-price mt-3">
                                    <?= htmlspecialchars($property['price'], ENT_QUOTES, 'UTF-8') ?>
                                </div>

                                <h4 class="property-title mt-3">
                                    <?= htmlspecialchars($property['title'], ENT_QUOTES, 'UTF-8') ?>
                                </h4>

                                <p class="property-location">
                                    <?= htmlspecialchars($property['location'], ENT_QUOTES, 'UTF-8') ?>, Karachi
                                </p>

                                <div class="property-details">
                                    <span><?= (int) $property['beds'] ?> Beds</span>
                                    <span><?= (int) $property['baths'] ?> Baths</span>
                                    <span><?= htmlspecialchars($property['area'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>

                                <a href="/contact.php" class="btn btn-outline-dark w-100 mt-4">
                                    Enquire Now
                                </a>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <div id="noResults" class="text-center py-5 d-none" role="status">
                <h3>No Properties Found</h3>
                <p class="text-muted">
                    Try changing your filters.
                </p>
            </div>

            <p class="text-center text-muted small mt-5">
                All listings and prices are fictional demonstration data.
            </p>

        </div>
    </section>

</main>

<?php
require_once __DIR__ . '/../app/includes/footer.php';
?>
