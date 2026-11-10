<?php

declare(strict_types=1);

session_start();

$pageTitle = 'Contact Us | BusinessPro Real Estate';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$contactErrors = $_SESSION['contact_errors'] ?? [];
$contactSuccess = $_SESSION['contact_success'] ?? null;

unset(
    $_SESSION['contact_errors'],
    $_SESSION['contact_success']
);

require_once __DIR__ . '/../app/includes/header.php';
?>


<main>

    <!-- Page Header -->
    <section class="page-header py-5">
        <div class="container text-center py-4">

            <span class="section-label">
                GET IN TOUCH
            </span>

            <h1 class="page-title mt-3">
                Contact Us
            </h1>

            <p class="page-description mt-3">
                Have a property enquiry? Send us a message
                using the form below.
            </p>

        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-page py-5">
        <div class="container py-4">

            <div class="row g-5">

                <!-- Contact Information -->
                <div class="col-lg-5">

                    <span class="section-label">
                        CONTACT INFORMATION
                    </span>

                    <h2 class="section-title mt-3">
                        Let's Talk Property
                    </h2>

                    <p class="contact-intro mt-3">
                        Whether you're interested in buying,
                        selling or renting, share your
                        requirements with us.
                    </p>

                    <div class="contact-info-card mt-4">
                        <h5>Location</h5>
                        <p>Karachi, Pakistan</p>
                    </div>

                    <div class="contact-info-card mt-3">
                        <h5>Email</h5>
                        <p>Contact details available on request</p>
                    </div>

                    <div class="contact-info-card mt-3">
                        <h5>Working Hours</h5>
                        <p>By appointment</p>
                    </div>

                    <p class="small text-muted mt-4">
                        BusinessPro is a demonstration website.
                        Contact details and services are illustrative.
                    </p>

                </div>

                <!-- Contact Form -->
                <div class="col-lg-7">

                    <div class="contact-form-card">

                        <h3>Send an Enquiry</h3>

                        <p class="text-muted mb-4">
                            Complete the form and we'll review
                            your property requirements.
                        </p>

                        <?php if ($contactSuccess): ?>
                            <div class="alert alert-success" role="status">
                                <?= htmlspecialchars($contactSuccess, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($contactErrors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    <?php foreach ($contactErrors as $error): ?>
                                        <li>
                                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form
                            action="/submit-contact.php"
                            method="post"
                            id="contactForm"
                        >
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                            >

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label for="fullName" class="form-label">
                                        Full Name *
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="fullName"
                                        name="full_name"
                                        maxlength="100"
                                        autocomplete="name"
                                        required
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">
                                        Email Address *
                                    </label>

                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        maxlength="254"
                                        autocomplete="email"
                                        required
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label">
                                        Phone Number
                                    </label>

                                    <input
                                        type="tel"
                                        class="form-control"
                                        id="phone"
                                        name="phone"
                                        maxlength="25"
                                        autocomplete="tel"
                                        placeholder="+92 300 1234567"
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label for="enquiryType" class="form-label">
                                        Enquiry Type *
                                    </label>

                                    <select
                                        class="form-select"
                                        id="enquiryType"
                                        name="enquiry_type"
                                        required
                                    >
                                        <option value="">
                                            Select an option
                                        </option>
                                        <option value="buy">Buy Property</option>
                                        <option value="sell">Sell Property</option>
                                        <option value="rent">Rent Property</option>
                                        <option value="general">General Enquiry</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="message" class="form-label">
                                        Your Message *
                                    </label>

                                    <textarea
                                        class="form-control"
                                        id="message"
                                        name="message"
                                        rows="6"
                                        minlength="10"
                                        maxlength="2000"
                                        placeholder="Tell us about your property requirements..."
                                        required
                                    ></textarea>
                                </div>

                                <div class="col-12">                              
                                    <button
                                        type="submit"
                                        class="btn btn-cta w-100"
                                    >
                                        Send Enquiry
                                    </button>
                                </div>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>
    </section>

</main>

<?php
require_once __DIR__ . '/../app/includes/footer.php';
?>
