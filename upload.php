<?php
require_once "sqli.php";


session_start();

// Check if images were uploaded
if (isset($_FILES['image1']) && isset($_FILES['image2'])) {
    $image1 = $_FILES['image1']['tmp_name'];
    $image2 = $_FILES['image2']['tmp_name'];
    $user_id = 1;


    $image1Data = addslashes(file_get_contents($image1));
    $image2Data = addslashes(file_get_contents($image2));

    $sql = "INSERT INTO verify_status (user_id, image1, image2) VALUES ('$user_id', '$image1Data', '$image2Data')";

    if ($conn->query($sql) === TRUE) {
        echo "Images uploaded successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
