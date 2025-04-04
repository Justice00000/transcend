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
	<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/66ad0e9432dca6db2cb95616/1i4a021tm';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
</head>

<body class="hidden-bar-wrapper">

<div class="page-wrapper">
 	
    <!-- Preloader -->
    <div class="preloader"></div>
 	
 	<!-- Main Header / Header Style Two -->
    <header class="main-header header-style-two">
		<div class="auto-container">
			<div class="header-inner">

			</div>
        </div>
    
    </header>
    <!--End Main Header -->
    
	
	<!--Page Title-->
    <section class="page-title" style="background-image:url(https://getfastlogistics.com/images/background/12.jpg);">
    	<div class="auto-container">
        	<h2>Track & Trace</h2>
			<div class="separater"></div>
        </div>
    </section>
    
    <!--Breadcrumb-->
    <div class="breadcrumb-outer">
    	<div class="auto-container">
        	<ul class="bread-crumb text-center">
            	<li><a href="https://platinumlinkslogitics.com">Home</a> <span>/</span></li>
                <li>Track & Trace</li>
            </ul>
        </div>
    </div>
    <!--End Page Title-->
	
	<!--Sidebar Page Container-->
    <div class="sidebar-page-container">
    	<div class="auto-container">
        	<div class="row clearfix">
				
				<!--Content Side-->
                <div class="content-side col-lg-12 col-md-12 col-sm-12">
                	<div class="track-section">
						<!-- Sec Title Two -->
						<div class="sec-title-two sec-title">
							<h2>Track & <span style="color:#ffad2b;">Trace Shipment</span></h2>
							<div class="separater"></div>
						</div>
						<div class="track-form-two">
							<form method="post" action="track.php">
								<div class="form-group">
									<label>Enter Tracking Number Here</label>
								</div>
								<div class="form-group">
									<input type="text" name="trackId" placeholder="Enter your tracking number e.g CRG-11-XXXX">
									<button type="submit" name="trackingsub" class="theme-btn submit-btn" style="background:#ffad2b;">Track Your Shipment</button>
								</div>
							</form>
						</div>
<p>Tracking ID not submitted.</p>
	Please enter a tracking ID to view shipment details.

			   
					</div>
				</div>



						
		
					</div>
				</div>
				
				
			</div>
		</div>
	</div>

<style>
#trackline_wrap {
    width: 100%;
    position: relative;
    margin: 20px 0;
}

#grey_line {
    width: 100%;
    height: 10px;
    background-color: #e0e0e0;
    position: relative;
    border-radius: 5px;
}

#startpoint, #endpoint {
    width: 20px;
    height: 20px;
    background-color: #ffad2b;
    position: absolute;
    top: -5px;
    border-radius: 50%;
}

#startpoint {
    left: 0;
}

#endpoint {
    right: 0;
}

#movingline {
    height: 100%;
    background-color: #ffad2b;
    border-radius: 5px 0 0 5px;
}

#pointer_img {
    position: absolute;
    top: -15px;
    transform: translateX(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
}

#pointer_img img {
    width: 30px;
    height: 30px;
}

#perc_count {
    background-color: #ffad2b;
    color: white;
    padding: 3px 6px;
    border-radius: 3px;
    margin-top: 5px;
}
</style>

	
	<!--Main Footer-->
	<!--Main Footer-->
    
		
		<!-- Footer Bottom -->
		<div class="footer-bottom">
			<div class="copyright"> &copy; 2024 / ALL RIGHTS RESERVED</div>
		</div>
		
	</footer>
	
</div>
<!--End pagewrapper-->

<script>
    document.addEventListener("DOMContentLoaded", function() {
    const targetPercentage = ;
    const movingline = document.getElementById("movingline");
    const pointerImg = document.getElementById("pointer_img");
    const percCount = document.getElementById("perc_count");

    let currentPercentage = 0;
    const interval = setInterval(() => {
        if (currentPercentage <= targetPercentage) {
            movingline.style.width = currentPercentage + "%";
            pointerImg.style.left = currentPercentage + "%";
            percCount.innerText = currentPercentage + "%";
            currentPercentage++;
        } else {
            clearInterval(interval);
        }
    }, 10); // Adjust the speed of the animation by changing the interval time
});

</script>

<!--Scroll to top-->
<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-arrow-up"></span></div>

<script src="js/jquery.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="js/jquery.fancybox.js"></script>
<script src="js/appear.js"></script>
<script src="js/owl.js"></script>
<script src="js/wow.js"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/script.js"></script>

</body>

<!-- Mirrored from nauthemes.net/html/Crest Courier/ by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 30 Mar 2023 06:23:48 GMT -->
</html>