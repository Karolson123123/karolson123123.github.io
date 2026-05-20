<?php
$servername = "localhost";
$username = "root";
$password = "123"; // Wpisz swoje hasło do MariaDB
$dbname = "ngineers_db";

// Połączenie z bazą danych przy użyciu MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}
?>