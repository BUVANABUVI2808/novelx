<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$db   = "nxsales_db";
$user = "nxsalesuser";
$pass = 'SHDF%$dsg%$%6563GFt';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name        = trim($_POST['name'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $requirement = trim($_POST['requirement'] ?? '');

    /* ---------------- VALIDATION ---------------- */

    // Name: only letters and spaces
    if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        die("Invalid name. Special characters or links are not allowed.");
    }

    // Block URLs in requirement
    if (preg_match("/(http|https|www\.)/i", $requirement)) {
        die("Links are not allowed in the requirement field.");
    }

    // Requirement: allow text, numbers, spaces, . , -
    if (!preg_match("/^[a-zA-Z0-9 .,\-]+$/", $requirement)) {
        die("Invalid requirement. Special characters are not allowed.");
    }

    /* --------------------------------------------- */

    $created_at = date('Y-m-d H:i:s');
    $updated_at = $created_at;
    $status     = 'New';
    $source     = 'website';

    $stmt = $conn->prepare(
        "INSERT INTO index_data 
        (name, email, phone, requirement, created_at, updated_at, status, source)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssssssss",
        $name,
        $email,
        $phone,
        $requirement,
        $created_at,
        $updated_at,
        $status,
        $source
    );

    if ($stmt->execute()) {
        header("Location: thank-you.html");
        exit;
    } else {
        echo "Database insert failed: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
