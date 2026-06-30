# iStore - E-Commerce Platform

A secure, full-stack PHP e-commerce web application featuring a dynamic product catalog, custom shopping cart mechanics, a complete order management pipeline, and an integrated administrative administration panel.

---

## 🚀 Key Features

* **Secure Authentication System:** Implement token-based user password reset flows and time-constrained admin OTP verification systems designed with timezone-fail-safes (`Asia/Kolkata`).
* **Payment Gateway Integration:** Secure transaction processing using the Razorpay API, executing verified payment signature checks on the backend server before changing transaction status blocks.
* **Administrative Control Panel:** Dynamic analytics dashboard allowing administrative control to add/edit products, manage categories, handle complaints, track orders, and generate systemic invoices.
* **Security Optimizations:** Built with SQL injection protection via robust string escaping, password data protection through strict `bcrypt` hashing encryption standards, and a modular environment setup configuration.

---

## 🛠️ Tech Stack

* **Backend:** PHP (Native Object-Oriented Logic structures)
* **Frontend:** HTML5, CSS3, JavaScript (Vanilla DOM processing)
* **Database:** MySQL / MariaDB Relational Engine
* **Third-Party Libraries:** PHPMailer (SMTP configuration context), Razorpay API SDK Core, FPDF (Automated Invoice generation library)

---

## 🔒 Security & Installation Note

This repository follows industry-standard security principles. All sensitive API secret codes and authentication credentials have been entirely decoupled from the main repository logic using a structural `.gitignore` shield.

To run this project locally:
1. Clone this repository to your local machine inside `xampp/htdocs/`.
2. Locate the `includes/` directory.
3. Duplicate the `credentials.example.php` file and rename the new copy to `credentials.php`.
4. Populate your custom local values inside `credentials.php` (such as your specific Gmail SMTP configuration keys and your individual Razorpay API secret hashes).
5. Import the `SQL/istore.sql` schema into your local phpMyAdmin database server instance.
