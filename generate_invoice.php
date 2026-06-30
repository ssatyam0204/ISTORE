<?php
session_start();
require 'includes/db_connect.php';
require 'includes/config.php';
require 'lib/fpdf/fpdf.php';

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}
if (!isset($_GET['id'])) {
    header('location: my_orders.php');
    exit();
}

$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$order_query = "SELECT o.*, u.name as customer_name, a.address_line, a.city, a.pincode, a.state 
                FROM orders o
                JOIN users u ON o.user_id = u.id 
                JOIN addresses a ON o.address_id = a.id 
                WHERE o.id = $order_id AND o.user_id = $user_id";
$order_result = mysqli_query($conn, $order_query);

if(mysqli_num_rows($order_result) == 0) {
    echo "Order not found.";
    exit();
}
$order = mysqli_fetch_assoc($order_result);

$items_query = "SELECT oi.quantity, oi.price, p.name as product_name 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);

class PDF extends FPDF {
    function Header() {
        // Logo
        if(file_exists('assets/logo.png')) {
            $this->Image('assets/logo.png',10,10,30);
        }
        // Store Info
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,'iStore Pvt. Ltd.',0,1,'R');
        $this->SetFont('Arial','',10);
        $this->Cell(0,6,'Mumbai, Maharashtra, India',0,1,'R');
        $this->Cell(0,6,"Phone: +91 99999 99999 | Email: support@istore.com");
        $this->Ln(10);

        // Title
        $this->SetFont('Arial','B',18);
        $this->Cell(0,12,'INVOICE',0,1,'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-70);

        // Thank You Message
        $this->SetFont('Arial','B',12);
        $this->SetTextColor(50,50,50);
        $this->Cell(0,8,'Thank you for shopping with iStore!',0,1,'C');
        $this->Ln(2);

        // Terms & Conditions
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);
        $this->Cell(0,8,'Terms & Conditions',0,1,'L');
        $this->SetFont('Arial','',8);
        
        $policy_text = "7-Day Return & Refund Policy: Products are eligible for return within 7 days from the date of delivery. Item must be unused, unopened, with all original packaging, tags, and accessories intact. Refunds are processed within 5-7 business days after inspection.";
        $this->MultiCell(0,4, $policy_text, 0, 'L');
        $this->Ln(2);

        $warranty_text = "Warranty: All Apple products sold on iStore come with official Apple India Warranty, activated from invoice date. This invoice is proof of purchase for all warranty claims.";
        $this->MultiCell(0,4, $warranty_text, 0, 'L');

        // Page number
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();

// Customer & Order Info
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,7,'Shipping Address',0,1);
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,6,htmlspecialchars($order['customer_name']),0,1);
$pdf->MultiCell(0,6,htmlspecialchars($order['address_line']).", ".htmlspecialchars($order['city']).", ".htmlspecialchars($order['state'])." - ".htmlspecialchars($order['pincode']));
$pdf->Ln(5);

// Order Details (Right side)
$pdf->SetX(120);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(40,7,'Order ID:',0,0,'L');
$pdf->SetFont('Arial','',11);
$pdf->Cell(40,7,'#'.$order['id'],0,1,'L');

$pdf->SetX(120);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(40,7,'Order Date:',0,0,'L');
$pdf->SetFont('Arial','',11);
$pdf->Cell(40,7,date('d M Y', strtotime($order['order_date'])),0,1,'L');

$pdf->SetX(120);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(40,7,'Payment Method:',0,0,'L');
$pdf->SetFont('Arial','',11);
$pdf->Cell(40,7,htmlspecialchars($order['payment_method'] ?? 'Online'),0,1,'L');
$pdf->Ln(10);

// Products Table
$pdf->SetFillColor(230,230,230);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(95,10,'Product',1,0,'C',true);
$pdf->Cell(30,10,'Quantity',1,0,'C',true);
$pdf->Cell(30,10,'Price',1,0,'C',true);
$pdf->Cell(35,10,'Total',1,1,'C',true);

$pdf->SetFont('Arial','',11);
$subtotal = 0;
$fill = false;
while($item = mysqli_fetch_assoc($items_result)) {
    $item_total = $item['price'] * $item['quantity'];
    $subtotal += $item_total;
    $pdf->Cell(95,10,htmlspecialchars($item['product_name']),1,0,'L',$fill);
    $pdf->Cell(30,10,$item['quantity'],1,0,'C',$fill);
    $pdf->Cell(30,10,number_format($item['price'], 2),1,0,'R',$fill);
    $pdf->Cell(35,10,number_format($item_total, 2),1,1,'R',$fill);
    $fill = !$fill;
}

// Totals Section
$pdf->Ln(5);
$pdf->SetX(110);
$pdf->SetFont('Arial','',11);
$pdf->Cell(50,8,'Subtotal:',1,0,'R');
$pdf->Cell(35,8,number_format($subtotal, 2),1,1,'R');

$pdf->SetX(110);
$pdf->Cell(50,8,'Delivery:',1,0,'R');
$pdf->Cell(35,8,number_format(DELIVERY_CHARGE, 2),1,1,'R');

$pdf->SetX(110);
$pdf->SetFont('Arial','B',12);
$pdf->SetFillColor(240,240,240);
$pdf->Cell(50,10,'Grand Total:',1,0,'R',true);
$pdf->Cell(35,10,'Rs. ' . number_format($order['total_amount'], 2),1,1,'R',true);

$pdf->Output('D', 'iStore-Invoice-'.$order_id.'.pdf');
?>
