<?php
// Include the Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';  // Adjust path if necessary

use TCPDF;

// Ensure required parameters are passed
if (isset($_GET['name']) && isset($_GET['email']) && isset($_GET['position'])) {
    $name = htmlspecialchars($_GET['name']);
    $email = htmlspecialchars($_GET['email']);
    $position = htmlspecialchars($_GET['position']);

    // Create a new PDF instance
    $pdf = new TCPDF();
    $pdf->AddPage();
    
    // Set document information
    $pdf->SetFont('helvetica', '', 12);
    
    // Add content to the PDF
    $pdf->Cell(0, 10, "Waiting List Confirmation", 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "Name: " . $name, 0, 1);
    $pdf->Cell(0, 10, "Email: " . $email, 0, 1);
    $pdf->Cell(0, 10, "Position: " . $position, 0, 1);
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "Thank you for signing up!", 0, 1, 'C');
    
    // Output the PDF directly to the browser as a download
    $pdf->Output('waiting_list_confirmation.pdf', 'D'); // 'D' triggers the file download dialog
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters for PDF generation']);
}
