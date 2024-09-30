<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'login_portal');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['username']) && $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {
    $username = $_SESSION['username'];
    $message = htmlspecialchars($_POST['message']); // Échapper les caractères spéciaux

    $query = "INSERT INTO messages (username, message) VALUES ('$username', '$message')";
    
    if ($conn->query($query) === TRUE) {
    } else {
        echo "Erreur : " . $conn->error;
    }
}

$sql = "SELECT * FROM messages ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Bienvenue<?php if (isset($_SESSION['username'])) echo ', ' . htmlspecialchars($_SESSION['username']); ?></h2>
        
        <?php if (isset($_SESSION['username'])): ?>
            <form method="POST">
                <textarea name="message" placeholder="Écrivez votre message ici..." required></textarea><br>
                <button type="submit">Envoyer</button>
            </form>
            <p><a href="logout.php">Se déconnecter</a></p>
        <?php else: ?>
            <p>Connectez-vous pour envoyer un message.</p>
        <?php endif; ?>

        <h3>Messages</h3>
        <div class="messages">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<p><strong>" . htmlspecialchars($row['username']) . ":</strong> " . htmlspecialchars($row['message']) . " <em>(" . $row['created_at'] . ")</em></p>";
                }
            } else {
                echo "<p>Aucun message à afficher.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
