<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['trackingsub'])) {
    // Retrieve the tracking number entered by the user
    $trackingId = $_POST['trackId'];

    // Sample tracking data for demonstration (replace with actual database or API)
    $shipmentDetails = getShipmentDetails($trackingId);

    if ($shipmentDetails) {
        echo "<h3>Shipment Details for Tracking ID: $trackingId</h3>";
        echo "<p>Status: " . $shipmentDetails['status'] . "</p>";
        echo "<p>Location: " . $shipmentDetails['location'] . "</p>";
        echo "<p>Estimated Delivery: " . $shipmentDetails['estimated_delivery'] . "</p>";
    } else {
        echo "<p>No details found for Tracking ID: $trackingId. Please check and try again.</p>";
    }
}

function getShipmentDetails($trackingId) {
    // Sample shipment details, you would replace this with real logic (e.g., database lookup, API call)
    $sampleData = [
        'CRG-11-1234' => [
            'status' => 'In Transit',
            'location' => 'Nairobi, Kenya',
            'estimated_delivery' => '2024-04-05',
        ],
        'CRG-11-5678' => [
            'status' => 'Delivered',
            'location' => 'Lagos, Nigeria',
            'estimated_delivery' => '2024-03-30',
        ]
    ];

    return isset($sampleData[$trackingId]) ? $sampleData[$trackingId] : null;
}
?>