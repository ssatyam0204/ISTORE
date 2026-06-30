# iStore - Premium E-Commerce Platform

A secure, full-stack PHP e-commerce web application featuring a dynamic customer storefront, custom shopping cart mechanics, an automated invoice generation pipeline, and a robust administrative control panel. 

The project has been architected following modern industry-standard security practices, ensuring all critical business logic is completely isolated from sensitive third-party API credentials and account access codes.

---

## 🔒 Professional Architecture & Security Highlights

This repository highlights standard production-grade security implementations:
* **Decoupled Key Management:** All private integration parameters—such as the database strings, Razorpay financial hashes, and SMTP mail account access lines—have been entirely isolated into an un-tracked setup file (`includes/credentials.php`) hidden securely behind a global `.gitignore` shield. 
* **Fail-Safe Token Routines:** Customer password reset paths utilize cryptographically secure pseudo-random bytes (`bin2hex(random_bytes(50))`) bound to strict 1-hour expiration tags processed precisely in the local `Asia/Kolkata` time context to prevent infrastructure discrepancies.
* **Admin Verification Gateways:** The store backend handles administrator password recoveries via 6-digit session codes (OTP validation arrays) generated programmatically and cleared on an aggressive 10-minute expiry layout.
* **Database Defensive Layer:** Built with strict string escaping protocol arrays to neutralize potential SQL Injection vectors along with automated password data isolation using native cryptographic `bcrypt` hashing algorithms.

---

## 🛠️ Complete Tech Stack & Libraries

* **Core Language Architecture:** PHP (Procedural & Structural Backend Controllers)
* **User Interface Engine:** HTML5, CSS3, JavaScript (Vanilla DOM manipulation and dynamic element handling)
* **Database Relational Engine:** MySQL / MariaDB Relational Database Management System
* **Third-Party Integrations:**
  * **PHPMailer (v6.x):** Embedded SMTP engine configured to run securely via TLS protocols over port 587.
  * **Razorpay Web SDK:** Client-to-server transaction payment gateway handling automated checkouts.
  * **FPDF Library:** Programmatic PDF generation engine utilized for printing customer digital store receipts.

---

## 📁 Repository Directory Structure

```text
istore/
│
├── .gitignore                   <-- Master rule matrix blocking secrets from public tracking
├── index.php                    <-- Primary customer storefront portal landing index
├── login.php / register.php     <-- Core user account creation & identity management portals
├── forgot_password.php          <-- User-side password reset generation script
├── reset_password.php           <-- User-side token validation and database updating script
├── cart.php / checkout.php      <-- Shopping cart controller and order checkout pipelines
├── generate_invoice.php         <-- Dynamic invoice generation layout building the PDF download
│
├── includes/
│   ├── db_connect.php           <-- Global relational database connector instance
│   ├── config.php               <-- General site parameters (GST rates, delivery fees)
│   ├── credentials.example.php  <-- Publicly tracked template file outlining configuration bounds
│   └── footer.php               <-- Global template file managing structural page layouts
│
└── admin/
    ├── index.php                <-- Administrative entry portal and validation board
    ├── dashboard.php            <-- Main metric tracking area for managing store updates
    ├── forgot_password.php      <-- Admin-side multi-digit OTP generation manager
    └── reset_password.php       <-- Admin-side validation checkpoint handling OTP input matching

⚙️ Detailed Local Deployment & Configuration Guide
Follow these steps exactly to deploy and execute this project locally on your development server:
*1. Project Directory Preparation
Download this repository or clone it directly into your local machine's server path:
C:\xampp\htdocs\istore\

*2. Relational Schema Setup
1.Start your XAMPP Control Panel and activate both the Apache and MySQL modules.
2.Open your web browser and navigate to the database interface dashboard: http://localhost/phpmyadmin/
3.Click on New in the left-hand column and create a database named exactly: istore
4.Create your database tables or import your clean schema structural setup to match your local execution environment.

*3. Local Secrets Isolation Setup (Crucial Step)
Because critical environment credentials are intentionally absent from public tracking for asset security, you must build your configuration file manually:
1.Navigate into your project's configuration directory: includes/
2.Locate the file named credentials.example.php.
3.Create a direct copy of this file within the same includes/ folder and name the new file exactly: credentials.php
4.Open your newly created credentials.php file in your text editor and populate it with your specific system integration keys:
<?php
// includes/credentials.php

// 1. Enter your real Gmail address and generated 16-character App Password for SMTP mail to function
define('SMTP_EMAIL', 'your-actual-email@gmail.com');
define('SMTP_PASSWORD', 'your-16-character-app-password');

// 2. Enter your live/test Razorpay API keys for checkout processing
define('RAZORPAY_KEY_ID', 'rzp_test_your_key_id_here');
define('RAZORPAY_KEY_SECRET', 'your_key_secret_here');
?>

*4. How to Generate a Secure Gmail SMTP App Password
Standard Google email account passwords will fail authentication cycles via PHPMailer due to strict modern connection firewalls. You must explicitly configure an isolated application credential token:
1.Access your target web browser and open your Google Account Settings -> Go to the Security tab.
2.Verify that 2-Step Verification is fully active on your profile.
3.Use the search bar inside your Google Account page to search for: App Passwords
4.Select "Other (Custom name)" in the app choice prompt, name your credential block iStore Automated Mailer Engine, and click Generate.
5.Copy the 16-character authorization token code box that appears on your screen and paste it cleanly into the SMTP_PASSWORD position inside your local includes/credentials.php file.

*5. Running the Application Storefront
To access the live user-facing storefront layout, open your browser and navigate to:
http://localhost/istore/
To manage backend listings, adjust inventory rows, check customer complaints, and review metrics, navigate straight to the master administrative terminal path:
http://localhost/istore/admin/
