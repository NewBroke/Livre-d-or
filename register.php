<?php

$conn = new mysqli('localhost', 'root', '', 'login_portal');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = ''; 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    

    $sql_check = "SELECT * FROM users WHERE username = '$username'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        $message = "Ce nom d'utilisateur existe déjà. Veuillez en choisir un autre.";
    } else {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: login.php");
            exit();
        } else {
            $message = "Erreur: " . $conn->error;
        }
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
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Créer un compte</h2>
        <form action="register.php" method="POST">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" name="username" required><br>
            <label for="password">Mot de passe:</label>
            <input type="password" name="password" required><br>
            <button type="submit">S'inscrire</button>
        </form>
        <p class="message"><?php echo $message; ?></p>
        <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>

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
