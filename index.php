<?php
$pageTitle = 'Početna';
require 'header.php';

$categories = mysqli_query($dbc, 'SELECT id, ime FROM kategorije ORDER BY id');
?>
<main class="home-content">
    <h1 class="visually-hidden">Najnovije vijesti iz Hrvatske i svijeta</h1>

    <?php while ($category = mysqli_fetch_assoc($categories)): ?>
        <?php
        $articleQuery = 'SELECT id, naslov, slika_url
                         FROM vijesti
                         WHERE idKategorija = ? AND arhiva = 0
                         ORDER BY datum DESC
                         LIMIT 3';
        $articleStatement = mysqli_prepare($dbc, $articleQuery);
        mysqli_stmt_bind_param($articleStatement, 'i', $category['id']);
        mysqli_stmt_execute($articleStatement);
        $articles = mysqli_stmt_get_result($articleStatement);
        ?>
        <section class="news-section" aria-labelledby="category-<?= $category['id'] ?>">
            <h2 id="category-<?= $category['id'] ?>">
                <a href="kategorija.php?id=<?= $category['id'] ?>">
                    <?= htmlspecialchars($category['ime'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </h2>
            <div class="news-grid">
                <?php while ($article = mysqli_fetch_assoc($articles)): ?>
                    <article class="news-card">
                        <a href="clanak.php?id=<?= $article['id'] ?>">
                            <img src="<?= htmlspecialchars($article['slika_url'], ENT_QUOTES, 'UTF-8') ?>"
                                 alt="<?= htmlspecialchars($article['naslov'], ENT_QUOTES, 'UTF-8') ?>">
                            <h3><?= htmlspecialchars($article['naslov'], ENT_QUOTES, 'UTF-8') ?></h3>
                        </a>
                    </article>
                <?php endwhile; ?>
            </div>
        </section>
        <?php mysqli_stmt_close($articleStatement); ?>
    <?php endwhile; ?>
</main>

<?php require 'footer.php'; ?>
