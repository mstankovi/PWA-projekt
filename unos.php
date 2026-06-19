<?php
require_once 'auth.php';
zahtijevajAdministratora();
require_once 'connect.php';
require_once 'upload.php';

$greske = [];
$naslov = '';
$sazetak = '';
$tekst = '';
$kategorijaId = '';
$arhiva = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naslov = trim($_POST['naslov'] ?? '');
    $sazetak = trim($_POST['sazetak'] ?? '');
    $tekst = trim($_POST['tekst'] ?? '');
    $kategorijaId = filter_var($_POST['kategorija'] ?? '', FILTER_VALIDATE_INT);
    $arhiva = isset($_POST['arhiva']);

    if ($naslov === '' || mb_strlen($naslov) > 100) {
        $greske[] = 'Naslov je obavezan i smije sadržavati najviše 100 znakova.';
    }

    if ($sazetak === '' || mb_strlen($sazetak) > 100) {
        $greske[] = 'Sažetak je obavezan i smije sadržavati najviše 100 znakova.';
    }

    if ($tekst === '') {
        $greske[] = 'Tekst vijesti je obavezan.';
    }

    if (!$kategorijaId) {
        $greske[] = 'Potrebno je odabrati kategoriju.';
    } else {
        $categoryStatement = mysqli_prepare($dbc, 'SELECT id FROM kategorije WHERE id = ?');
        mysqli_stmt_bind_param($categoryStatement, 'i', $kategorijaId);
        mysqli_stmt_execute($categoryStatement);
        mysqli_stmt_store_result($categoryStatement);

        if (mysqli_stmt_num_rows($categoryStatement) === 0) {
            $greske[] = 'Odabrana kategorija ne postoji.';
        }
    }

    if (!isset($_FILES['slika']) || $_FILES['slika']['error'] === UPLOAD_ERR_NO_FILE) {
        $greske[] = 'Potrebno je odabrati sliku.';
    } elseif ($_FILES['slika']['error'] !== UPLOAD_ERR_OK) {
        $greske[] = 'Slika nije uspješno prenesena.';
    }

    if ($greske === []) {
        $greskaSlike = '';
        $putanjaSlike = spremiUploadanuSliku($_FILES['slika'], $greskaSlike);

        if (!$putanjaSlike) {
            $greske[] = $greskaSlike;
        } else {
            $insertQuery = 'INSERT INTO vijesti
                            (naslov, sazetak, tekst, slika_url, idKategorija, arhiva)
                            VALUES (?, ?, ?, ?, ?, ?)';
            $insertStatement = mysqli_prepare($dbc, $insertQuery);
            $archiveValue = $arhiva ? 1 : 0;
            mysqli_stmt_bind_param(
                $insertStatement,
                'ssssii',
                $naslov,
                $sazetak,
                $tekst,
                $putanjaSlike,
                $kategorijaId,
                $archiveValue
            );

            if (mysqli_stmt_execute($insertStatement)) {
                $newArticleId = mysqli_insert_id($dbc);
                $redirectPage = $arhiva ? 'administracija.php' : 'clanak.php';
                header('Location: ' . $redirectPage . '?id=' . $newArticleId);
                exit;
            }

            obrisiUploadanuSliku($putanjaSlike);
            $greske[] = 'Vijest nije uspješno spremljena u bazu.';
        }
    }
}

$pageTitle = 'Unos vijesti';
require 'header.php';
$categories = mysqli_query($dbc, 'SELECT id, ime FROM kategorije ORDER BY id');
?>
<main class="form-content">
    <section class="form-panel" aria-labelledby="form-title">
        <header class="form-header">
            <p class="form-kicker">Nova vijest</p>
            <h1 id="form-title">Unos sadržaja</h1>
            <p>Sva polja označena zvjezdicom obavezno je ispuniti.</p>
        </header>

        <?php if ($greske !== []): ?>
            <div class="form-errors" role="alert">
                <p>Ispravite sljedeće pogreške:</p>
                <ul>
                    <?php foreach ($greske as $greska): ?>
                        <li><?= htmlspecialchars($greska, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-item">
                <label for="naslov">Naslov <span aria-hidden="true">*</span></label>
                <input type="text" name="naslov" id="naslov" maxlength="100"
                       value="<?= htmlspecialchars($naslov, ENT_QUOTES, 'UTF-8') ?>" required autofocus>
            </div>

            <div class="form-item">
                <label for="sazetak">Kratki sažetak <span aria-hidden="true">*</span></label>
                <textarea name="sazetak" id="sazetak" rows="3" maxlength="100" required><?= htmlspecialchars($sazetak, ENT_QUOTES, 'UTF-8') ?></textarea>
                <small>Najviše 100 znakova.</small>
            </div>

            <div class="form-item">
                <label for="tekst">Tekst vijesti <span aria-hidden="true">*</span></label>
                <textarea name="tekst" id="tekst" rows="12" required><?= htmlspecialchars($tekst, ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="form-item">
                <label for="kategorija">Kategorija <span aria-hidden="true">*</span></label>
                <select name="kategorija" id="kategorija" required>
                    <option value="">Odaberite kategoriju</option>
                    <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?= $category['id'] ?>" <?= (int) $kategorijaId === (int) $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['ime'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-item">
                <label for="slika">Slika <span aria-hidden="true">*</span></label>
                <input type="file" name="slika" id="slika" accept="image/jpeg,image/png,image/webp" required>
                <small>Dozvoljeni formati: JPEG, PNG i WebP. Najveća veličina: 5 MB.</small>
            </div>

            <div class="checkbox-item">
                <input type="checkbox" name="arhiva" id="arhiva" value="1" <?= $arhiva ? 'checked' : '' ?>>
                <label for="arhiva">Spremi vijest u arhivu</label>
            </div>

            <div class="form-actions">
                <button type="reset" class="button button-secondary">Poništi</button>
                <button type="submit" class="button button-primary">Prihvati</button>
            </div>
        </form>
    </section>
</main>

<?php require 'footer.php'; ?>
