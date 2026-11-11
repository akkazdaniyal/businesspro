<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

// Run this script from the PHP CLI only.
if (PHP_SAPI !== 'cli') {
    exit('CLI access only.');
}

function prompt(string $message): string
{
    echo $message;
    return trim((string) fgets(STDIN));
}

$email = prompt('Admin email: ');
$fullName = prompt('Admin full name: ');

if (
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($email) > 254
) {
    exit("Invalid email address.\n");
}

if ($fullName === '' || mb_strlen($fullName) > 100) {
    exit("Invalid full name.\n");
}

// Read password without displaying it in Terminal
echo 'Admin password: ';

$sttyState = shell_exec('stty -g');
if (!is_string($sttyState) || trim($sttyState) === '') {
    exit("Unable to securely read password.\n");
}

try {
    shell_exec('stty -echo');
    $password = rtrim((string) fgets(STDIN), "\r\n");
} finally {
    shell_exec('stty ' . escapeshellarg(trim($sttyState)));
    echo "\n";
}

if (strlen($password) < 6 || strlen($password) > 72) {
    exit("Password must be between 6 and 72 bytes.\n");
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

unset($password);

try {
    $pdo = getDatabaseConnection();

    $stmt = $pdo->prepare(
        'INSERT INTO admin_users (full_name, email, password_hash)
         VALUES (:full_name, :email, :password_hash)'
    );

    $stmt->execute([
        'full_name' => $fullName,
        'email' => $email,
        'password_hash' => $passwordHash,
    ]);

    echo "Admin account created successfully!\n";

} catch (PDOException $e) {
    error_log('Admin creation failed: ' . $e->getMessage());
    echo "Unable to create admin account. Check database permissions or duplicate email.\n";
    exit(1);
}
