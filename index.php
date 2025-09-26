<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sklep Internetowy</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <img src="images/1.png" alt="Sklep Logo">
    </header>

<nav>
    <div class="nav-buttons">
        <button class="nav-btn" onclick="location.href='php/koszyk.php'">Koszyk</button>

        <?php if (isset($_SESSION["email"])): ?>
            <span style="color: white; margin-left: 10px;">Witaj, <?= htmlspecialchars($_SESSION["email"]) ?></span>
        <?php if (isset($_SESSION["rola"]) && $_SESSION["rola"] === 'admin'): ?>
            <button class="nav-btn" onclick="location.href='php/dodaj_produkt.php'">Dodaj produkt</button>
        <?php endif; ?>

            <button class="nav-btn" onclick="location.href='php/logout.php'">Wyloguj się</button>
        <?php else: ?>
            <button class="nav-btn" onclick="location.href='php/logowanie.php'">Logowanie</button>
            <button class="nav-btn" onclick="location.href='php/rejestracja.php'">Rejestracja</button>
        <?php endif; ?>
        <button class="nav-btn" onclick="location.href='snake/index.html'">snake</button>
        <button class="nav-btn" onclick="location.href='pong/index.html'">pong</button>
    </div>
</nav>

    <form method="get" class="filtruj-form">
        <input type="text" name="szukaj" placeholder="Szukaj nazwy produktu">
        <input type="number" step="0.01" name="maxCena" placeholder="Cena maksymalna">
        <button type="submit">Filtruj</button>
        <button type="button" onclick="window.location.href='index.php'">Resetuj filtr</button>
    </form>


    <div class="container">

<?php
// Nazwa serwera SQL z nazwą instancji
$serverName = "localhost\\SQLEXPRESS";

// Parametry połączenia z bazą
// TrustServerCertificate - akceptuj certyfikat
// CharacterSet - wymuszenie kodowania UTF-8 dla polskich znaków
$connectionOptions = array(
    "Database" => "SklepDB",
    "TrustServerCertificate" => true,
    "CharacterSet" => "UTF-8"
);

// Nawiązanie połączenia z SQL Server
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Sprawdzenie, czy połączenie się udało
if (!$conn) {
    // Jeśli nie – pokaż błąd i zakończ skrypt
    die("<p style='color:red;'>Błąd połączenia z bazą:</p><pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}

// Warunki filtrowania produktów (domyślnie: tylko dostępne)
$warunki = ["dostepnosc = 1"];
$params = []; // Tablica z wartościami do podstawienia w zapytaniu SQL

// Jeżeli wpisaliśmy coś w polu "szukaj", dodaje do kwerendy warunek "nazwa LIKE ?"
if (!empty($_GET["szukaj"])) {
    $warunki[] = "nazwa LIKE ?";
    $params[] = "%" . $_GET["szukaj"] . "%"; // szukamy podobieństw (np. zawiera tekst)
}

// Jeśli podano maksymalną cenę i jest to liczba, dodje warunek "cena <= ?"
if (!empty($_GET["maxCena"]) && is_numeric($_GET["maxCena"])) {
    $warunki[] = "cena <= ?";
    $params[] = (float)$_GET["maxCena"];
}

// Jedna kwerenda z klauzulą WHERE
$sql = "SELECT * FROM Produkty WHERE " . implode(" AND ", $warunki);

$stmt = sqlsrv_query($conn, $sql, $params);

// Nagłówek i kontener na produkty
echo "<h2>Nasze produkty</h2>";
echo "<div class='produkty'>";

// Jeśli zapytanie SQL się nie powiodło wyświetlam błąd
if (!$stmt) {
    echo "<p style='color:red;'>Błąd w zapytaniu SQL:</p>";
    echo "<pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
} else {
    // Przetwarzanie każdego wiersza z wyniku zapytania
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Pobranie wartości z kolumny 'nazwa' lub "Brak nazwy" jeśli null
        $nazwa = $row["nazwa"] ?? "Brak nazwy";

        // Pobranie opisu (lub pusty tekst jeśli brak)
        $opis = $row["opis"] ?? "";

        // Pobranie ceny – konwersja na liczbę i sformatowanie do 2 miejsc po przecinku
        $cena = isset($row["cena"]) ? number_format((float)$row["cena"], 2) : "0.00";
        $obrazek = $row["obrazek"] ?? "images/brak.png";

        // Wyświetlenie jednego produktu w HTML
        echo "<div class='produkt'>";
        echo "<img src='$obrazek' alt='$nazwa' class='miniatura'>";
        echo "<h3>$nazwa</h3>";
        echo "<p>$opis</p>";
        echo "<span class='cena'>$cena zł</span>";
        // Funkcjonalność koszyka 1
        echo "<button onclick=\"dodajDoKoszyka('" . htmlspecialchars($nazwa) . "', " . (float)$cena . ")\">Dodaj do koszyka</button>";
        echo "</div>";
}
}

// Zamknięcie kontenera .produkty
echo "</div>";

// Zamknięcie połączenia z bazą
sqlsrv_close($conn);
?>

    </div>

    <footer>
        <div class="footer-buttons">
            <button>Zasady użytkowania</button>
            <button>Polityka prywatności</button>
            <button>Polityka dotycząca plików cookie</button>
        </div>
    </footer>

<script>
// Funkcjonalność koszyka 2
// Funkcja dodająca produkt do koszyka
function dodajDoKoszyka(nazwa, cena) {
    let koszyk = JSON.parse(localStorage.getItem("koszyk")) || [];

    // Sprawdzamy czy produkt jest już w koszyku
    let istnieje = koszyk.find(p => p.nazwa === nazwa);
    if (istnieje) {
        istnieje.ilosc += 1;
    } else {
        koszyk.push({ nazwa: nazwa, cena: cena, ilosc: 1 });
    }

    localStorage.setItem("koszyk", JSON.stringify(koszyk));
    alert("Dodano do koszyka: " + nazwa);
}
</script>

</body>
</html>