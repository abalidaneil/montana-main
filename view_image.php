<?php
require_once "sqli.php";

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = intval($_GET['id']);
    $type = $_GET['type'] == '1' ? 'image1' : 'image2';

    $query = "SELECT $type FROM verify_status WHERE id = $id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imageData = $row[$type];

        // Determine image type
        $imageInfo = getimagesizefromstring($imageData);
        $mimeType = $imageInfo ? $imageInfo['mime'] : 'image/jpeg'; // Default to JPEG if detection fails

        // Set appropriate headers
        header("Content-Type: $mimeType");
        header("Content-Length: " . strlen($imageData));
        header("Cache-Control: private, max-age=0");

        // Output the image data
        echo $imageData;
        exit;
    }
}

// If image not found, return a 404
header("HTTP/1.0 404 Not Found");
echo "Image not found";
?>