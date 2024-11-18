<?php

session_start(); 


if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    header("Location: login.php"); 
    exit;
}

require_once "connect_data.php"; 

$rows = ''; 


if (isset($_GET['nip']) && !empty($_GET['nip'])) {
    $nip = $_GET['nip'];
    
    
    $connect = new mysqli($host, $user_name, $password, $db_name);
    
    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    
    $sql_customer = "SELECT name, nip, email, phone, address FROM customers WHERE nip = ?";
    $stmt = $connect->prepare($sql_customer);
    $stmt->bind_param("s", $nip);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rows .= '<tr class="table-row" data-nip="' . htmlspecialchars($row['nip']) . '">
            <td>' . htmlspecialchars($row['name']) . '</td>
            <td>' . htmlspecialchars($row['nip']) . '</td>
            <td>' . htmlspecialchars($row['email']) . '</td>
            <td>' . htmlspecialchars($row['phone']) . '</td>
            <td>' . htmlspecialchars($row['address']) . '</td>
            <td class="action-buttons hidden">
                <button onclick="editClient(\'' . htmlspecialchars($row['nip']) . '\')">Edytuj</button>
                <button onclick="showTransactionHistoryAJAX(\'' . htmlspecialchars($row['nip']) . '\')">Historia transakcji</button>
                <button onclick="deleteClient(\'' . htmlspecialchars($row['nip']) . '\')">Usuń</button>
                
            </td>
          </tr>';
        }
    } else {
        $rows = '<tr><td colspan="5">Brak danych dla podanego NIP</td></tr>';
    }

    $stmt->close();
    $connect->close();
} else {
    
    $connect = new mysqli($host, $user_name, $password, $db_name);
    
    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    $sql_customer = "SELECT name, nip, email, phone, address FROM customers";
    $result = $connect->query($sql_customer);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rows .= '<tr class="table-row" data-nip="' . htmlspecialchars($row['nip']) . '">
            <td>' . htmlspecialchars($row['name']) . '</td>
            <td>' . htmlspecialchars($row['nip']) . '</td>
            <td>' . htmlspecialchars($row['email']) . '</td>
            <td>' . htmlspecialchars($row['phone']) . '</td>
            <td>' . htmlspecialchars($row['address']) . '</td>
            <td class="action-buttons hidden">
                <button onclick="editClient(\'' . htmlspecialchars($row['nip']) . '\')">Edytuj</button>
                <button onclick="showTransactionHistoryAJAX(\'' . htmlspecialchars($row['nip']) . '\')">Historia transakcji</button>
                <button onclick="deleteClient(\'' . htmlspecialchars($row['nip']) . '\')">Usuń</button>
            </td>
          </tr>';
        }
    } else {
        $rows = '<tr><td colspan="5">Brak danych</td></tr>';
    }

    $connect->close();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CRM - Zarządzanie Klientami i Transakcjami</title>
    <link rel="stylesheet" href="style.css" />
    
</head>
<body>
    <header>
        <h1>CRM</h1>
        
        <a href="logout.php">Wyloguj się</a> 
    </header>
    <div class="sidebar">
        <h2>CRM</h2>
        <ul>
            <li>
                <a href="#" onclick="openModal('clientModal')">Dodaj Klienta</a>
            </li>
            <li>
                <a href="#" onclick="openModal('transactionModal')">Dodaj Transakcję</a>
            </li>
            <li><a href="index.php">Baza Danych</a></li>
        </ul>
    </div>
    <div class="content">
        <header>
            <h1>CRM</h1>
            <form id="search-form" class="search-form" method="GET" action="index.php" onsubmit="cleanNipInput()">
                <input
                    type="text"
                    id="search-input"
                    name="nip"
                    placeholder="Szukaj po NIP..."
                    aria-label="Szukaj klientów"
                />
                <button type="submit">Szukaj</button>
                <input
                    type="text"
                    id="search-name"
                    placeholder="Szukaj po nazwie..."
                    aria-label="Szukaj klientów po nazwie"
                    oninput="filterClientsByName()" 
                />
                
                <button type="submit">Szukaj</button>
            </form>    
        </header>

        
        <section id="database-section" class="hidden">
            <h2>Baza Danych</h2>
            <table>
                <thead>
                    <tr>
                        <th>Pełna nazwa</th>
                        <th>NIP</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Adres</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    
                    echo $rows; 
                ?>
                </tbody>
            </table>
        </section>
    </div>

    <!-- Modal dodawania klienta -->
