<?php
require_once "connect_data.php"; 

if (isset($_GET['nip']) && !empty($_GET['nip'])) {
    $nip = $_GET['nip'];

    
    $connect = new mysqli($host, $user_name, $password, $db_name);
    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    
    $sql = "SELECT p.transaction_date, p.invoice_number, p.product, p.amount, p.price 
            FROM purchases p 
            WHERE p.client_nip = ? 
            ORDER BY p.transaction_date DESC";
    
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("s", $nip); 
    $stmt->execute();
    $result = $stmt->get_result();

    
    if ($result->num_rows > 0) {
        echo "<h3>Historia transakcji klienta o NIP: $nip</h3>";
        echo "<table>
                <thead>
                    <tr>
                        <th>Data transakcji</th>
                        <th>Numer faktury</th>
                        <th>Produkt</th>
                        <th>Ilość</th>
                        <th>Cena</th>
                    </tr>
                </thead>
                <tbody>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['transaction_date']) . "</td>
                    <td>" . htmlspecialchars($row['invoice_number']) . "</td>
                    <td>" . htmlspecialchars($row['product']) . "</td>
                    <td>" . htmlspecialchars($row['amount']) . "</td>
                    <td>" . number_format($row['price'], 2, ',', ' ') . " PLN</td>
                </tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "Brak transakcji dla tego klienta.";
    }

    $stmt->close();
    $connect->close();
}
?>