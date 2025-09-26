<?php
session_start();            // Uruchom sesję
session_unset();            // Wyczyść dane sesji
session_destroy();          // Zniszcz sesję
header("Location: ../index.php"); // Przekieruj na stronę główną
exit;
