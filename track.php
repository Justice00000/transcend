<?php
// Connect to DB
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Result</title>
    <link href="css/bootstrap.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Tracking Result</h2>
    <hr>

    <?php if ($tracking_details): ?>
        <h4>Tracking Number: <?= htmlspecialchars($tracking_details['tracking_number']) ?></h4>
        <p>Status: <?= htmlspecialchars($tracking_details['status']) ?></p>
        <p>Sender: <?= htmlspecialchars($tracking_details['sender_name']) ?> (<?= htmlspecialchars($tracking_details['sender_contact']) ?>)</p>
        <p>Receiver: <?= htmlspecialchars($tracking_details['receiver_name']) ?> (<?= htmlspecialchars($tracking_details['receiver_contact']) ?>)</p>
        <p>Dispatch Location: <?= htmlspecialchars($tracking_details['dispatch_location']) ?></p>
        <p>Destination: <?= htmlspecialchars($tracking_details['destination']) ?></p>
        <p>Dispatch Date: <?= htmlspecialchars($tracking_details['dispatch_date']) ?></p>
        <p>Delivery Date: <?= htmlspecialchars($tracking_details['delivery_date']) ?></p>
        <p>Shipment Mode: <?= htmlspecialchars($tracking_details['shipment_mode']) ?></p>
        <p>Carrier: <?= htmlspecialchars($tracking_details['carrier']) ?> (Ref: <?= htmlspecialchars($tracking_details['carrier_ref_no']) ?>)</p>
        <p>Weight: <?= htmlspecialchars($tracking_details['weight']) ?> kg</p>
        <p>Payment Mode: <?= htmlspecialchars($tracking_details['payment_mode']) ?></p>
        <p>Description: <?= htmlspecialchars($tracking_details['package_desc']) ?></p>
        <?php if (!empty($tracking_details['package_image'])): ?>
            <img src="<?= htmlspecialchars($tracking_details['package_image']) ?>" width="300" alt="Package Image">
        <?php endif; ?>
    <?php else: ?>
        <p style="color:red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <br><a href="track.html" class="btn btn-primary">Back to Tracking Page</a>
</div>
</body>
</html>
