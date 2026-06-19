<?php
if (session_status() === PHP_SESSION_NONE) {
    $secureCookie = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    session_set_cookie_params([
        'httponly' => true,
        'secure' => $secureCookie,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function korisnikJePrijavljen(): bool
{
    return isset($_SESSION['korisnik_id']);
}

function korisnikJeAdministrator(): bool
{
    return korisnikJePrijavljen() && ($_SESSION['razina'] ?? 0) === 1;
}

function zahtijevajAdministratora(): void
{
    if (!korisnikJePrijavljen()) {
        header('Location: prijava.php');
        exit;
    }

    if (!korisnikJeAdministrator()) {
        http_response_code(403);
        $pageTitle = 'Zabranjen pristup';
        require 'header.php';
        ?>
        <main class="message-content">
            <section class="message-panel" aria-labelledby="access-title">
                <h1 id="access-title">Nemate administratorska prava</h1>
                <p>
                    Korisnik
                    <strong><?= htmlspecialchars($_SESSION['korisnicko_ime'], ENT_QUOTES, 'UTF-8') ?></strong>
                    nema dopuštenje za pristup ovoj stranici.
                </p>
                <a class="button button-primary" href="index.php">Povratak na naslovnicu</a>
            </section>
        </main>
        <?php
        require 'footer.php';
        exit;
    }
}
