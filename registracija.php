<?php
require_once 'connect.php';
require_once 'auth.php';

if (korisnikJePrijavljen()) {
    header('Location: index.php');
    exit;
}

$greske = [];
$ime = '';
$prezime = '';
$korisnickoIme = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime'] ?? '');
    $prezime = trim($_POST['prezime'] ?? '');
    $korisnickoIme = trim($_POST['korisnickoIme'] ?? '');
    $lozinka = $_POST['lozinka'] ?? '';
    $ponovljenaLozinka = $_POST['ponovljenaLozinka'] ?? '';

    if ($ime === '' || mb_strlen($ime) > 50) {
        $greske[] = 'Ime je obavezno i smije sadržavati najviše 50 znakova.';
    }
    if ($prezime === '' || mb_strlen($prezime) > 50) {
        $greske[] = 'Prezime je obavezno i smije sadržavati najviše 50 znakova.';
    }
    if ($korisnickoIme === '' || mb_strlen($korisnickoIme) > 50) {
        $greske[] = 'Korisničko ime je obavezno i smije sadržavati najviše 50 znakova.';
    }
    if ($lozinka === '') {
        $greske[] = 'Lozinka je obavezna.';
    } elseif ($lozinka !== $ponovljenaLozinka) {
        $greske[] = 'Lozinke se ne podudaraju.';
    }

    if ($greske === []) {
        $checkStatement = mysqli_prepare($dbc, 'SELECT id FROM korisnik WHERE korisnickoIme = ?');
        mysqli_stmt_bind_param($checkStatement, 's', $korisnickoIme);
        mysqli_stmt_execute($checkStatement);
        mysqli_stmt_store_result($checkStatement);

        if (mysqli_stmt_num_rows($checkStatement) > 0) {
            $greske[] = 'Korisničko ime već postoji.';
        } else {
            $hashLozinke = password_hash($lozinka, PASSWORD_DEFAULT);
            $insertStatement = mysqli_prepare(
                $dbc,
                'INSERT INTO korisnik (ime, prezime, korisnickoIme, lozinka, razina)
                 VALUES (?, ?, ?, ?, 0)'
            );
            mysqli_stmt_bind_param($insertStatement, 'ssss', $ime, $prezime, $korisnickoIme, $hashLozinke);

            if (mysqli_stmt_execute($insertStatement)) {
                header('Location: prijava.php?registriran=1');
                exit;
            }

            $greske[] = 'Registracija nije uspjela.';
        }
    }
}

$pageTitle = 'Registracija';
require 'header.php';
?>
<main class="form-content auth-content">
    <section class="form-panel" aria-labelledby="registration-title">
        <header class="form-header">
            <p class="form-kicker">Korisnički račun</p>
            <h1 id="registration-title">Registracija</h1>
            <p>Sva polja su obavezna.</p>
        </header>

        <?php if ($greske !== []): ?>
            <div class="form-errors" role="alert">
                <ul>
                    <?php foreach ($greske as $greska): ?>
                        <li><?= htmlspecialchars($greska, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-item">
                <label for="ime">Ime</label>
                <input type="text" name="ime" id="ime" maxlength="50"
                       value="<?= htmlspecialchars($ime, ENT_QUOTES, 'UTF-8') ?>" required autofocus>
            </div>
            <div class="form-item">
                <label for="prezime">Prezime</label>
                <input type="text" name="prezime" id="prezime" maxlength="50"
                       value="<?= htmlspecialchars($prezime, ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="form-item">
                <label for="korisnickoIme">Korisničko ime</label>
                <input type="text" name="korisnickoIme" id="korisnickoIme" maxlength="50"
                       value="<?= htmlspecialchars($korisnickoIme, ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="form-item">
                <label for="lozinka">Lozinka</label>
                <input type="password" name="lozinka" id="lozinka" required>
            </div>
            <div class="form-item">
                <label for="ponovljenaLozinka">Ponovite lozinku</label>
                <input type="password" name="ponovljenaLozinka" id="ponovljenaLozinka" required>
            </div>
            <div class="form-actions">
                <a class="button button-secondary" href="prijava.php">Već imam račun</a>
                <button type="submit" class="button button-primary">Registriraj se</button>
            </div>
        </form>
    </section>
</main>

<?php require 'footer.php'; ?>
