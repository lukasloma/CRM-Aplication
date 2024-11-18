<?php
session_start();


if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    header("Location: login.php");
    exit;
}


if (isset($_GET['nip']) && !empty($_GET['nip'])) {
    $nip = $_GET['nip'];

    
    require_once "connect_data.php";

    
    $connect = new mysqli($host, $user_name, $password, $db_name);

    
    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    
    $sql_delete = "DELETE FROM customers WHERE nip = ?";
    $stmt = $connect->prepare($sql_delete);
    $stmt->bind_param("s", $nip); 

    if ($stmt->execute()) {
        
        header("Location: index.php");
    } else {
        
        echo "Błąd usuwania klienta: " . $connect->error;
    }

    $stmt->close();
    $connect->close();
} else {
   
    echo "Brak NIP-u do usunięcia klienta.";
}
?>