<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'login_portal');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            header("Location: welcome.php");
            exit();
        } else {
            $message = "Mot de passe invalide. Veuillez réessayer.";
        }
    } else {
        $message = "Nom d'utilisateur invalide. Veuillez réessayer.";
    }
}

$sql_messages = "SELECT * FROM messages ORDER BY created_at DESC";
$result_messages = $conn->query($sql_messages);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Se connecter</h2>
        <form action="login.php" method="POST">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" name="username" required><br>
            <label for="password">Mot de passe:</label>
            <input type="password" name="password" required><br>
            <button type="submit">Connexion</button>
        </form>

        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <p>Pas encore de compte ? <a href="register.php">S'inscrire</a></p>

        <h3>Messages</h3>
        <div class="messages">
            <?php
            if ($result_messages->num_rows > 0) {
                while ($row = $result_messages->fetch_assoc()) {
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
