<?php
require_once 'auth.php';
zahtijevajAdministratora();
require_once 'connect.php';
require_once 'upload.php';

$greske = [];
$selectedId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$editArticle = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $articleId = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);

    if (!$articleId) {
        $greske[] = 'Vijest nije ispravno odabrana.';
    }

    if ($action === 'delete' && $greske === []) {
        $imageStatement = mysqli_prepare($dbc, 'SELECT slika_url FROM vijesti WHERE id = ?');
        mysqli_stmt_bind_param($imageStatement, 'i', $articleId);
        mysqli_stmt_execute($imageStatement);
        $imageResult = mysqli_stmt_get_result($imageStatement);
        $imageRow = mysqli_fetch_assoc($imageResult);

        $deleteStatement = mysqli_prepare($dbc, 'DELETE FROM vijesti WHERE id = ?');
        mysqli_stmt_bind_param($deleteStatement, 'i', $articleId);
        if (mysqli_stmt_execute($deleteStatement)) {
            if ($imageRow) {
                obrisiUploadanuSliku($imageRow['slika_url']);
            }

            header('Location: administracija.php?status=deleted');
            exit;
        }

        $greske[] = 'Vijest nije uspješno izbrisana.';
    }

    if ($action === 'update') {
        $naslov = trim($_POST['naslov'] ?? '');
        $sazetak = trim($_POST['sazetak'] ?? '');
        $tekst = trim($_POST['tekst'] ?? '');
        $kategorijaId = filter_var($_POST['kategorija'] ?? '', FILTER_VALIDATE_INT);
        $arhiva = isset($_POST['arhiva']) ? 1 : 0;

        $currentImageStatement = mysqli_prepare($dbc, 'SELECT slika_url FROM vijesti WHERE id = ?');
        mysqli_stmt_bind_param($currentImageStatement, 'i', $articleId);
        mysqli_stmt_execute($currentImageStatement);
        $currentImageResult = mysqli_stmt_get_result($currentImageStatement);
        $currentImageRow = mysqli_fetch_assoc($currentImageResult);
        $staraSlika = $currentImageRow['slika_url'] ?? '';

        if (!$currentImageRow) {
            $greske[] = 'Odabrana vijest ne postoji.';
        }

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

        $putanjaSlike = $staraSlika;
        $novaSlikaSpremljena = false;

        if (isset($_FILES['slika']) && $_FILES['slika']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($greske === []) {
                $greskaSlike = '';
                $novaPutanja = spremiUploadanuSliku($_FILES['slika'], $greskaSlike);

                if ($novaPutanja) {
                    $putanjaSlike = $novaPutanja;
                    $novaSlikaSpremljena = true;
                } else {
                    $greske[] = $greskaSlike;
                }
            }
        }

        if ($greske === []) {
            $updateQuery = 'UPDATE vijesti
                            SET naslov = ?, sazetak = ?, tekst = ?, slika_url = ?, idKategorija = ?, arhiva = ?
                            WHERE id = ?';
            $updateStatement = mysqli_prepare($dbc, $updateQuery);
            mysqli_stmt_bind_param(
                $updateStatement,
                'ssssiii',
                $naslov,
                $sazetak,
                $tekst,
                $putanjaSlike,
                $kategorijaId,
                $arhiva,
                $articleId
            );
            if (mysqli_stmt_execute($updateStatement)) {
                if ($novaSlikaSpremljena) {
                    obrisiUploadanuSliku($staraSlika);
                }

                header('Location: administracija.php?id=' . $articleId . '&status=updated');
                exit;
            }

            if ($novaSlikaSpremljena) {
                obrisiUploadanuSliku($putanjaSlike);
            }
            $greske[] = 'Promjene nisu spremljene.';
        }

        $selectedId = $articleId;
        $editArticle = [
            'id' => $articleId,
            'naslov' => $naslov,
            'sazetak' => $sazetak,
            'tekst' => $tekst,
            'slika_url' => $staraSlika,
            'idKategorija' => $kategorijaId,
            'arhiva' => $arhiva,
        ];
    }
}

if ($selectedId && !$editArticle) {
    $editQuery = 'SELECT id, naslov, sazetak, tekst, slika_url, idKategorija, arhiva
                  FROM vijesti WHERE id = ?';
    $editStatement = mysqli_prepare($dbc, $editQuery);
    mysqli_stmt_bind_param($editStatement, 'i', $selectedId);
    mysqli_stmt_execute($editStatement);
    $editResult = mysqli_stmt_get_result($editStatement);
    $editArticle = mysqli_fetch_assoc($editResult);

    if (!$editArticle) {
        header('Location: administracija.php');
        exit;
    }
}

