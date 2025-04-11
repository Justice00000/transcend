// track.js - Client-side JavaScript for Tracking Functionality with Dynamic Progress Bar

class TrackingProgressBar {
    constructor() {
        // Status to percentage mapping
        this.statusPercentageMap = {
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
    }

    // Initialize progress bar elements
    initialize() {
        this.movingline = document.getElementById("movingline");
        this.pointerImg = document.getElementById("pointer_img");
        this.percCount = document.getElementById("perc_count");

        if (!this.movingline || !this.pointerImg || !this.percCount) {
            console.error("Progress bar elements not found");
            return null;
        }

        return this;
    }

    // Set progress to a specific percentage
    setProgress(percentage) {
        // Ensure percentage is between 0 and 100
        const clampedPercentage = Math.max(0, Math.min(100, percentage));

        this.movingline.style.width = `${clampedPercentage}%`;
        this.pointerImg.style.left = `${clampedPercentage}%`;
        this.percCount.innerText = `${Math.round(clampedPercentage)}%`;

        return this;
    }

    // Animate progress to a target percentage
    animateTo(targetPercentage, duration = 1000) {
        const startPercentage = parseFloat(this.movingline.style.width) || 0;
        const startTime = performance.now();

        const animate = (currentTime) => {
            const elapsedTime = currentTime - startTime;
            const progress = Math.min(elapsedTime / duration, 1);

            // Ease in-out interpolation
            const easedProgress = progress < 0.5 
                ? 4 * progress * progress * progress 
                : 1 - Math.pow(-2 * progress + 2, 3) / 2;

            const currentPercentage = startPercentage + 
                (targetPercentage - startPercentage) * easedProgress;

            this.setProgress(currentPercentage);

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
        return this;
    }

    // Get percentage for a specific status
    getPercentageForStatus(status) {
        return this.statusPercentageMap[status] || 0;
    }

    // Add or update a custom status
    addCustomStatus(status, percentage) {
        this.statusPercentageMap[status] = percentage;
        return this;
    }

    // Increment progress
    increment(amount = 10) {
        const currentPercentage = parseFloat(this.movingline.style.width) || 0;
        return this.setProgress(Math.min(currentPercentage + amount, 100));
    }

    // Decrement progress
    decrement(amount = 10) {
        const currentPercentage = parseFloat(this.movingline.style.width) || 0;
        return this.setProgress(Math.max(currentPercentage - amount, 0));
    }
}

// Main tracking functionality
document.addEventListener("DOMContentLoaded", function() {
    // Global progress bar instance
    window.trackingProgressBar = null;

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
        
        // Fetch tracking information
        fetch('https://serverside-yugv.onrender.com/api/track', options)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data);
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
                console.error('Full error:', error);
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
        // Create the HTML for the tracking result
        let resultHTML = buildTrackingResultHTML(data);
        
        // Display the tracking result
        displayTrackingResult(resultHTML);
        
        // Initialize and animate progress bar
        window.trackingProgressBar = new TrackingProgressBar().initialize();
        if (window.trackingProgressBar) {
            window.trackingProgressBar
                .setProgress(0)  // Start at 0
                .animateTo(calculateProgressPercentage(data.status));
        }
    }
    
    // Function to build tracking result HTML
    function buildTrackingResultHTML(data) {
        // Format the estimated delivery date if it exists
        let estimatedDelivery = formatEstimatedDelivery(data.estimated_delivery);
        
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
            resultHTML += buildAdditionalInfoHTML(data.additional_info);
        }
        
        // Add progress bar and status message
        resultHTML += `
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
        
        return resultHTML;
    }
    
    // Function to build additional info HTML
    function buildAdditionalInfoHTML(info) {
        return `
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
    
    // Function to display tracking result
    function displayTrackingResult(message) {
        // Get the container with the specific ID
        const resultElement = document.getElementById('tracking-result-container');
        if (resultElement) {
            // Set the HTML content
            resultElement.innerHTML = message;
        }
    }
    
    // Function to format estimated delivery date
    function formatEstimatedDelivery(estimatedDelivery) {
        if (!estimatedDelivery) return null;
        
        try {
            // Check if it's already formatted
            if (!estimatedDelivery.includes('/')) {
                const date = new Date(estimatedDelivery);
                return date.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
            }
            return estimatedDelivery;
        } catch (e) {
            return estimatedDelivery;
        }
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

// Expose methods for manual progress bar control
// You can now use these in the browser console or attach to buttons
/*
Example usage:
- window.trackingProgressBar.setProgress(50)  // Set to 50%
- window.trackingProgressBar.increment()      // Increment by 10%
- window.trackingProgressBar.decrement()      // Decrement by 10%
- window.trackingProgressBar.animateTo(75)    // Animate to 75%
- window.trackingProgressBar.addCustomStatus('New Status', 55)  // Add a custom status
*/