<?php
require_once 'connect.php';

$categoryId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$categoryId) {
    header('Location: index.php');
    exit;
}

$categoryStatement = mysqli_prepare($dbc, 'SELECT ime FROM kategorije WHERE id = ?');
mysqli_stmt_bind_param($categoryStatement, 'i', $categoryId);
mysqli_stmt_execute($categoryStatement);
$categoryResult = mysqli_stmt_get_result($categoryStatement);
$category = mysqli_fetch_assoc($categoryResult);

if (!$category) {
    header('Location: index.php');
    exit;
}

$pageTitle = $category['ime'];
require 'header.php';

$articleStatement = mysqli_prepare(
    $dbc,
    'SELECT id, naslov, slika_url, datum
     FROM vijesti
     WHERE idKategorija = ? AND arhiva = 0
     ORDER BY datum DESC'
);
mysqli_stmt_bind_param($articleStatement, 'i', $categoryId);
mysqli_stmt_execute($articleStatement);
$articles = mysqli_stmt_get_result($articleStatement);
?>
<main class="home-content">
    <section class="news-section category-page" aria-labelledby="category-title">
        <h1 id="category-title"><?= htmlspecialchars($category['ime'], ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="news-grid">
            <?php if (mysqli_num_rows($articles) === 0): ?>
                <p class="empty-message">U ovoj kategoriji trenutačno nema objavljenih vijesti.</p>
            <?php endif; ?>

            <?php while ($article = mysqli_fetch_assoc($articles)): ?>
                <article class="news-card">
                    <a href="clanak.php?id=<?= $article['id'] ?>">
                        <img src="<?= htmlspecialchars($article['slika_url'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="<?= htmlspecialchars($article['naslov'], ENT_QUOTES, 'UTF-8') ?>">
                        <h2><?= htmlspecialchars($article['naslov'], ENT_QUOTES, 'UTF-8') ?></h2>
                        <time datetime="<?= date('Y-m-d', strtotime($article['datum'])) ?>">
                            <?= date('d. m. Y.', strtotime($article['datum'])) ?>
                        </time>
                    </a>
                </article>
            <?php endwhile; ?>
        </div>
    </section>
</main>

<?php require 'footer.php'; ?>
