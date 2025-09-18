<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['file'];

// Validate file type
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$fileType = mime_content_type($file['tmp_name']);

if (!in_array($fileType, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.']);
    exit;
}

// Validate file size (max 10MB)
$maxSize = 10 * 1024 * 1024; // 10MB
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 10MB.']);
    exit;
}

// Create uploads directory if it doesn't exist
$uploadDir = '../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate unique filename
$fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
$fileName = uniqid() . '_' . time() . '.' . $fileExtension;
$filePath = $uploadDir . $fileName;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $filePath)) {
    // Return success with file URL
    $fileUrl = 'uploads/' . $fileName;
    echo json_encode([
        'success' => true,
        'message' => 'File uploaded successfully',
        'file_url' => $fileUrl,
        'file_name' => $fileName
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
}
?>

