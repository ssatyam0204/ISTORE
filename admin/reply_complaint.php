<?php
require 'includes/header.php';
require '../includes/mailer_config.php';

if (!isset($_GET['id'])) {
    header('location: manage_complaints.php');
    exit();
}
$complaint_id = intval($_GET['id']);
$message = '';

$complaint_query = "SELECT c.*, u.name as user_name, u.email as user_email 
                    FROM complaints c 
                    JOIN users u ON c.user_id = u.id 
                    WHERE c.id = $complaint_id";
$complaint_result = mysqli_query($conn, $complaint_query);
$complaint = mysqli_fetch_assoc($complaint_result);

if (isset($_POST['send_reply'])) {
    $reply_subject = mysqli_real_escape_string($conn, $_POST['reply_subject']);
    $reply_message = nl2br(mysqli_real_escape_string($conn, $_POST['reply_message']));

    $mail = get_mailer();
    try {
        $mail->addAddress($complaint['user_email'], $complaint['user_name']);
        $mail->isHTML(true);
        $mail->Subject = $reply_subject;
        $mail->Body    = "Hello " . $complaint['user_name'] . ",<br><br>This is a reply regarding your complaint ticket #" . $complaint_id . ".<br><br><hr>" . $reply_message . "<hr><br>Thank you,<br>iStore Support";
        $mail->send();
        $message = "Reply sent successfully to " . $complaint['user_email'];
    } catch (Exception $e) {
        $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
<h2>Reply to Complaint #<?php echo $complaint_id; ?></h2>
<div class="details-card">
    <p><strong>User:</strong> <?php echo htmlspecialchars($complaint['user_name']); ?></p>
    <p><strong>Subject:</strong> <?php echo htmlspecialchars($complaint['subject']); ?></p>
    <p><strong>Original Message:</strong> <?php echo htmlspecialchars($complaint['message']); ?></p>
</div>

<form action="reply_complaint.php?id=<?php echo $complaint_id; ?>" method="POST" class="profile-form" style="margin-top:20px;">
    <?php if ($message): ?><p class="form-message"><?php echo $message; ?></p><?php endif; ?>
    <label for="reply_subject">Email Subject:</label>
    <input type="text" name="reply_subject" value="Re: Complaint Ticket #<?php echo $complaint_id; ?>" required>
    <label for="reply_message">Your Reply:</label>
    <textarea name="reply_message" rows="8" required></textarea>
    <button type="submit" name="send_reply">Send Reply</button>
</form>

<?php require 'includes/footer.php'; ?>