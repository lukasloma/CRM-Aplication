<?php

require_once "connect_data.php";


$connect = new mysqli($host, $user_name, $password, $db_name);


if ($connect->connect_error != 0) {
    echo "Błąd połączenia: " . $connect->connect_error;
    exit();
} else {
    
    $name = $_POST['name'];
    $nip = preg_replace('/\D/', '', $_POST['nip']);  
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    
    $sql_customer = "INSERT INTO customers (name, nip, email, phone, address) 
                     VALUES (?, ?, ?, ?, ?)";

    
    if ($stmt = $connect->prepare($sql_customer)) {
        
        $stmt->bind_param("sssss", $name, $nip, $email, $phone, $address);

        
        if ($stmt->execute()) {
            
            header("Location: index.php");
            exit();  
        } else {
            echo "Błąd wykonania zapytania: " . $stmt->error;
        }

       
        $stmt->close();
    } else {
        echo "Błąd przygotowania zapytania: " . $connect->error;
    }

   
    $connect->close();
}

?>