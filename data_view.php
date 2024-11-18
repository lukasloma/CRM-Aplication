<?php

require_once "connect_data.php";

$connect = new mysqli($host, $user_name, $password, $db_name);
$sql_customer = "SELECT `name`, `nip`, `email`, `phone`, `address` FROM customers";
$result = $connect->query($sql_customer);

if ($result->num_rows > 0) {
    $rows = '';
    while ($row=$result->fetch_assoc()){
        $rows .= '<tr>
                    <td>' . htmlspecialchars($row['name']) . '</td>
                    <td>' . htmlspecialchars($row['nip']) . '</td>
                    <td>' . htmlspecialchars($row['email']) . '</td>
                    <td>' . htmlspecialchars($row['phone']) . '</td>
                    <td>' . htmlspecialchars($row['address']) . '</td>
                  </tr>';
    }
    }else {
        $rows = '<tr><td colspan="5">Brak danych</td></tr>';
}
$connect->close();
?>