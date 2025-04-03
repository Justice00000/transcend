<?php
// Database configuration (already in your code)
$dsn = "pgsql:host=dpg-cvn925a4d50c73fv6m70-a;port=5432;dbname=admin_db_5jq5;user=admin_db_5jq5_user;password=zQ7Zey6xTtDtqT99fKgUepfsuEhCjIoZ";

try {
    $conn = new PDO($dsn);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$tracking_details = null;
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trackingsub'])) {
    $trackId = $_POST['trackId'];

    if (!empty($trackId)) {
        try {
            $stmt = $conn->prepare("SELECT * FROM tracking_orders WHERE tracking_number = :trackId");
            $stmt->bindParam(':trackId', $trackId);
            $stmt->execute();
            $tracking_details = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$tracking_details) {
                $error_message = "No shipment found with tracking number: " . htmlspecialchars($trackId);
            }
        } catch (PDOException $e) {
            $error_message = "Error fetching tracking details: " . $e->getMessage();
        }
    } else {
        $error_message = "Please enter a tracking number.";
    }
}
?>
