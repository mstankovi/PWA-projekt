<?php
function spremiUploadanuSliku(array $datoteka, string &$greska): ?string
{
    if (($datoteka['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $greska = 'Slika nije uspješno prenesena.';
        return null;
    }

    if (($datoteka['size'] ?? 0) > 5 * 1024 * 1024) {
        $greska = 'Slika ne smije biti veća od 5 MB.';
        return null;
    }

    if (!is_uploaded_file($datoteka['tmp_name'])) {
        $greska = 'Odabrana datoteka nije valjan upload.';
        return null;
    }

    $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($datoteka['tmp_name']);
    $dozvoljeniTipovi = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($dozvoljeniTipovi[$mimeType])) {
        $greska = 'Dozvoljene su samo JPEG, PNG i WebP slike.';
        return null;
    }

    $uploadDirektorij = __DIR__ . '/assets/uploads/';
    if (!is_dir($uploadDirektorij) && !mkdir($uploadDirektorij, 0755, true)) {
        $greska = 'Direktorij za slike nije moguće pripremiti.';
        return null;
    }

    $nazivDatoteke = bin2hex(random_bytes(16)) . '.' . $dozvoljeniTipovi[$mimeType];
    $odrediste = $uploadDirektorij . $nazivDatoteke;

    if (!move_uploaded_file($datoteka['tmp_name'], $odrediste)) {
        $greska = 'Slika nije uspješno spremljena.';
        return null;
    }

    return 'assets/uploads/' . $nazivDatoteke;
}

function obrisiUploadanuSliku(string $putanja): void
{
    $uploadDirektorij = realpath(__DIR__ . '/assets/uploads');
    $datoteka = realpath(__DIR__ . '/' . $putanja);

    if ($uploadDirektorij && $datoteka && str_starts_with($datoteka, $uploadDirektorij . DIRECTORY_SEPARATOR)) {
        unlink($datoteka);
    }
}
