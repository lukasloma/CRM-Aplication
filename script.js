document.addEventListener("DOMContentLoaded", function () {
  // Sprawdzamy dostępność elementów przed przypisaniem nasłuchiwaczy
  const searchForm = document.getElementById("search-form");
  const nipInput = document.getElementById("search-input");
  const searchNipInput = document.getElementById("search-nip");
  const searchNameInput = document.getElementById("search-name");

  // Dodaj nasłuchiwacze tylko, jeśli elementy istnieją
  if (searchForm && nipInput) {
    // Funkcja czyszcząca NIP przed wysłaniem formularza
    searchForm.addEventListener("submit", function (event) {
      nipInput.value = nipInput.value.replace(/\D/g, ''); 
    });
  }

  if (searchNipInput) {
    searchNipInput.addEventListener("input", () => {
      filterClientsByNIP();  
      filterClientsByName(); 
    });
  }

  if (searchNameInput) {
    searchNameInput.addEventListener("input", () => {
      filterClientsByNIP();  
      filterClientsByName(); 
    });
  }

  
});


function showDatabase() {
  const databaseSection = document.getElementById("database-section");
  if (databaseSection) {
    databaseSection.classList.toggle("hidden");
  }
}

function editClient(nip) {
  // Pobierz wiersz tabeli, który zawiera dane tego klienta
  const row = document.querySelector(`tr[data-nip="${nip}"]`);

  if (row) {
  
    const name = row.children[0].textContent;
    const email = row.children[2].textContent;
    const phone = row.children[3].textContent;
    const address = row.children[4].textContent;

    
    console.log("Próba uzupełnienia formularza edycji:");
    console.log("edit-name:", document.getElementById("edit-name"));
    console.log("edit-nip:", document.getElementById("edit-nip"));
    console.log("edit-email:", document.getElementById("edit-email"));
    console.log("edit-phone:", document.getElementById("edit-phone"));
    console.log("edit-address:", document.getElementById("edit-address"));

    if (document.getElementById("edit-name") && document.getElementById("edit-nip") &&
        document.getElementById("edit-email") && document.getElementById("edit-phone") &&
        document.getElementById("edit-address")) {
      
      
      document.getElementById("edit-name").value = name;
      document.getElementById("edit-nip").value = nip;
      document.getElementById("edit-email").value = email;
      document.getElementById("edit-phone").value = phone;
      document.getElementById("edit-address").value = address;

      
      openModal("editClientModal");
    } else {
      console.error("Formularz edycji nie został poprawnie załadowany.");
    }
  } else {
    console.error("Nie znaleziono klienta do edycji.");
  }
}

function filterClientsByName() {
  const searchValue = document.getElementById("search-name") ? document.getElementById("search-name").value.toLowerCase() : "";
  const tableRows = document.querySelectorAll("#database-section tbody .table-row");

  tableRows.forEach(row => {
    const nameCell = row.children[0].textContent.toLowerCase(); 

    if (nameCell.includes(searchValue)) {
      row.style.display = ""; 
    } else {
      row.style.display = "none"; 
    }
  });
}

function filterClientsByNIP() {
  const nipValue = document.getElementById("search-nip") ? document.getElementById("search-nip").value.replace(/\D/g, '') : ""; // Wyczyść wszystko oprócz cyfr
  const tableRows = document.querySelectorAll("#database-section tbody .table-row");

  tableRows.forEach(row => {
    const nipCell = row.children[1].textContent; 

    if (nipCell.includes(nipValue)) {
      row.style.display = ""; 
    } else {
      row.style.display = "none"; 
    }
  });
}

// Funkcja do przełączania formularza rejestracji
function showRegisterForm() {
  const loginForm = document.querySelector('.login-form');
  const registerForm = document.querySelector('.register-form');

  if (loginForm) loginForm.style.display = 'none';
  if (registerForm) registerForm.style.display = 'block';
}

// Funkcja do przełączania formularza logowania
function showLoginForm() {
  const registerForm = document.querySelector('.register-form');
  const loginForm = document.querySelector('.login-form');

  if (registerForm) registerForm.style.display = 'none';
  if (loginForm) loginForm.style.display = 'block';
}

function deleteClient(nip) {
  if (confirm("Czy na pewno chcesz usunąć tego klienta?")) {
      
      window.location.href = "delete_client.php?nip=" + nip;
  }
}


document.addEventListener("DOMContentLoaded", function () {
  // Przycisk do wyświetlenia historii transakcji
  const transactionHistoryButton = document.getElementById('transaction-history-button');
  if (transactionHistoryButton) {
    transactionHistoryButton.addEventListener("click", function() {
      const nip = transactionHistoryButton.getAttribute("data-nip"); 
      showTransactionHistoryAJAX(nip); 
    });
  }
});

function closeAllModals() {
  var modals = document.querySelectorAll('.modal');
  modals.forEach(function(modal) {
    modal.style.display = 'none';
  });
}

// Funkcja do otwierania modala
function openModal(modalId) {
  closeAllModals();  

  var modal = document.getElementById(modalId);
  if (modal) {
    modal.style.display = 'flex';
    modal.style.zIndex = "1050"; 
  }
}

// Funkcja do zamknięcia konkretnego modala
function closeModal(modalId) {
  var modal = document.getElementById(modalId);
  if (modal) {
    modal.style.display = 'none';
  }
}

// Funkcja do otwierania historii transakcji przez AJAX
function showTransactionHistoryAJAX(nip) {
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "load_transaction_history.php?nip=" + nip, true);

  xhr.onload = function () {
    if (xhr.status === 200) {
      var container = document.getElementById('transaction-history-container');
      if (container) {
        container.innerHTML = xhr.responseText; 
        container.style.display = 'block'; 

      
        openModal('transaction-history-modal');
      } else {
        console.error("Nie znaleziono kontenera dla historii transakcji.");
      }
    } else {
      alert('Wystąpił problem z ładowaniem historii transakcji.');
    }
  };

  xhr.onerror = function () {
    alert('Błąd połączenia z serwerem.');
  };

  xhr.send();
}