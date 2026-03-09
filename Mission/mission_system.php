<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scifi_rts_mmorpg";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch missions for a specific level range
$level_start = 1; // This can be dynamically set based on player's level
$level_end = 50;

$sql = "SELECT * FROM missions WHERE level BETWEEN $level_start AND $level_end ORDER BY level ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mission Objectives</title>
    <style>
        .mission {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px 0;
        }
        .level {
            font-weight: bold;
        }
        .objective {
            font-size: large;
        }
        .description {
            margin-top: 5px;
        }
        .rewards {
            color: green;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h1>Mission Objectives (Levels 1-50)</h1>
    <div id="missions">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="mission">';
                echo '<div class="level">Level ' . htmlspecialchars($row["level"]) . '</div>';
                echo '<div class="objective">' . htmlspecialchars($row["objective"]) . '</div>';
                echo '<div class="description">' . htmlspecialchars($row["description"]) . '</div>';
                echo '<div class="rewards">Rewards: ' . htmlspecialchars($row["rewards"]) . '</div>';
                echo '</div>';
            }
        } else {
            echo "No missions found.";
        }
        ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
