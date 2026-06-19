# Newsweek web-portal

Projekt za predmet Programiranje web aplikacija. Završna verzija je PHP/MySQL portal s naslovnicom, kategorijama, pojedinačnim člancima, korisničkim računima i zaštićenom administracijom sadržaja.

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

1. Vratite završnu treću fazu naredbom `git checkout 9b43679`.
2. Kopirajte projekt u XAMPP direktorij `htdocs`.
3. Pokrenite Apache i MySQL servise u XAMPP Control Panelu.
4. Otvorite phpMyAdmin na `http://localhost/phpmyadmin`.
5. Odaberite karticu **Import** i uvezite datoteku `database.sql`. Skripta samostalno stvara bazu `newsweek`, tablice i početne podatke.
6. U pregledniku otvorite `http://localhost/PWA%20Web%20App/index.php` ili URL koji odgovara nazivu mape projekta.
7. Za povratak na aktualnu verziju upotrijebite `git checkout main`.

Postavke veze s bazom nalaze se u `connect.php`. Zadane vrijednosti odgovaraju standardnoj XAMPP instalaciji: poslužitelj `localhost`, korisnik `root` i prazna lozinka. Na drugom poslužitelju mogu se postaviti varijable `DB_HOST`, `DB_USER`, `DB_PASSWORD` i `DB_NAME`.

Naslovnica prikazuje tri najnovije objavljene vijesti iz svake kategorije. Stranica **Unos** dodaje novu vijest, a **Administracija** omogućuje uređivanje, arhiviranje i brisanje postojećih vijesti.

## Upute za pokretanje četvrte faze

1. Slijedite korake za treću fazu i ponovno uvezite `database.sql` kako bi se dodao administratorski račun.
2. Otvorite `index.php` i odaberite **Prijava**.
3. Za administratorski pristup koristite:

```text
Korisničko ime: admin
Lozinka: Admin123!
```

4. Nakon prijave administrator može otvoriti stranice **Unos** i **Administracija**.
5. Novi korisnici registriraju se preko stranice **Registracija** i nemaju administratorska prava.

Lozinke se u bazi spremaju kao hash, SQL upiti s korisničkim podacima koriste pripremljene izraze, a upload prihvaća samo stvarne JPEG, PNG i WebP slike do 5 MB. Za produkcijski deploy potrebno je promijeniti administratorsku lozinku i podatke za bazu u `connect.php`, uključiti HTTPS te omogućiti pisanje u direktorij `assets/uploads`.