$pageTitle = 'Administracija';
require 'header.php';
$categories = mysqli_query($dbc, 'SELECT id, ime FROM kategorije ORDER BY id');

$statusMessages = [
    'updated' => 'Vijest je izmijenjena.',
    'deleted' => 'Vijest je izbrisana.',
];
$status = $_GET['status'] ?? '';
?>
<main class="admin-content">
    <header class="admin-header">
        <p class="form-kicker">Upravljanje sadržajem</p>
        <h1>Administracija</h1>
    </header>

    <?php if (isset($statusMessages[$status])): ?>
        <p class="status-message" role="status"><?= $statusMessages[$status] ?></p>
    <?php endif; ?>

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

    <?php if (!$editArticle): ?>
        <?php
        $articles = mysqli_query(
            $dbc,
            'SELECT v.id, v.naslov, v.sazetak, v.slika_url, v.arhiva, v.datum, k.ime AS kategorija
             FROM vijesti v
             JOIN kategorije k ON k.id = v.idKategorija
             ORDER BY v.datum DESC'
        );
        ?>
        <div class="admin-toolbar">
            <p>Odaberite vijest koju želite urediti ili izbrisati.</p>
            <a class="button button-primary" href="unos.php">Dodaj vijest</a>
        </div>
        <div class="news-grid admin-grid">
            <?php while ($article = mysqli_fetch_assoc($articles)): ?>
                <article class="news-card admin-card">
                    <a href="administracija.php?id=<?= $article['id'] ?>">
                        <img src="<?= htmlspecialchars($article['slika_url'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="<?= htmlspecialchars($article['naslov'], ENT_QUOTES, 'UTF-8') ?>">
                        <p class="admin-card-meta">
                            <?= htmlspecialchars($article['kategorija'], ENT_QUOTES, 'UTF-8') ?>
                            <?= $article['arhiva'] ? ' · Arhivirano' : '' ?>
                        </p>
                        <h2><?= htmlspecialchars($article['naslov'], ENT_QUOTES, 'UTF-8') ?></h2>
                    </a>
                </article>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <section class="form-panel admin-form" aria-labelledby="edit-title">
            <div class="admin-toolbar">
                <h2 id="edit-title">Uređivanje vijesti</h2>
                <a href="administracija.php">Povratak na popis</a>
            </div>

            <form action="administracija.php?id=<?= $editArticle['id'] ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $editArticle['id'] ?>">

                <div class="form-item">
                    <label for="naslov">Naslov</label>
                    <input type="text" name="naslov" id="naslov" maxlength="100"
                           value="<?= htmlspecialchars($editArticle['naslov'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div class="form-item">
                    <label for="sazetak">Kratki sažetak</label>
                    <textarea name="sazetak" id="sazetak" rows="3" maxlength="100" required><?= htmlspecialchars($editArticle['sazetak'], ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="form-item">
                    <label for="tekst">Tekst vijesti</label>
                    <textarea name="tekst" id="tekst" rows="12" required><?= htmlspecialchars($editArticle['tekst'], ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="form-item">
                    <label for="kategorija">Kategorija</label>
                    <select name="kategorija" id="kategorija" required>
                        <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?= $category['id'] ?>" <?= (int) $editArticle['idKategorija'] === (int) $category['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['ime'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-item">
                    <label for="slika">Nova slika (nije obavezna)</label>
                    <input type="file" name="slika" id="slika" accept="image/jpeg,image/png,image/webp">
                    <img class="current-image" src="<?= htmlspecialchars($editArticle['slika_url'], ENT_QUOTES, 'UTF-8') ?>" alt="Trenutačna slika vijesti">
                </div>

                <div class="checkbox-item">
                    <input type="checkbox" name="arhiva" id="arhiva" value="1" <?= $editArticle['arhiva'] ? 'checked' : '' ?>>
                    <label for="arhiva">Spremi vijest u arhivu</label>
                </div>

                <div class="form-actions admin-actions">
                    <button type="submit" name="action" value="delete" class="button button-danger" formnovalidate>Izbriši</button>
                    <button type="reset" class="button button-secondary">Poništi promjene</button>
                    <button type="submit" name="action" value="update" class="button button-primary">Spremi promjene</button>
                </div>
            </form>
        </section>
    <?php endif; ?>
</main>

<?php require 'footer.php'; ?>
