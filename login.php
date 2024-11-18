<?php

require_once "connect_data.php"; 


$connect = new mysqli($host, $user_name, $password, $db_name);

if ($connect->connect_error != 0) {
    echo "Error: " . $connect->connect_error;
} else {

    
    if (isset($_POST['register'])) {
        
        $username = $_POST['username_register'];
        $password = $_POST['password_register'];
        $email = $_POST['email'];

        // Szyfrowanie hasła
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        
        $sql_check = "SELECT * FROM users WHERE username_register = ?";
        $stmt = $connect->prepare($sql_check);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Użytkownik o tej nazwie już istnieje!";
        } else {
            
            $sql_insert = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
            $stmt = $connect->prepare($sql_insert);
            $stmt->bind_param("sss", $username, $hashedPassword, $email);

            if ($stmt->execute()) {
                $success_message = "Rejestracja zakończona sukcesem! Możesz się teraz zalogować.";
            } else {
                $error_message = "Błąd rejestracji: " . $connect->error;
            }
        }

        $stmt->close();
    }

    // Logowanie użytkownika
    if (isset($_POST['login'])) {
        
        $username = $_POST['username'];
        $password = $_POST['password'];

        
        $sql = "SELECT * FROM users WHERE username_register = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            
            if (password_verify($password, $user['password_register'])) {
                
                session_start();
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $user['id']; 
                $_SESSION['username'] = $username;
                header("Location: index.php"); 
                exit;
            } else {
                $error_message = "Niepoprawne hasło";
            }
        } else {
            $error_message = "Użytkownik nie istnieje";
        }

        $stmt->close();
    }

    $connect->close();
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Logowanie / Rejestracja</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="login-container">
        <!-- Logowanie -->
        <form class="login-form" action="login.php" method="POST">
            <h2>Logowanie</h2>
            <div class="form-group">
                <label for="username">Nazwa użytkownika</label>
                <input type="text" id="username" name="username" required />
            </div>
            <div class="form-group">
                <label for="password">Hasło</label>
                <input type="password" id="password" name="password" required />
            </div>
            <button type="submit" name="login">Zaloguj się</button>
            <!-- Opcjonalnie: Komunikat błędu -->
            <?php if (isset($error_message)) { ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php } ?>
            <!-- Link do rejestracji -->
            <p>Nie masz konta? <a href="#" onclick="showRegisterForm()">Zarejestruj się</a></p>
        </form>

        <!-- Rejestracja -->
        <form class="register-form" action="login.php" method="POST" style="display: none;">
            <h2>Rejestracja</h2>
            <div class="form-group">
                <label for="username_register">Nazwa użytkownika</label>
                <input type="text" id="username_register" name="username_register" required />
            </div>
            <div class="form-group">
                <label for="password_register">Hasło</label>
                <input type="password" id="password_register" name="password_register" required />
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required />
            </div>
            <button type="submit" name="register">Zarejestruj się</button>
            <?php if (isset($error_message)) { ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php } ?>
            <?php if (isset($success_message)) { ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php } ?>
            <p>Masz już konto? <a href="#" onclick="showLoginForm()">Zaloguj się</a></p>
        </form>
    </div>

    <script src="script.js">
       
    </script>
</body>
</html>