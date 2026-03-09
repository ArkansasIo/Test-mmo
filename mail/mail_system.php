<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mail_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle new email submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_mail'])) {
    $sender_id = (int)$_POST['sender_id'];
    $recipient_id = (int)$_POST['recipient_id'];
    $subject = $conn->real_escape_string($_POST['subject']);
    $body = $conn->real_escape_string($_POST['body']);

    $sql = "INSERT INTO mails (sender_id, recipient_id, subject, body) VALUES ($sender_id, $recipient_id, '$subject', '$body')";
    if ($conn->query($sql) === TRUE) {
        echo "Mail sent successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch user's received mails
$user_id = 1; // This should be dynamically set based on the logged-in user
$sql = "SELECT u.username as sender, m.subject, m.body, m.sent_at FROM mails m JOIN users u ON m.sender_id = u.id WHERE m.recipient_id = $user_id ORDER BY m.sent_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mail System</title>
    <style>
        #mail-box {
            width: 500px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }
        .mail {
            border-bottom: 1px solid #ccc;
            padding: 10px;
        }
        .subject {
            font-weight: bold;
        }
        .timestamp {
            font-size: small;
            color: gray;
        }
    </style>
</head>
<body>
    <h1>Inbox</h1>
    <div id="mail-box">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="mail">';
                echo '<div class="subject">' . htmlspecialchars($row["subject"]) . '</div>';
                echo '<div class="body">' . htmlspecialchars($row["body"]) . '</div>';
                echo '<div class="timestamp">From: ' . htmlspecialchars($row["sender"]) . ' at ' . $row["sent_at"] . '</div>';
                echo '</div>';
            }
        } else {
            echo "No mails.";
        }
        ?>
    </div>

    <h1>Send Mail</h1>
    <form method="POST" action="">
        <label for="sender_id">Sender ID:</label>
        <input type="text" name="sender_id" id="sender_id" required><br>
        <label for="recipient_id">Recipient ID:</label>
        <input type="text" name="recipient_id" id="recipient_id" required><br>
        <label for="subject">Subject:</label>
        <input type="text" name="subject" id="subject" required><br>
        <label for="body">Body:</label>
        <textarea name="body" id="body" required></textarea><br>
        <button type="submit" name="send_mail">Send</button>
    </form>
</body>
</html>

<?php
$conn->close();
?>
