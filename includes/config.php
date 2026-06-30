<?php
// includes/config.php
date_default_timezone_set('Asia/Kolkata');

// Load the real secret keys from our hidden credentials file securely
require_once 'credentials.php'; 

// Store Settings (Safe to show on GitHub)
define('GST_RATE', 0.18); // 18%
define('DELIVERY_CHARGE', 50.00); // ₹50
?>