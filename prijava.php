<?php
require_once 'connect.php';
require_once 'auth.php';

if (korisnikJePrijavljen()) {
    header('Location: index.php');
    exit;
}

$neuspjesnaPrijava = false;
$korisnickoIme = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $korisnickoIme = trim($_POST['korisnickoIme'] ?? '');
    $lozinka = $_POST['lozinka'] ?? '';

    $loginStatement = mysqli_prepare(
        $dbc,
        'SELECT id, ime, korisnickoIme, lozinka, razina
         FROM korisnik WHERE korisnickoIme = ?'
    );
    mysqli_stmt_bind_param($loginStatement, 's', $korisnickoIme);
    mysqli_stmt_execute($loginStatement);
    $loginResult = mysqli_stmt_get_result($loginStatement);
    $user = mysqli_fetch_assoc($loginResult);

    if ($user && password_verify($lozinka, $user['lozinka'])) {
        session_regenerate_id(true);
        $_SESSION['korisnik_id'] = (int) $user['id'];
        $_SESSION['ime'] = $user['ime'];
        $_SESSION['korisnicko_ime'] = $user['korisnickoIme'];
        $_SESSION['razina'] = (int) $user['razina'];

        header('Location: ' . ($_SESSION['razina'] === 1 ? 'administracija.php' : 'index.php'));
        exit;
    }

    $neuspjesnaPrijava = true;
}

$pageTitle = 'Prijava';
require 'header.php';
?>
<main class="form-content auth-content">
    <section class="form-panel" aria-labelledby="login-title">
        <header class="form-header">
            <p class="form-kicker">Korisnički račun</p>
            <h1 id="login-title">Prijava</h1>
        </header>

        <?php if (isset($_GET['registriran'])): ?>
            <p class="status-message" role="status">Registracija je uspješna. Sada se možete prijaviti.</p>
        <?php endif; ?>

        <?php if ($neuspjesnaPrijava): ?>
            <p class="form-errors" role="alert">Korisničko ime ili lozinka nisu ispravni.</p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-item">
                <label for="korisnickoIme">Korisničko ime</label>
                <input type="text" name="korisnickoIme" id="korisnickoIme" maxlength="50"
                       value="<?= htmlspecialchars($korisnickoIme, ENT_QUOTES, 'UTF-8') ?>" required autofocus>
            </div>
            <div class="form-item">
                <label for="lozinka">Lozinka</label>
                <input type="password" name="lozinka" id="lozinka" required>
            </div>
            <div class="form-actions">
                <a class="button button-secondary" href="registracija.php">Registracija</a>
                <button type="submit" class="button button-primary">Prijava</button>
            </div>
        </form>
    </section>
</main>

<?php require 'footer.php'; ?>