<div id="clientModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('clientModal')">&times;</span>
        <form method="POST" action="add_customer.php" id="client-form" class="form-container">
            <h2>Dodaj Klienta</h2>
            <div class="form-group">
                <label for="name">Pełna nazwa</label>
                <input type="text" id="name" placeholder="Nazwa firmy lub klienta" name="name" required />
            </div>
            <div class="form-group">
                <label for="nip">NIP</label>
                <input type="text" id="nip" placeholder="123-456-78-90" name="nip" required />
            </div>
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" placeholder="example@example.com" name="email" required />
            </div>
            <div class="form-group">
                <label for="phone">Telefon</label>
                <input type="tel" id="phone" placeholder="+48 123 456 789" name="phone" required />
            </div>
            <div class="form-group">
                <label for="address">Adres</label>
                <input type="text" id="address" placeholder="Adres" name="address" required />
            </div>
            <button type="submit" class="submit-btn" name="add_client">Zapisz Klienta</button>
        </form>
    </div>
</div>

<!-- Modal dodawania transakcji -->
<div id="transactionModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('transactionModal')">&times;</span>
        <form id="transaction-form" class="form-container" action="add_purchase.php" method="POST">
            <h2>Dodaj Transakcję</h2>
            <div class="form-group">
                <label for="client-nip">NIP Klienta</label>
                <input type="text" id="client-nip" placeholder="123-456-78-90" name="client_nip" required />
            </div>
            <div class="form-group">
                <label for="invoice_number">Numer faktury</label>
                <input type="text" id="invoice_number" name="invoice_number" required />
            </div>
            <div class="form-group">
                <label for="transaction-date">Data transakcji</label>
                <input type="date" id="transaction-date" name="transaction_date" required />
            </div>
            <div class="form-group">
                <label for="product">Produkt</label>
                <input type="text" id="product" name="product" placeholder="Nazwa produktu" required />
            </div>
            <div class="form-group">
                <label for="amount">Ilość</label>
                <input type="number" id="amount" name="amount" placeholder="Ilość / sztuk" required />
            </div>    
            <div class="form-group">
                <label for="amount">Kwota</label>
                <input type="number" id="price" name="price" placeholder="Kwota transakcji - 0.00 PLN" step="0.01" required />
            </div>
            <button type="submit" class="submit-btn">Zapisz Transakcję</button>
        </form>
    </div>
</div>

<!-- Modal edytowania klienta -->
<div id="editClientModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editClientModal')">&times;</span>
        <form method="POST" action="update_customer.php" id="edit-client-form" class="form-container">
            <h2>Edytuj Klienta</h2>
            <div class="form-group">
                <label for="edit-name">Pełna nazwa</label>
                <input type="text" id="edit-name" name="name" required />
            </div>
            <div class="form-group">
                <label for="edit-nip">NIP</label>
                <input type="text" id="edit-nip" name="nip" required />
            </div>
            <div class="form-group">
                <label for="edit-email">E-mail</label>
                <input type="email" id="edit-email" name="email" required />
            </div>
            <div class="form-group">
                <label for="edit-phone">Telefon</label>
                <input type="tel" id="edit-phone" name="phone" required />
            </div>
            <div class="form-group">
                <label for="edit-address">Adres</label>
                <input type="text" id="edit-address" name="address" required />
            </div>
            <button type="submit" class="submit-btn">Zapisz Zmiany</button>
        </form>
    </div>
</div>

<!-- Modal dla historii transakcji -->
<div id="transaction-history-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('transaction-history-modal')">&times;</span>
        <h2>Historia Transakcji</h2>
        <div id="transaction-history-container"></div>
    </div>
</div>

    
    <script src="script.js"></script>
    <script>
        // Obsługa zdarzenia kliknięcia na wiersz tabeli
        document.querySelectorAll('.table-row').forEach(row => {
            row.addEventListener('click', () => {
                const actionButtons = row.querySelector('.action-buttons');
                actionButtons.classList.toggle('hidden');
            });
        });

        function cleanNipInput() {
            const nipInput = document.getElementById("search-input");
            nipInput.value = nipInput.value.replace(/\D/g, ''); 
        }
    </script>
    
</body>
</html>