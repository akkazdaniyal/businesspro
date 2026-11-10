<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/database.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /contact.php', true, 303);
    exit;
}

// Validate CSRF token
$submittedToken = $_POST['csrf_token'] ?? '';
$sessionToken = $_SESSION['csrf_token'] ?? '';

if (
    !is_string($submittedToken) ||
    !is_string($sessionToken) ||
    $sessionToken === '' ||
    !hash_equals($sessionToken, $submittedToken)
) {
    http_response_code(403);
    exit('Invalid form request.');
}

// Read and normalize form values
$fullName = trim((string) ($_POST['full_name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$enquiryType = (string) ($_POST['enquiry_type'] ?? '');
$message = trim((string) ($_POST['message'] ?? ''));

$allowedTypes = ['buy', 'sell', 'rent', 'general'];

$errors = [];

if ($fullName === '' || mb_strlen($fullName) > 100) {
    $errors[] = 'Enter a valid full name.';
}

if (
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($email) > 254
) {
    $errors[] = 'Enter a valid email address.';
}

if (
    mb_strlen($phone) > 25 ||
    ($phone !== '' && !preg_match('/^[0-9+\s().-]{7,25}$/', $phone))
) {
    $errors[] = 'Enter a valid phone number.';
}

if (!in_array($enquiryType, $allowedTypes, true)) {
    $errors[] = 'Select a valid enquiry type.';
}

if (mb_strlen($message) < 10 || mb_strlen($message) > 2000) {
    $errors[] = 'Message must be between 10 and 2000 characters.';
}

if ($errors !== []) {
    $_SESSION['contact_errors'] = $errors;
    header('Location: /contact.php', true, 303);
    exit;
}

try {
    $pdo = getDatabaseConnection();

    $stmt = $pdo->prepare(
        'INSERT INTO enquiries
         (full_name, email, phone, enquiry_type, message)
         VALUES
         (:full_name, :email, :phone, :enquiry_type, :message)'
    );

    $stmt->execute([
        'full_name' => $fullName,
        'email' => $email,
        'phone' => $phone !== '' ? $phone : null,
        'enquiry_type' => $enquiryType,
        'message' => $message,
    ]);

    $_SESSION['contact_success'] =
        'Your enquiry has been submitted successfully.';

    // Rotate CSRF token after successful submission
    unset($_SESSION['csrf_token']);

} catch (Throwable $e) {
    error_log('Contact form database error: ' . $e->getMessage());

    $_SESSION['contact_errors'] = [
        'Unable to submit your enquiry right now. Please try again.'
    ];
}

header('Location: /contact.php', true, 303);
exit;
