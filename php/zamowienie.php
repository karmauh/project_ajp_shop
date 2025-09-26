<?php
session_start();

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION["uzytkownikID"])) {
    die("Musisz być zalogowany, aby złożyć zamówienie.");
}

// odbieramy dane z JS
$data = json_decode(file_get_contents("php://input"), true);

$koszyk = $data["koszyk"] ?? [];
$suma = 0;

foreach ($koszyk as $produkt) {
    $suma += $produkt["cena"] * $produkt["ilosc"];
}

// Połączenie z bazą danych
$conn = sqlsrv_connect("localhost\\SQLEXPRESS", [
    "Database" => "SklepDB",
    "TrustServerCertificate" => true,
    "CharacterSet" => "UTF-8"
]);

if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}

// pobieramy  aktualną datę w php
$dataZamowienia = date("Y-m-d H:i:s");

$sql = "INSERT INTO Zamowienia (uzytkownikID, dataZamowienia, suma)
        VALUES (?, ?, ?)";

$params = [$_SESSION["uzytkownikID"], $dataZamowienia, $suma];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt) {
    echo "Zamówienie zostało zapisane.";
} else {
    echo "Błąd: " . print_r(sqlsrv_errors(), true);
}

sqlsrv_close($conn);

?>