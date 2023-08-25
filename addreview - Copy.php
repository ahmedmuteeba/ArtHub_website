<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required form fields are provided
    if (isset($_POST['fullName'], $_POST['comment'], $_POST['rating'], $_POST['email'],$_POST['artId'])) {
        // Retrieve form data
        $fullName = $_POST['fullName'];
        $comment = $_POST['comment'];
        $rating = $_POST['rating'];
        $email = $_POST['email'];
        $artId = $_POST['artId'];

        // Connect to the database
        $servername = 'localhost';
        $dbUsername = 'root';
        $dbPassword = '';
        $dbName = 'project';

        $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);
        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }s
        // Retrieve userId based on fullName and email
        $sql = "SELECT userId FROM users WHERE fullName =? AND email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $fullName,$email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userId = $row['userId'];
            // Insert the rating and review into the database
            $insertStmt = $conn->prepare("INSERT INTO reviews (userId, rating, comment,artId) VALUES (?, ?, ?, ?)");
            $insertStmt->bind_param("iisi",$userId, $rating, $comment,$artId);
            $insertStmt->execute();

            // Check if the insertion was successful
            if ($insertStmt->affected_rows > 0) {
                echo 'Review added successfully!';
                header("Location: artwork.php");
            } else {
                echo 'Error adding review.';
            }

            $insertStmt->close();
        } else {
            echo 'User not found.';
        }

        $stmt->close();
        $conn->close();
    } else {
        echo 'Please provide all required form fields.';
    }
}
?>
