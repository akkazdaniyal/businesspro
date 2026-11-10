
<?php
$pageTitle = 'About Us | BusinessPro Real Estate';
require_once __DIR__ . '/../app/includes/header.php';
?>

<main>

    <!-- Page Header -->
    <section class="page-header py-5">
        <div class="container text-center py-4">
            <span class="section-label">WHO WE ARE</span>

            <h1 class="page-title mt-3">
                About BusinessPro
            </h1>

            <p class="page-description mt-3">
                Helping you make confident real estate
                decisions in Karachi.
            </p>
        </div>
    </section>

    <!-- About Introduction -->
    <section class="about-intro py-5">
        <div class="container py-4">
            <div class="row align-items-center g-5">

                <div class="col-lg-6">
                    <img
                        src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1000&q=85"
                        alt="Modern residential interior"
                        class="img-fluid about-image"
                    >
                </div>

                <div class="col-lg-6">
                    <span class="section-label">
                        OUR STORY
                    </span>

                    <h2 class="section-title mt-3">
                        Your Property Journey,
                        Our Priority.
                    </h2>

                    <p class="text-muted mt-4">
                        BusinessPro is a fictional real estate
                        agency created as a demonstration of
                        a professional property services website.
                    </p>

                    <p class="text-muted">
                        Our sample services showcase how a
                        modern real estate business can help
                        customers explore buying, selling,
                        and renting opportunities.
                    </p>

                    <a href="/contact.php" class="btn btn-brand mt-3">
                        Contact Us
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- Mission and Vision -->
    <section class="mission-section py-5">
        <div class="container py-4">

            <div class="row g-4">

                <div class="col-md-6">
                    <div class="mission-card h-100">
                        <span class="section-label">
                            OUR MISSION
                        </span>

                        <h3 class="mt-3">Making Property Simple</h3>

                        <p>
                            To demonstrate a clear, accessible
                            and user-friendly property browsing
                            experience for prospective customers.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mission-card h-100">
                        <span class="section-label">
                            OUR VISION
                        </span>

                        <h3 class="mt-3">Building Confidence</h3>

                        <p>
                            To showcase how transparent
                            information and thoughtful design
                            can improve the real estate journey.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </section>

</main>

<?php
require_once __DIR__ . '/../app/includes/footer.php';
?>
