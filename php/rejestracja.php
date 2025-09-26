<?php
// Sprawdzenie, czy formularz został wysłany metodą POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Połączenie z bazą danych SQL Server
    $serverName = "localhost\\SQLEXPRESS";
    $conn = sqlsrv_connect($serverName, [
        "Database" => "SklepDB",
        "TrustServerCertificate" => true,
        "CharacterSet" => "UTF-8"
    ]);

    // Sprawdzenie poprawności połączenia
    if (!$conn) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Pobranie danych z formularza
    $imie = $_POST["imie"];
    $nazwisko = $_POST["nazwisko"];
    $email = $_POST["email"];
    $haslo = password_hash($_POST["haslo"], PASSWORD_DEFAULT); // Haszowanie hasła

    // Zapytanie SQL do dodania użytkownika do bazy
    $sql = "INSERT INTO Uzytkownicy (imie, nazwisko, email, haslo)
            VALUES (?, ?, ?, ?)";
    $params = [$imie, $nazwisko, $email, $haslo];
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Informacja o wyniku rejestracji
    if ($stmt) {
        header("Location: ../index.php");
        exit;
    } else {
        echo "Błąd: " . print_r(sqlsrv_errors(), true);
    }

    // Zamknięcie połączenia
    sqlsrv_close($conn);
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rejestracja</title>
    <link rel="stylesheet" href="../css/rejestracja.css">
</head>
<body>

    <header>
        <img src="../images/1.png" alt="Sklep Logo">
    </header>
    <nav>
        <button class="nav-btn" onclick="location.href='../index.php'">Strona główna</button>
    </nav>
    <div class="container">
        <!-- Formularz rejestracji użytkownika -->
        <form method="post">
            <label>Imię: <input type="text" name="imie" required></label><br>
            <label>Nazwisko: <input type="text" name="nazwisko" required></label><br>
            <label>Email: <input type="email" name="email" required></label><br>
            <label>Hasło: <input type="password" name="haslo" required></label><br>
            <button type="submit">Zarejestruj się</button>
        </form>
    </div>

</body>
</html>