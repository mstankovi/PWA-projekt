CREATE DATABASE IF NOT EXISTS newsweek
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_croatian_ci;

USE newsweek;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS korisnik;
DROP TABLE IF EXISTS vijesti;
DROP TABLE IF EXISTS kategorije;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE kategorije (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ime VARCHAR(50) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_kategorije_ime (ime)
) ENGINE=InnoDB;

CREATE TABLE korisnik (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ime VARCHAR(50) NOT NULL,
    prezime VARCHAR(50) NOT NULL,
    korisnickoIme VARCHAR(50) NOT NULL,
    lozinka VARCHAR(255) NOT NULL,
    razina TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_korisnik_korisnicko_ime (korisnickoIme)
) ENGINE=InnoDB;

CREATE TABLE vijesti (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    naslov VARCHAR(100) NOT NULL,
    sazetak VARCHAR(100) NOT NULL,
    tekst TEXT NOT NULL,
    slika_url VARCHAR(255) NOT NULL,
    idKategorija INT UNSIGNED NOT NULL,
    arhiva TINYINT(1) NOT NULL DEFAULT 0,
    datum TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_vijesti_kategorija (idKategorija),
    CONSTRAINT fk_vijesti_kategorija
        FOREIGN KEY (idKategorija) REFERENCES kategorije (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO kategorije (id, ime) VALUES
    (1, 'Hrvatska'),
    (2, 'Svijet');

INSERT INTO vijesti (naslov, sazetak, tekst, slika_url, idKategorija, arhiva, datum) VALUES
    ('Obnova povijesne jezgre ulazi u novu fazu',
     'Predstavljen je novi raspored radova u povijesnoj gradskoj jezgri.',
     'Radovi na obnovi povijesne jezgre nastavljaju se prema novom rasporedu koji obuhvaća nekoliko važnih gradskih lokacija.\n\nNadležne službe predstavile su plan prema kojem će se zahvati izvoditi postupno kako bi svakodnevni život stanovnika bio što manje ometan. Posebna pozornost posvećena je sigurnosti građana i očuvanju vrijednih povijesnih obilježja.\n\nU prvoj etapi obnavljat će se pročelja i krovišta, nakon čega slijedi uređenje javnih površina.',
     'assets/img/hrvatska.png', 1, 0, '2026-06-19 09:00:00'),
    ('Gradovi predstavljaju nove planove javnog prijevoza',
     'Nove linije trebale bi bolje povezati gradska naselja i središte.',
     'Nekoliko hrvatskih gradova predstavilo je planove razvoja javnog prijevoza za sljedeće razdoblje. Projekti uključuju obnovu voznog parka, prilagodbu voznog reda i bolju povezanost udaljenijih naselja.\n\nGrađani će prijedloge moći pregledati tijekom javnog savjetovanja.',
     'assets/img/hrvatska.png', 1, 0, '2026-06-18 12:00:00'),
    ('Otvoren program potpore mladim poduzetnicima',
     'Program donosi savjetovanje i financijsku potporu novim poslovnim idejama.',
     'Otvoren je novi program potpore namijenjen mladim poduzetnicima i osobama koje tek pokreću vlastiti posao. Sudionicima će biti dostupne radionice, mentorska podrška i sredstva za razvoj početnih projekata.\n\nPrijave su otvorene do kraja mjeseca.',
     'assets/img/hrvatska.png', 1, 0, '2026-06-17 15:30:00'),
    ('Svjetski čelnici razgovaraju o energetskoj suradnji',
     'Na međunarodnom sastanku predstavljeni su novi zajednički energetski ciljevi.',
     'Predstavnici više država okupili su se na međunarodnom sastanku posvećenom energetskoj sigurnosti i suradnji. Razgovori su obuhvatili stabilnost opskrbe, razvoj infrastrukture i ulaganja u nove izvore energije.\n\nSudionici su najavili nastavak pregovora tijekom sljedećih mjeseci.',
     'assets/img/svijet.png', 2, 0, '2026-06-19 08:30:00'),
    ('Novi sporazum donosi promjene u međunarodnoj trgovini',
     'Države potpisnice postupno će uskladiti dio trgovinskih pravila.',
     'Novi međunarodni sporazum trebao bi pojednostaviti dio postupaka u trgovini između država potpisnica. Dokument određuje prijelazna razdoblja i zajednička pravila za razmjenu robe.\n\nPrimjena prvih odredbi očekuje se početkom sljedeće godine.',
     'assets/img/svijet.png', 2, 0, '2026-06-18 10:15:00'),
    ('Znanstvenici predstavili rezultate velikog istraživanja',
     'Višegodišnje istraživanje donosi nove podatke o promjenama u okolišu.',
     'Međunarodna skupina znanstvenika objavila je rezultate višegodišnjeg istraživanja provedenog na više kontinenata. Prikupljeni podaci pomoći će u boljem razumijevanju promjena u okolišu i planiranju budućih mjera.\n\nCjeloviti rezultati dostupni su stručnoj javnosti.',
     'assets/img/svijet.png', 2, 0, '2026-06-17 11:45:00');
