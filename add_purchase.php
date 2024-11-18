<?php

require_once "connect_data.php";


$connect = new mysqli($host, $user_name, $password, $db_name);


if ($connect->connect_error != 0) {
    echo "Błąd połączenia: " . $connect->connect_error;
    exit();
} else {
    
    $client_nip = preg_replace('/\D/', '', $_POST['client_nip']); 
    $product = $_POST['product'];
    $amount = $_POST['amount'];
    $invoice_number = $_POST['invoice_number'];
    $price = $_POST['price'];
    $transaction_date = $_POST['transaction_date'];

    
    $sql_check_customer = "SELECT id FROM customers WHERE nip = ?";
    
    if ($stmt_check = $connect->prepare($sql_check_customer)) {
        $stmt_check->bind_param("s", $client_nip);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        
        if ($stmt_check->num_rows == 0) {
            
            echo "<script>
                    alert('Klient o podanym NIPie nie istnieje. Zostaniesz przekierowany do strony głównej.');
                    window.location.href = 'index.php';
                  </script>";
        } else {
            
            $sql_transaction = "INSERT INTO purchases (client_nip, product, amount, invoice_number, price, transaction_date) 
                                VALUES (?, ?, ?, ?, ?, ?)";
            
            if ($stmt_transaction = $connect->prepare($sql_transaction)) {
                $stmt_transaction->bind_param("ssssds", $client_nip, $product, $amount, $invoice_number, $price, $transaction_date);
                
                if ($stmt_transaction->execute()) {
                   
                    echo "<script>
                            alert('Transakcja została dodana pomyślnie!');
                            window.location.href = 'index.php';
                          </script>";
                } else {
                    echo "<script>
                            alert('Błąd dodawania transakcji: " . $stmt_transaction->error . "');
                          </script>";
                }
                $stmt_transaction->close();
            } else {
                echo "<script>
                        alert('Błąd przygotowania zapytania do transakcji: " . $connect->error . "');
                      </script>";
            }
        }

        
        $stmt_check->close();
    } else {
        echo "<script>
                alert('Błąd przygotowania zapytania: " . $connect->error . "');
              </script>";
    }

    
    $connect->close();
}
?>