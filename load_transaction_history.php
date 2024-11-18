<?php
// Połączenie z bazą danych
require_once 'connect_data.php';

// Sprawdzamy, czy NIP klienta został przekazany w zapytaniu
if (isset($_GET['nip']) && !empty($_GET['nip'])) {
    $nip = $_GET['nip'];
    
    // Połączenie z bazą danych
    $connect = new mysqli($host, $user_name, $password, $db_name);
    
    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    // Pobieramy dane klienta na podstawie NIP
    $sql_customer = "SELECT name, nip, email, phone, address FROM customers WHERE nip = ?";
    $stmt = $connect->prepare($sql_customer);
    $stmt->bind_param("s", $nip);
    $stmt->execute();
    $customer_result = $stmt->get_result();

    // Jeśli klient istnieje
    if ($customer_result->num_rows > 0) {
        $customer = $customer_result->fetch_assoc();

        // Wyświetlamy dane klienta
        echo "<h3>Informacje o kliencie</h3>";
        echo "<p><strong>Nazwa:</strong> " . htmlspecialchars($customer['name']) . "</p>";
        echo "<p><strong>NIP:</strong> " . htmlspecialchars($customer['nip']) . "</p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($customer['email']) . "</p>";
        echo "<p><strong>Telefon:</strong> " . htmlspecialchars($customer['phone']) . "</p>";
        echo "<p><strong>Adres:</strong> " . htmlspecialchars($customer['address']) . "</p>";
        
        // Zapytanie do pobrania transakcji klienta
        $sql_purchases = "SELECT product, amount, price, invoice_number, transaction_date 
                          FROM purchases WHERE client_nip = ? ORDER BY transaction_date DESC";
        $stmt = $connect->prepare($sql_purchases);
        $stmt->bind_param("s", $nip);
        $stmt->execute();
        $purchases_result = $stmt->get_result();

        // Jeśli klient ma jakieś transakcje
        if ($purchases_result->num_rows > 0) {
            echo "<h3>Historia Transakcji</h3>";
            echo "<table border='1'>
                    <thead>
                        <tr>
                            <th>Produkt</th>
                            <th>Ilość</th>
                            <th>Cena</th>
                            <th>Wartość</th>
                            <th>Numer Faktury</th>
                            <th>Data Transakcji</th>
                        </tr>
                    </thead>
                    <tbody>";

            $total_spent = 0; // Zmienna do sumowania wydanych pieniędzy przez klienta

            while ($row = $purchases_result->fetch_assoc()) {
                // Obliczanie wartości transakcji
                $transaction_value = $row['amount'] * $row['price'];
                $total_spent += $transaction_value;

                echo "<tr>
                        <td>" . htmlspecialchars($row['product']) . "</td>
                        <td>" . htmlspecialchars($row['amount']) . "</td>
                        <td>" . htmlspecialchars($row['price']) . " PLN</td>
                        <td>" . number_format($transaction_value, 2) . " PLN</td>
                        <td>" . htmlspecialchars($row['invoice_number']) . "</td>
                        <td>" . htmlspecialchars($row['transaction_date']) . "</td>
                    </tr>";
            }

            echo "</tbody>
                  </table>";

            // Podsumowanie łącznej kwoty wydanej przez klienta
            echo "<p><strong>Łączna kwota wydana przez klienta: </strong>" . number_format($total_spent, 2) . " PLN</p>";
        } else {
            echo "<p>Brak transakcji dla tego klienta.</p>";
        }
    } else {
        echo "<p>Klient o podanym NIP nie istnieje w systemie.</p>";
    }

    $stmt->close();
    $connect->close();
} else {
    echo "<p>Nie podano NIP klienta.</p>";
}
?>