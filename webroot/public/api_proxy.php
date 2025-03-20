<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Define the path to the backend API folder (now in `app-backend/api/`)
$apiPath = realpath(__DIR__ . '/../../app-backend/api'); // Adjusted path

// Validate and sanitize the requested endpoint
$endpoint = $_GET['endpoint'] ?? null;

// Check if the endpoint is provided and is a valid PHP file
if (!$endpoint || !preg_match('/^[\w\-]+\.php$/', $endpoint)) {
    echo json_encode(['success' => false, 'message' => 'Invalid endpoint specified.']);
    exit;
}

// Construct the full path to the target file in the api folder
$targetFile = $apiPath . '/' . $endpoint;

// Check if the file exists
if (!file_exists($targetFile)) {
    echo json_encode(['success' => false, 'message' => 'Endpoint file not found.']);
    exit;
}

// If the requested endpoint is PDF generation, handle it differently
if ($endpoint === 'generate_pdf.php') {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="waiting_list_confirmation.pdf"');

    include $targetFile;
    exit;
}

// Include and execute the target backend script
try {
    include $targetFile;
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error including endpoint: ' . $e->getMessage()]);
}
