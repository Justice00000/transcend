<?php 
// Database configuration for PostgreSQL
$dsn = "pgsql:host=dpg-cvn925a4d50c73fv6m70-a;port=5432;dbname=admin_db_5jq5;user=admin_db_5jq5_user;password=zQ7Zey6xTtDtqT99fKgUepfsuEhCjIoZ";

try {
    $conn = new PDO($dsn);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Initialize tracking number with month and random number
$tracking_number = "CC-" . date("m") . "-" . rand(100000, 999999);

// Process form submission
if (isset($_POST['trackingsub'])) {
    try {
        // Sender information
        $sname = $_POST['sname'];
        $scontact = $_POST['scontact'];
        $smail = $_POST['smail'];
        $saddress = $_POST['saddress'];
        
        // Receiver information
        $rname = $_POST['rname'];
        $rcontact = $_POST['rcontact'];
        $rmail = $_POST['rmail'];
        $raddress = $_POST['raddress'];
        
        // Shipment information
        $status = $_POST['status'];
        $dispatchl = $_POST['dispatchl'];
        $carrier = $_POST['carrier'];
        $carrier_ref = $_POST['carrier_ref'];
        $weight = $_POST['weight'];
        $payment_mode = $_POST['payment_mode'];
        $dest = $_POST['dest'];
        $desc = $_POST['desc'];
        $dispatch_date = $_POST['dispatch'];
        $delivery_date = $_POST['delivery'];
        $ship_mode = $_POST['ship_mode'];
        $quantity = $_POST['quantity'];
        $delivery_time = $_POST['delivery_time'];
        
        // Handle file upload
        $image_path = "";
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['image']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                $upload_dir = "uploads/";
                
                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Generate unique filename
                $filename = $tracking_number . "_" . basename($_FILES['image']['name']);
                $target_file = $upload_dir . $filename;
                
                // Move the uploaded file
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_path = $target_file;
                } else {
                    $upload_error = "Error uploading file.";
                }
            } else {
                $upload_error = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
            }
        }
        
        // Current timestamp for creation date
        $created_at = date("Y-m-d H:i:s");
        
        // SQL query to insert tracking data using PDO prepared statement for PostgreSQL
        $stmt = $conn->prepare("INSERT INTO tracking_orders (
            tracking_number, 
            sender_name, 
            sender_contact, 
            sender_email, 
            sender_address, 
            receiver_name, 
            receiver_contact, 
            receiver_email, 
            receiver_address, 
            status, 
            dispatch_location, 
            carrier, 
            carrier_ref_no, 
            weight, 
            payment_mode, 
            destination, 
            package_desc, 
            dispatch_date, 
            delivery_date, 
            shipment_mode, 
            quantity, 
            delivery_time, 
            package_image, 
            created_at
        ) VALUES (
            :tracking_number, 
            :sname, 
            :scontact, 
            :smail, 
            :saddress, 
            :rname, 
            :rcontact, 
            :rmail, 
            :raddress, 
            :status, 
            :dispatchl, 
            :carrier, 
            :carrier_ref, 
            :weight, 
            :payment_mode, 
            :dest, 
            :desc, 
            :dispatch_date, 
            :delivery_date, 
            :ship_mode, 
            :quantity, 
            :delivery_time, 
            :image_path, 
            :created_at
        )");
        
        // Bind parameters
        $stmt->bindParam(':tracking_number', $tracking_number);
        $stmt->bindParam(':sname', $sname);
        $stmt->bindParam(':scontact', $scontact);
        $stmt->bindParam(':smail', $smail);
        $stmt->bindParam(':saddress', $saddress);
        $stmt->bindParam(':rname', $rname);
        $stmt->bindParam(':rcontact', $rcontact);
        $stmt->bindParam(':rmail', $rmail);
        $stmt->bindParam(':raddress', $raddress);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':dispatchl', $dispatchl);
        $stmt->bindParam(':carrier', $carrier);
        $stmt->bindParam(':carrier_ref', $carrier_ref);
        $stmt->bindParam(':weight', $weight);
        $stmt->bindParam(':payment_mode', $payment_mode);
        $stmt->bindParam(':dest', $dest);
        $stmt->bindParam(':desc', $desc);
        $stmt->bindParam(':dispatch_date', $dispatch_date);
        $stmt->bindParam(':delivery_date', $delivery_date);
        $stmt->bindParam(':ship_mode', $ship_mode);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':delivery_time', $delivery_time);
        $stmt->bindParam(':image_path', $image_path);
        $stmt->bindParam(':created_at', $created_at);
        
        // Execute query
        $stmt->execute();
        
        // Set success message
        $success_message = "Tracking added successfully with tracking number: " . $tracking_number;
        
        // Generate new tracking number for next entry
        $tracking_number = "CC-" . date("m") . "-" . rand(100000, 999999);
    } catch(PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Platinumlinkslogitics | Track</title>
    <!-- Stylesheets -->
    <link href="css1/bootstrap.css" rel="stylesheet">
    <link href="css1/style.css" rel="stylesheet">
    <link href="css1/responsive.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
    <link rel="icon" href="images/favicon.png" type="image/x-icon">
    <!-- Responsive -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" type="text/css" href="css/track.css">
    <!-- Start of Tawk.to Script -->
    <script type="text/javascript">
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function () {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/66ad0e9432dca6db2cb95616/1i4a021tm';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
    <!-- End of Tawk.to Script -->
</head>

<body class="hidden-bar-wrapper">

<div class="page-wrapper">
    <header class="main-header header-style-two">
        <div class="auto-container">
            <div class="header-inner"></div>
        </div>
    </header>

    <section class="page-title" style="background-image:url(https://getfastlogistics.com/images/background/12.jpg);">
        <div class="auto-container">
            <h2>Track & Trace</h2>
            <div class="separater"></div>
        </div>
    </section>
    
    <div class="breadcrumb-outer">
        <div class="auto-container">
            <ul class="bread-crumb text-center">
                <li><a href="https://platinumlinkslogitics.com">Home</a> <span>/</span></li>
                <li>Track & Trace</li>
            </ul>
        </div>
    </div>

    <div class="sidebar-page-container">
        <div class="auto-container">
            <div class="row clearfix">
                <div class="content-side col-lg-12 col-md-12 col-sm-12">
                    <div class="track-section">
                        <div class="sec-title-two sec-title">
                            <h2>Track & <span style="color:#ffad2b;">Trace Shipment</span></h2>
                            <div class="separater"></div>
                        </div>
                        <div class="track-form-two">
                            <form method="post" action="track.php" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Enter Tracking Number Here</label>
                                </div>
                                <div class="form-group">
                                    <input type="text" name="trackId" placeholder="Enter your tracking number e.g CRG-11-XXXX">
                                    <button type="submit" name="trackingsub" class="theme-btn submit-btn" style="background:#ffad2b;">Track Your Shipment</button>
                                </div>
                            </form>
                        </div>
                        <p>Tracking ID not submitted. Please enter a tracking ID to view shipment details.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js1/jquery.js"></script>
<script src="js1/popper.min.js"></script>
<script src="js1/bootstrap.min.js"></script>
<script src="js1/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="js1/jquery.fancybox.js"></script>
<script src="js1/appear.js"></script>
<script src="js1/owl.js"></script>
<script src="js1/wow.js"></script>
<script src="js1/jquery-ui.js"></script>
<script src="js1/script.js"></script>

</body>

</html>