<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Koszyk</title>
    <link rel="stylesheet" href="../css/koszyk.css">
</head>
<body>
<header>
    <img src="../images/1.png" alt="Sklep Logo">
</header>
<nav>
    <div class="nav-buttons">
        <button class="nav-btn" onclick="location.href='../index.php'">Strona główna</button>
        <button class="nav-btn" onclick="location.href='koszyk.php'">Koszyk</button>
        <?php if (isset($_SESSION["rola"]) && $_SESSION["rola"] === 'admin'): ?>
            <button class="nav-btn" onclick="location.href='dodaj_produkt.php'">➕ Dodaj produkt</button>
        <?php endif; ?>
        <?php if (isset($_SESSION["email"])): ?>
            <span style="color: white; margin-left: 10px;">Witaj, <?= htmlspecialchars($_SESSION["email"]) ?></span>
            <button class="nav-btn" onclick="location.href='logout.php'">Wyloguj się</button>
        <?php else: ?>
            <button class="nav-btn" onclick="location.href='logowanie.php'">Logowanie</button>
            <button class="nav-btn" onclick="location.href='rejestracja.php'">Rejestracja</button>
        <?php endif; ?>
    </div>
</nav>

<div class="container">
    <h2>Twój koszyk</h2>
    <div id="komunikat"></div>
    <button class="nav-btn" onclick="wyczyscKoszyk()">🗑️ Wyczyść koszyk</button>
    <div id="koszyk"></div>
</div>

<script>
const kontener = document.getElementById("koszyk");
let koszyk = JSON.parse(localStorage.getItem("koszyk")) || [];

if (koszyk.length === 0) {
    kontener.innerHTML = "<p>Koszyk jest pusty.</p>";
} else {
    let suma = 0;
    let html = "<table><tr><th>Produkt</th><th>Cena</th><th>Ilość</th><th>Razem</th></tr>";
    koszyk.forEach(p => {
        let razem = p.cena * p.ilosc;
        suma += razem;
        html += `<tr><td>${p.nazwa}</td><td>${p.cena.toFixed(2)} zł</td><td>${p.ilosc}</td><td>${razem.toFixed(2)} zł</td></tr>`;
    });
    html += `<tr><td colspan="3"><strong>Suma:</strong></td><td><strong>${suma.toFixed(2)} zł</strong></td></tr>`;

    if (<?= isset($_SESSION["uzytkownikID"]) ? 'true' : 'false' ?>) {
        html += `<tr><td colspan="4" style="text-align:right;">
                    <button class="btn-submit" onclick="zlozZamowienie()">Złóż zamówienie</button>
                </td></tr>`;
    }
    html += "</table>";
    kontener.innerHTML = html;
}

function wyczyscKoszyk() {
    if (confirm("Czy na pewno chcesz wyczyścić koszyk?")) {
        localStorage.removeItem("koszyk");
        location.reload();
    }
}

function zlozZamowienie() {
    if (!confirm("Czy na pewno chcesz złożyć zamówienie?")) return;

    let koszyk = JSON.parse(localStorage.getItem("koszyk")) || [];
    let suma = koszyk.reduce((acc, p) => acc + (p.cena * p.ilosc), 0);

    fetch("zamowienie.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ koszyk: koszyk, suma: suma })
    })
    .then(res => res.text())
    .then(res => {
        wyswietlKomunikat(res.includes("zostało") ? "success" : "error", res);
        if (res.includes("zostało")) {
            localStorage.removeItem("koszyk");
            setTimeout(() => location.reload(), 2000);
        }
    });
}

function wyswietlKomunikat(typ, tresc) {
    const div = document.getElementById("komunikat");
    div.className = typ;
    div.innerText = tresc;
    div.style.display = "block";
}
</script>
</body>
</html>
