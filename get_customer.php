<?php
require_once "connect_data.php";

$response = ["success" => false];

if (isset($_GET['nip'])) {
    $nip = $_GET['nip'];

    $connect = new mysqli($host, $user_name, $password, $db_name);

    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    $sql = "SELECT name, nip, email, phone, address FROM customers WHERE nip = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("s", $nip);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response["customer"] = $result->fetch_assoc();
        $response["success"] = true;
    }

    $stmt->close();
    $connect->close();
}

header('Content-Type: application/json');
echo json_encode($response);
?>