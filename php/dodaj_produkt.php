<?php
session_start();

// sprawdzamy czy użytkownik jest zalogowany i ma wlasciwa role
if (!isset($_SESSION["uzytkownikID"]) || $_SESSION["rola"] !== 'admin') {
    die("Dostęp tylko dla administratora.");
}


// połączenie z bazą
$conn = sqlsrv_connect("localhost\\SQLEXPRESS", [
    "Database" => "SklepDB",
    "TrustServerCertificate" => true,
    "CharacterSet" => "UTF-8"
]);

// metoda post pobierania danych z formularza i wykonanie kwerendy do bazy
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nazwa = $_POST["nazwa"];
    $opis = $_POST["opis"];
    $cena = (float)$_POST["cena"];
    $dostepnosc = isset($_POST["dostepnosc"]) ? 1 : 0;

    $sql = "INSERT INTO Produkty (nazwa, opis, cena, dostepnosc, obrazek)
    VALUES (?, ?, ?, ?, ?)";
    
    $params = [$nazwa, $opis, $cena, $dostepnosc, $_POST["obrazek"]];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) { // przekierowanie nastrone główną po dodaniu produktu
        header("Location: ../index.php");
        exit;
    } else {
        echo "Błąd: " . print_r(sqlsrv_errors(), true);
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dodaj produkt</title>
    <link rel="stylesheet" href="../css/rejestracja.css">
</head>
<body>
<header>
    <img src="../images/1.png" alt="Logo">
</header>
<nav>
    <div class="nav-buttons">
        <button class="nav-btn" onclick="location.href='../index.php'">Strona główna</button>
        <button class="nav-btn" onclick="location.href='logout.php'">Wyloguj się</button>
    </div>
</nav>
<div class="container">
    <h2>Dodaj nowy produkt</h2>
    <form method="post">
        <label>Nazwa: <input type="text" name="nazwa" required></label>
        <label>Opis: <input type="text" name="opis"></label>
        <label>Link do obrazka (URL lub ścieżka lokalna): <input type="text" name="obrazek"></label>
        <label>Cena: <input type="number" name="cena" step="0.01" required></label>
        <label><input type="checkbox" name="dostepnosc" checked> Dostępny</label>
        <button type="submit">Dodaj produkt</button>
    </form>
</div>
</body>
</html>
