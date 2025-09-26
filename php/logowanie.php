<?php
session_start(); // Uruchomienie sesji do przechowywania danych logowania

// Sprawdzenie, czy formularz został wysłany metodą POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Połączenie z bazą danych SQL Server
    $conn = sqlsrv_connect("localhost\\SQLEXPRESS", [
        "Database" => "SklepDB",
        "TrustServerCertificate" => true,
        "CharacterSet" => "UTF-8"
    ]);

    // Pobranie danych z formularza
    $email = $_POST["email"];
    $haslo = $_POST["haslo"];

    // Zapytanie SQL do wyszukania użytkownika po e-mailu
    $sql = "SELECT * FROM Uzytkownicy WHERE email = ?";
    $stmt = sqlsrv_query($conn, $sql, [$email]);

    // Jeśli zapytanie zwróci wynik, sprawdzamy hasło
    if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        if (password_verify($haslo, $row["haslo"])) {
            // Jeśli hasło poprawne – zapisujemy dane użytkownika do sesji
            $_SESSION["uzytkownikID"] = $row["uzytkownikID"];
            $_SESSION["email"] = $row["email"];
            $_SESSION["rola"] = $row["rola"];
            header("Location: ../index.php");
            exit;

        } else {
            echo "Nieprawidłowe hasło.";
        }
    } else {
        echo "Użytkownik nie istnieje.";
    }

    // Zamknięcie połączenia z bazą
    sqlsrv_close($conn);
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rejestracja</title>
    <link rel="stylesheet" href="../css/logowanie.css">
</head>
<body>

    <header>
        <img src="../images/1.png" alt="Sklep Logo">
    </header>
    <nav>
        <button class="nav-btn" onclick="location.href='../index.php'">Strona główna</button>
    </nav>
    <div class="container">
        <!-- Formularz logowania -->
        <form method="post">
            <label>Email: <input type="email" name="email" required></label><br>
            <label>Hasło: <input type="password" name="haslo" required></label><br>
            <button type="submit">Zaloguj się</button>
        </form>
    </div>

</body>
</html>