<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chat_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle new message submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $user_id = 1; // This should be dynamically set based on the logged-in user
    $message = $conn->real_escape_string($_POST['message']);

    $sql = "INSERT INTO messages (user_id, message) VALUES ($user_id, '$message')";
    if ($conn->query($sql) === TRUE) {
        echo "New message created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch all messages
$sql = "SELECT users.username, messages.message, messages.created_at FROM messages JOIN users ON messages.user_id = users.id ORDER BY messages.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat Application</title>
    <style>
        #chat-box {
            width: 500px;
            height: 300px;
            border: 1px solid #ccc;
            overflow-y: scroll;
            margin-bottom: 10px;
        }
        .message {
            margin: 10px 0;
        }
        .username {
            font-weight: bold;
        }
        .timestamp {
            font-size: small;
            color: gray;
        }
    </style>
</head>
<body>
    <div id="chat-box">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="message">';
                echo '<span class="username">' . htmlspecialchars($row["username"]) . ':</span> ';
                echo '<span class="text">' . htmlspecialchars($row["message"]) . '</span><br>';
                echo '<span class="timestamp">' . $row["created_at"] . '</span>';
                echo '</div>';
            }
        } else {
            echo "No messages.";
        }
        ?>
    </div>

    <form method="POST" action="">
        <input type="text" name="message" placeholder="Type your message here..." required>
        <button type="submit">Send</button>
    </form>
</body>
</html>

<?php
$conn->close();
?>
