<?php
require_once "connect_data.php"; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $nip = $_POST['nip'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $connect = new mysqli($host, $user_name, $password, $db_name);

    if ($connect->connect_error) {
        die("Błąd połączenia: " . $connect->connect_error);
    }

    
    $sql_update = "UPDATE customers SET name = ?, email = ?, phone = ?, address = ? WHERE nip = ?";
    $stmt = $connect->prepare($sql_update);
    $stmt->bind_param("sssss", $name, $email, $phone, $address, $nip);

    if ($stmt->execute()) {
        echo "Dane klienta zostały zaktualizowane.";
    } else {
        echo "Błąd przy aktualizacji danych: " . $stmt->error;
    }

    $stmt->close();
    $connect->close();

    
    header("Location: index.php");
    exit;
}
?>