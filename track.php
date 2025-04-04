<?php
// Database configuration for PostgreSQL
$dsn = "pgsql:host=dpg-cvn925a4d50c73fv6m70-a;port=5432;dbname=admin_db_5jq5;user=admin_db_5jq5_user;password=zQ7Zey6xTtDtqT99fKgUepfsuEhCjIoZ";

try {
    $conn = new PDO($dsn);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if the form is submitted with a tracking number
    if (isset($_POST['trackId']) && !empty($_POST['trackId'])) {
        $tracking_number = $_POST['trackId'];
        
        // Fetch tracking record from the database
        $stmt = $conn->prepare("SELECT * FROM tracking_orders WHERE tracking_number = :tracking_number");
        $stmt->bindParam(':tracking_number', $tracking_number);
        $stmt->execute();
        $tracking_record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if a record was found
        if ($tracking_record) {
            // Display tracking information
            echo "<p>Tracking Number: " . htmlspecialchars($tracking_record['tracking_number']) . "</p>";
            echo "<p>Status: " . htmlspecialchars($tracking_record['status']) . "</p>";
        } else {
            // If no record is found
            echo "<p>Tracking number not found.</p>";
        }
    } else {
        // Default message when no tracking ID is entered
        echo "<p>Please enter a tracking ID to view shipment details.</p>";
    }
} catch (PDOException $e) {
    $error_message = "Database Error: " . $e->getMessage();
    echo "<p>Error: " . $error_message . "</p>";
}
?>