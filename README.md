# Newsweek web-portal

Projekt za predmet Programiranje web aplikacija. Trenutačna verzija je PHP/MySQL portal s naslovnicom, kategorijama, pojedinačnim člancima, unosom vijesti i administracijom sadržaja.

## Upute za pokretanje prve faze

1. Vratite statičnu verziju naredbom `git checkout faza-1`.
2. Otvorite datoteku `index.html` u web-pregledniku.
3. Klikom na naslov vijesti otvorit će se stranica `clanak.html`.
4. Za povratak na aktualnu verziju upotrijebite `git checkout main`.

## Upute za pokretanje druge faze

1. Vratite drugu fazu naredbom `git checkout d735d58`.
2. Kopirajte projekt u XAMPP direktorij `htdocs`.
3. Pokrenite Apache servis u XAMPP Control Panelu.
4. U pregledniku otvorite `http://localhost/PWA%20Web%20App/unos.html` ili URL koji odgovara nazivu mape projekta.
5. Ispunite sva polja, odaberite JPEG, PNG ili WebP sliku do 5 MB i kliknite **Prihvati**.
6. Za povratak na aktualnu verziju upotrijebite `git checkout main`.

## Upute za pokretanje treće faze

1. Kopirajte projekt u XAMPP direktorij `htdocs`.
2. Pokrenite Apache i MySQL servise u XAMPP Control Panelu.
3. Otvorite phpMyAdmin na `http://localhost/phpmyadmin`.
4. Odaberite karticu **Import** i uvezite datoteku `database.sql`. Skripta samostalno stvara bazu `newsweek`, tablice i početne podatke.
5. U pregledniku otvorite `http://localhost/PWA%20Web%20App/index.php` ili URL koji odgovara nazivu mape projekta.

Postavke veze s bazom nalaze se u `connect.php`. Zadane vrijednosti odgovaraju standardnoj XAMPP instalaciji: poslužitelj `localhost`, korisnik `root` i prazna lozinka.

Naslovnica prikazuje tri najnovije objavljene vijesti iz svake kategorije. Stranica **Unos** dodaje novu vijest, a **Administracija** omogućuje uređivanje, arhiviranje i brisanje postojećih vijesti. Prijava i zaštita administracije dodaju se u četvrtoj fazi.
