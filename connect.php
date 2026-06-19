<?php
$server = getenv('DB_HOST') ?: 'localhost';
$korisnik = getenv('DB_USER') ?: 'root';
$lozinka = getenv('DB_PASSWORD') ?: '';
$baza = getenv('DB_NAME') ?: 'newsweek';

$dbc = mysqli_connect($server, $korisnik, $lozinka, $baza);

if (!$dbc) {
    die('Povezivanje s bazom podataka nije uspjelo.');
}

mysqli_set_charset($dbc, 'utf8mb4');
