<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Define the path to the backend API folder
$apiPath = realpath(__DIR__ . '/../api'); // or use a direct path if realpath fails

// Validate and sanitize the requested endpoint
$endpoint = $_GET['endpoint'] ?? null;
if (!$endpoint || !preg_match('/^[\w\-]+\.php$/', $endpoint)) {
    echo json_encode(['success' => false, 'message' => 'Invalid endpoint']);
    exit;
}

// Construct the full path to the target file in the api folder
$targetFile = $apiPath . '/' . $endpoint;
if (!file_exists($targetFile)) {
    echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
    exit;
}

// Include and execute the target backend script
include $targetFile;
