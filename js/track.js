// track.js - Client-side JavaScript for tracking functionality
document.addEventListener("DOMContentLoaded", function() {
    // Get the form element
    const trackForm = document.getElementById('tracking-form');
    
    // Add event listener for form submission
    if (trackForm) {
        trackForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            // Get the tracking ID input
            const trackingInput = this.querySelector('input[name="trackId"]');
            const trackingId = trackingInput.value.trim();
            
            // Validate tracking ID
            if (!trackingId) {
                displayTrackingResult("Please enter a tracking number.");
                return;
            }
            
            // Show loading indicator
            displayTrackingResult(`
                <div class="loading-spinner"></div>
                <p class="text-center">Searching for your shipment...</p>
            `);
            
            // Make the fetch request to the Node.js backend API
            fetchTrackingInfo(trackingId);
        });
    }
    
    // Function to fetch tracking information
    function fetchTrackingInfo(trackingId) {
        // Create the request options
        const options = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ trackId: trackingId })
        };
        
        // Change this line to use the correct API endpoint
        fetch('https://serverside-yugv.onrender.com/api/track', options)
            .then(response => {
                console.log('Response status:', response.status); // Add logging
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data); // Add logging
                if (data.found) {
                    // Display the tracking information with progress bar
                    displayTrackingSuccess(data);
                } else {
                    // Display not found message
                    displayTrackingResult(`
                        <div class="alert alert-danger">
                            <p>Tracking number not found in our system.</p>
                            <p>Please check the number and try again.</p>
                        </div>
                    `);
                }
            })
            .catch(error => {
                // Handle error
                console.error('Full error:', error); // More detailed error logging
                displayTrackingResult(`
                    <div class="alert alert-danger">
                        <p>Error connecting to tracking service:</p>
                        <p>${error.message}</p>
                    </div>
                `);
            });
    }
    
    // Function to display tracking success information with progress bar
    function displayTrackingSuccess(data) {
        // Calculate progress percentage based on status
        let progressPercentage = calculateProgressPercentage(data.status);
        
        // Format the estimated delivery date if it exists
        let estimatedDelivery = data.estimated_delivery;
        if (estimatedDelivery) {
            try {
                // Check if it's already formatted
                if (!estimatedDelivery.includes('/')) {
                    const date = new Date(estimatedDelivery);
                    estimatedDelivery = date.toLocaleDateString('en-US', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                }
            } catch (e) {
                // Keep original format if parsing fails
            }
        }
        
        // Create the HTML for the tracking result
        let resultHTML = `
            <div class="tracking-result">
                <h3>Shipment Information</h3>
                <div class="shipment-details">
                    <p><strong>Tracking Number:</strong> ${data.tracking_number}</p>
                    <p><strong>Status:</strong> ${data.status}</p>
                    ${data.origin ? `<p><strong>Origin:</strong> ${data.origin}</p>` : ''}
                    ${data.destination ? `<p><strong>Destination:</strong> ${data.destination}</p>` : ''}
                    ${estimatedDelivery ? `<p><strong>Estimated Delivery:</strong> ${estimatedDelivery}</p>` : ''}
                `;
        
        // Add additional info if available
        if (data.additional_info) {
            const info = data.additional_info;
            resultHTML += `
                <hr>
                <h4>Detailed Information</h4>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Sender Details</h5>
                        ${info.sender_name ? `<p><strong>Name:</strong> ${info.sender_name}</p>` : ''}
                        ${info.sender_address ? `<p><strong>Address:</strong> ${info.sender_address}</p>` : ''}
                    </div>
                    <div class="col-md-6">
                        <h5>Receiver Details</h5>
                        ${info.receiver_name ? `<p><strong>Name:</strong> ${info.receiver_name}</p>` : ''}
                        ${info.receiver_address ? `<p><strong>Address:</strong> ${info.receiver_address}</p>` : ''}
                    </div>
                </div>
                
                <div class="package-details">
                    <h5>Package Details</h5>
                    <div class="row">
                        ${info.weight ? `<div class="col-md-4"><p><strong>Weight:</strong> ${info.weight}</p></div>` : ''}
                        ${info.quantity ? `<div class="col-md-4"><p><strong>Quantity:</strong> ${info.quantity}</p></div>` : ''}
                        ${info.shipment_mode ? `<div class="col-md-4"><p><strong>Shipment Mode:</strong> ${info.shipment_mode}</p></div>` : ''}
                        ${info.payment_mode ? `<div class="col-md-4"><p><strong>Payment Mode:</strong> ${info.payment_mode}</p></div>` : ''}
                        ${info.carrier ? `<div class="col-md-4"><p><strong>Carrier:</strong> ${info.carrier}</p></div>` : ''}
                        ${info.dispatch_date ? `<div class="col-md-4"><p><strong>Dispatch Date:</strong> ${info.dispatch_date}</p></div>` : ''}
                    </div>
                    ${info.package_desc ? `<p><strong>Package Description:</strong> ${info.package_desc}</p>` : ''}
                </div>
            `;
        }
        
        resultHTML += `
                </div>
                
                <div id="trackline_wrap">
                    <div id="grey_line">
                        <div id="startpoint"></div>
                        <div id="endpoint"></div>
                        <div id="movingline" style="width: 0%;"></div>
                        <div id="pointer_img" style="left: 0%;">
                            <img src="images/pointer.png" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMCIgaGVpZ2h0PSIzMCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiNmZmFkMmIiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cG9seWdvbiBwb2ludHM9IjMgMTEgMjIgMiAyMyAyMSAxMiAxMiAzIDExIj48L3BvbHlnb24+PC9zdmc+'" alt="Tracking Pointer">
                            <div id="perc_count">0%</div>
                        </div>
                    </div>
                </div>
                
                <div class="tracking-message">
                    <p>${getStatusMessage(data.status)}</p>
                </div>
            </div>
        `;
        
        // Display the tracking result
        displayTrackingResult(resultHTML);
        
        // Initialize the progress animation
        animateProgressBar(progressPercentage);
    }
    
    // Function to display tracking result
    function displayTrackingResult(message) {
        // Get the container with the specific ID
        const resultElement = document.getElementById('tracking-result-container');
        if (resultElement) {
            // Set the HTML content
            resultElement.innerHTML = message;
        }
    }
    
    // Function to animate the progress bar
    function animateProgressBar(targetPercentage) {
        const movingline = document.getElementById("movingline");
        const pointerImg = document.getElementById("pointer_img");
        const percCount = document.getElementById("perc_count");
        
        if (!movingline || !pointerImg || !percCount) return;
        
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
        }, 20); // Animation speed
    }
    
    // Function to calculate progress percentage based on status
    function calculateProgressPercentage(status) {
        const statusMap = {
            "Order Processing": 10,
            "Order Processed": 20,
            "Shipment Created": 30,
            "Package Received": 40,
            "In Transit": 60,
            "Customs Clearance": 70,
            "Arrived at Destination": 80,
            "Out for Delivery": 90,
            "Delivered": 100
        };
        
        return statusMap[status] || 0;
    }
    
    // Function to get status message based on status
    function getStatusMessage(status) {
        const messageMap = {
            "Order Processing": "Your order is being processed by our team.",
            "Order Processed": "Your order has been processed and is awaiting shipment.",
            "Shipment Created": "Your shipment has been created and is ready for pickup.",
            "Package Received": "We have received your package at our facility.",
            "In Transit": "Your package is in transit to its destination.",
            "Customs Clearance": "Your package is undergoing customs clearance.",
            "Arrived at Destination": "Your package has arrived at the destination facility.",
            "Out for Delivery": "Your package is out for delivery today.",
            "Delivered": "Your package has been delivered successfully."
        };
        
        return messageMap[status] || "Tracking status: " + status;
    }
});