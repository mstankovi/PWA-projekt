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
    sazetak VARCHAR(255) NOT NULL,
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

INSERT INTO korisnik (ime, prezime, korisnickoIme, lozinka, razina) VALUES
    ('Admin', 'Newsweek', 'admin', '$2y$10$9BUxPRG9KbRVyYFdizfkoeuHJO7re9p2DjMG4Cjk2WPhNwg7kSn.K', 1);

INSERT INTO kategorije (id, ime) VALUES
    (1, 'Hrvatska'),
    (2, 'Svijet');

INSERT INTO vijesti (naslov, sazetak, tekst, slika_url, idKategorija, arhiva, datum) VALUES
    ('Plenki dobio avion!',
     'Nakon čak 2 dana čekanja, premijer je konačno dobio svoj avion.',
     'Postupak dodjele državnog aviončića predsjedniku Vlade uspješno je okončan nakon rekordno kratkog razdoblja čekanja od svega dva dana.
      Nadležne službe istaknule su kako je cijeli proces protekao uz visoku razinu strpljenja, državničke sabranosti i povremenog pogledavanja prema nebu.
       Posebna pozornost posvećena je tome da aviončić bude prikladan za ozbiljne obveze, ali i dovoljno simpatičan da se može parkirati uz osmijeh. U prvoj etapi provedeno je svečano čekanje, nakon čega je uslijedilo zadovoljno preuzimanje letjelice i kratka izjava da se reforme sada mogu odvijati još brže.',
     'assets/img/plenkijev_avion.jpg', 1, 0, '2026-06-19 09:00:00'),
    ('Radovi u Zagebu traju i dalje...',
     'Nove linije trebale bi bolje povezati gradska naselja i središte.',
     'Zagreb je predstavio novi raspored radova prema kojem će se promet odvijati u skladu s tradicijom iznenađenja, obilazaka i povremenog pitanja građana jesu li uopće krenuli pravim putem. Plan uključuje otvaranje dodatnih rupa, premještanje postojećih gužvi i bolju povezanost semafora koji ionako već dugo razgovaraju sami sa sobom.
      Građani će sve izmjene moći proučiti izravno na terenu, najčešće u koloni, uz mogućnost javnog savjetovanja s najbližim vozačem kroz spušteni prozor.',
     'assets/img/radoviZG.jpg', 1, 0, '2026-06-18 12:00:00'),
    ('Plenkoviću stiže novi avion',
     'U zamjenu za iseljavanje mladih, obrazovanih i sposobnih, premijeru je obećan novi aviončić.',
     'U sklopu novog paketa demografskih mjera, javnosti je predstavljen simboličan aranžman prema kojem se odlazak mladih, obrazovanih i sposobnih građana nadomješta isporukom novog aviončića premijeru. Nadležne službe istaknule su kako je riječ o modernom rješenju koje uspješno povezuje prazne učionice, nepopunjena radna mjesta i visoke državničke ambicije.
      Posebna pozornost posvećena je održivosti projekta, budući da se svakim novim odlaskom rasterećuje sustav, smanjuju očekivanja i otvara dodatni prostor za svečano polijetanje.',
     'assets/img/ponudili_mu_avion.jpg', 1, 0, '2026-06-17 15:30:00'),
    ('Novosti iz JANAF-a',
     'Ko tu koga krade, ko tu koga vara, tko tu koga vara, a tko tu koga krade?',
     'JANAF je javnosti predstavio novi strateški plan prema kojem će se naftovodi nastaviti kretati u smjeru stabilnosti, odgovornosti i blagog iznenađenja kad god netko pogleda poslovne rezultate.
      Iz Uprave poručuju kako sustav radi uredno, cijevi su pod kontrolom, a sve odluke teku prema planu — uz napomenu da se u kompaniji i dalje njeguje tradicionalna hrvatska vrijednost: da nitko izvana nije potpuno siguran što se unutra događa.',
     'assets/img/foiled_again.jpg', 1, 0, '2026-06-16 15:30:00'),
    ('TVZ Summer Break 2026 ruši sve rekorde',
     'U usporedbi s prošlom godinom, ovogodišnji Summer Break donio je više posjetitelja, više sunca i više zabave.',
     'Posebnu pozornost izazvao je dolazak studenata FER-a, koji su se, prema riječima organizatora, najprije pojavili “samo da vide o čemu se radi”, a zatim ostali dovoljno dugo da neformalno priznaju kako TVZ ipak ima bolju atmosferu, bolji tempo i znatno prihvatljiviju razinu životne radosti.
      Iako službena potvrda još nije stigla, nekoliko svjedoka tvrdi da su ferovci pri odlasku tiho zaključili kako je TVZ možda ipak bolji faks.',
     'assets/img/tvz-summer-break-party.jpg', 1, 0, '2026-06-15 15:30:00'),
    ('Zagrebačka filharmonija svirala bivšem austrijskom premijeru!',
     'Zagrebačka filharmonija održala je svečani nastup za Sebastiana Kurza. Kulturna diplomacija dosegnula novu razinu tišine, gudača i pristojnog kimanja glavom.',
     'Prema riječima prisutnih, program je bio pažljivo odabran kako bi se spojili ozbiljnost europske politike i bogata domaća tradicija komentiranja svega i svačega.
       Bivšem premijeru navodno nije bilo posve jasno zašto se dio publike, osobito oni koji dobro poznaju hrvatski jezik i narodne izraze, tijekom najave nastupa diskretno smijuljio. Organizatori su samo kratko poručili da je riječ o “kulturnoj nijansi koju je teško prevesti”.',
     'assets/img/Sebastian.jpg', 2, 0, '2026-06-19 08:30:00'),
    ('Školsko nasilje! Učiteljica šamarala učenika!',
     'Prva dama Francuske ponovno "disciplinirala" bivšeg učenika i muža u javnosti!',
     'Javnost je ponovno svjedočila neobičnoj pedagoškoj metodi prve dame Francuske, koja je u avionu, prema svemu sudeći, odlučila kratko podsjetiti bivšeg učenika i sadašnjeg muža na osnove lijepog ponašanja.
      Iako iz Elizejske palače tvrde da je riječ o “privatnom trenutku u javnom prostoru”, promatrači ističu kako je disciplina bila brza, precizna i provedena bez prethodne najave roditeljskog sastanka.',
     'assets/img/macron.webp', 2, 0, '2026-06-18 10:15:00'),
    ('Trump izjavio da je TVZ najbolji faks na svijetu!',
     'Predsjednik SAD-a Donald Trump pohvalio je TVZ i istaknuo kako je riječ o “najboljem fakultetu na svijetu”.',
     'Trump je u svom prepoznatljivom tonu poručio kako je Amerika najbolja država na svijetu, a TVZ najbolji faks, dodavši da “svi to znaju, samo neki još nisu imali hrabrosti priznati”.
      Nakon snažne izjave, sastanak je nastavljen u mirnijem ritmu jer je predsjednik, zadovoljan vlastitim zaključkom, navodno zaspao prije nego što je itko stigao postaviti prvo pitanje.',
     'assets/img/Trump_stamp_of_approval.webp', 2, 0, '2026-06-17 11:45:00'),
    ('Borat izabran za predsjednika Kazahstana!',
     'Nakon duge borbe s osebujnom osobnošću, Borat je izabran za predsjednika Kazahstana.',
     'Borat je nakon napete i potpuno očekivano neobične kampanje izabran za predsjednika Kazahstana, uz obećanje da će zemlju voditi “vrlo lijepo” i uz maksimalnu količinu svečanih mahanja.
      U prvom obraćanju zahvalio je narodu, sebi i svim slučajnim prolaznicima, poručivši da Kazahstan ulazi u novo doba demokracije, tradicije i međunarodnog zbunjivanja.',
     'assets/img/Borat_predsjednik.webp', 2, 0, '2026-06-16 11:45:00'),
    ('Mr. Worldwide ide na svjetsku turneju!',
     'Poznati glazbenik Pitbull najavio je svjetsku turneju koja će obuhvatiti više od 50 zemalja.',
     'Pitbull, poznatiji kao Mr. Worldwide, najavio je veliku svjetsku turneju koja će obuhvatiti više od 50 zemalja, nekoliko kontinenata i neograničenu količinu sunčanih naočala u zatvorenom prostoru.
      Organizatori poručuju da su ulaznice dostupne svima, ali pravi fanovi imat će poseban zadatak: na koncert doći ošišani na ćelavo, kako bi publika napokon izgledala jednako svjetski, sjajno i aerodinamično kao njihov idol.',
     'assets/img/mr.worldwide.jpg', 2, 0, '2026-06-15 11:45:00');
