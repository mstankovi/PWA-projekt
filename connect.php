<?php
$server = 'localhost';
$korisnik = 'root';
$lozinka = '';
$baza = 'newsweek';

$dbc = mysqli_connect($server, $korisnik, $lozinka, $baza);

if (!$dbc) {
    die('Povezivanje s bazom podataka nije uspjelo.');
}

mysqli_set_charset($dbc, 'utf8mb4');
