<?php declare(strict_types = 1);

/**
 * Artikli model
 * @since 0.1.1.pre-alpha.M1
 *
 * @author Danijel Galić
 * @copyright 2022 Kapriol Web Trgovina
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Aplikacija\Model
 */

namespace FireHub\Aplikacija\Kapriol\Model;

use FireHub\Jezgra\Komponente\BazaPodataka\BazaPodataka;
use FireHub\Aplikacija\Kapriol\Jezgra\Domena;
use FireHub\Jezgra\Kontejner\Greske\Kontejner_Greska;

/**
 * ### Artikli model
 * @since 0.1.1.pre-alpha.M1
 *
 * @package Aplikacija\Model
 */
final class Artikli_Model extends Master_Model {

    /**
     * ### Konstruktor
     * @since 0.1.1.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Sesije.
     */
    public function __construct (
        private BazaPodataka $bazaPodataka
    ) {

        parent::__construct();

    }

    /**
     * ### Dohvati artikle
     * @since 0.1.1.pre-alpha.M1
     *
     * @param int|string $kategorija <p>
     * ID kategorije.
     * </p>
     * @param int|string $podkategorija <p>
     * ID podkategorije.
     * </p>
     * @param int $pomak <p>
     * Pomak od kojeg se limitiraju zapisi.
     * </p>
     * @param int $limit <p>
     * Broj redaka koje odabiremo.
     * </p>
     * @param int|string $velicina <p>
     * Veličina artikla.
     * </p>
     * @param int|string $trazi <p>
     * Traži artikl.
     * </p>
     * @param string $poredaj <p>
     * Poredaj rezultate artikala.
     * </p>
     * @param string $poredaj_redoslijed <p>
     * ASC ili DESC.
     * </p>
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return array Niz artikala.
     */
    public function artikli (int|string $kategorija, int|string $podkategorija, int $pomak, int $limit, int|string $velicina, int|string $trazi, string $poredaj, string $poredaj_redoslijed):array {

        if ($kategorija === 'izdvojeno') {

            $artikli = $this->bazaPodataka->tabela('artikliview')
                ->sirovi("
                    SELECT
                       artikliview.ID, Naziv, Link, Opis, ".Domena::sqlCijena()." AS Cijena, ".Domena::sqlCijenaAkcija()." AS CijenaAkcija,
                       IF(".Domena::sqlCijenaAkcija()." > 0, ".Domena::sqlCijenaAkcija().", ".Domena::sqlCijena().") AS Cijenafinal,
                       GROUP_CONCAT(DISTINCT artiklikarakteristike.Velicina) AS Velicine,
                       (SELECT Slika FROM slikeartikal WHERE slikeartikal.ClanakID = artikliview.ID ORDER BY slikeartikal.Zadana DESC LIMIT 1) AS Slika,
                       ".(Domena::Hr() ? 'artikliview.GratisHr' : 'artikliview.GratisBa')." AS GratisID,
                       artikliview.Novo,
                       artikliview.Cijena30Dana".Domena::sqlTablica()." AS Cijena30Dana
                    FROM artikliview
                    LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikliview.ID
                    WHERE Aktivan = 1 AND ".Domena::sqlTablica()." = 1 AND Izdvojeno = 1 AND ".Domena::sqlCijena()." > 0
                    GROUP BY artikliview.ID
                    ORDER BY ".ucwords($poredaj)." $poredaj_redoslijed
                    LIMIT 12
                ")
                ->napravi();

        } else if ($kategorija === 'sve') {

            $artikli = $this->bazaPodataka->tabela('artikliview')
                ->sirovi("
                    SELECT
                       artikliview.ID, Naziv, Link, Opis, ".Domena::sqlCijena()." AS Cijena, ".Domena::sqlCijenaAkcija()." AS CijenaAkcija,
                       IF(".Domena::sqlCijenaAkcija()." > 0, ".Domena::sqlCijenaAkcija().", ".Domena::sqlCijena().") AS Cijenafinal,
                       GROUP_CONCAT(DISTINCT artiklikarakteristike.Velicina) AS Velicine,
                       (SELECT Slika FROM slikeartikal WHERE slikeartikal.ClanakID = artikliview.ID ORDER BY slikeartikal.Zadana DESC LIMIT 1) AS Slika,
                       ".(Domena::Hr() ? 'artikliview.GratisHr' : 'artikliview.GratisBa')." AS GratisID,
                       artikliview.Novo,
                       artikliview.Cijena30Dana".Domena::sqlTablica()." AS Cijena30Dana
                    FROM artikliview
                    LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikliview.ID
                    WHERE Aktivan = 1 AND ".Domena::sqlTablica()." = 1 AND ".Domena::sqlCijena()." > 0
                    {$this->trazi($trazi)}
                    GROUP BY artikliview.ID
                    {$this->velicineUpit($velicina)}
                    ORDER BY ".ucwords($poredaj)." $poredaj_redoslijed
                    LIMIT $pomak, $limit
                ")
                ->napravi();

        } else if ($kategorija === 'akcija') {

            $artikli = $this->bazaPodataka->tabela('artikliview')
                ->sirovi("
                    SELECT
                       artikliview.ID, Naziv, Link, Opis, ".Domena::sqlCijena()." AS Cijena, ".Domena::sqlCijenaAkcija()." AS CijenaAkcija,
                       IF(".Domena::sqlCijenaAkcija()." > 0, ".Domena::sqlCijenaAkcija().", ".Domena::sqlCijena().") AS Cijenafinal,
                       GROUP_CONCAT(DISTINCT artiklikarakteristike.Velicina) AS Velicine,
                       (SELECT Slika FROM slikeartikal WHERE slikeartikal.ClanakID = artikliview.ID ORDER BY slikeartikal.Zadana DESC LIMIT 1) AS Slika,
                       ".(Domena::Hr() ? 'artikliview.GratisHr' : 'artikliview.GratisBa')." AS GratisID,
                       artikliview.Novo,
                       artikliview.Cijena30Dana".Domena::sqlTablica()." AS Cijena30Dana
                    FROM artikliview
                    LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikliview.ID
                    WHERE Aktivan = 1 AND ".Domena::sqlTablica()." = 1 AND ".Domena::sqlCijenaAkcija()." > 0 AND ".Domena::sqlCijena()." > 0
                    {$this->trazi($trazi)}
                    GROUP BY artikliview.ID
                    {$this->velicineUpit($velicina)}
                    ORDER BY ".ucwords($poredaj)." $poredaj_redoslijed
                    LIMIT $pomak, $limit
                ")
                ->napravi();

        } else if ($kategorija === 'outlet') {

            $artikli = $this->bazaPodataka->tabela('artikliview')
                ->sirovi("
                    SELECT
                       artikliview.ID, Naziv, Link, Opis, ".Domena::sqlCijena()." AS Cijena, ".Domena::sqlCijenaAkcija()." AS CijenaAkcija,
                       IF(".Domena::sqlCijenaAkcija()." > 0, ".Domena::sqlCijenaAkcija().", ".Domena::sqlCijena().") AS Cijenafinal,
                       GROUP_CONCAT(DISTINCT artiklikarakteristike.Velicina) AS Velicine,
                       (SELECT Slika FROM slikeartikal WHERE slikeartikal.ClanakID = artikliview.ID ORDER BY slikeartikal.Zadana DESC LIMIT 1) AS Slika,
                       ".(Domena::Hr() ? 'artikliview.GratisHr' : 'artikliview.GratisBa')." AS GratisID,
                       artikliview.Novo,
                       artikliview.Cijena30Dana".Domena::sqlTablica()." AS Cijena30Dana
                    FROM artikliview
                    LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikliview.ID
                    WHERE Aktivan = 1 AND ".Domena::sqlTablica()." = 1 AND ".Domena::sqlCijena()." > 0 AND ".Domena::sqlOutlet()." = 1
                    {$this->trazi($trazi)}
                    GROUP BY artikliview.ID
                    {$this->velicineUpit($velicina)}
                    ORDER BY ".ucwords($poredaj)." $poredaj_redoslijed
                    LIMIT $pomak, $limit
                ")
                ->napravi();

        } else if ($kategorija === 'novo') {

            $artikli = $this->bazaPodataka->tabela('artikliview')
                ->sirovi("
                    SELECT
                       artikliview.ID, Naziv, Link, Opis, ".Domena::sqlCijena()." AS Cijena, ".Domena::sqlCijenaAkcija()." AS CijenaAkcija,
                       IF(".Domena::sqlCijenaAkcija()." > 0, ".Domena::sqlCijenaAkcija().", ".Domena::sqlCijena().") AS Cijenafinal,
                       GROUP_CONCAT(DISTINCT artiklikarakteristike.Velicina) AS Velicine,
                       (SELECT Slika FROM slikeartikal WHERE slikeartikal.ClanakID = artikliview.ID ORDER BY slikeartikal.Zadana DESC LIMIT 1) AS Slika,
                       ".(Domena::Hr() ? 'artikliview.GratisHr' : 'artikliview.GratisBa')." AS GratisID,
                       artikliview.Novo,
                       artikliview.Cijena30Dana".Domena::sqlTablica()." AS Cijena30Dana
                    FROM artikliview
                    LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikliview.ID
                    WHERE Aktivan = 1 AND ".Domena::sqlTablica()." = 1 AND ".Domena::sqlCijena()." > 0 AND Novo = 1
                    {$this->trazi($trazi)}
                    GROUP BY artikliview.ID
                    {$this->velicineUpit($velicina)}
                    ORDER BY ".ucwords($poredaj)." $poredaj_redoslijed
                    LIMIT $pomak, $limit
                ")
                ->napravi();

        } else if ($podkategorija === 'sve') {

            $artikli = $this->bazaPodataka->tabela('artikliview')
                ->sirovi("
                SELECT
                    artikliview.ID, Naziv, Link, Opis, ".Domena::sqlCijena()." AS Cijena, ".Domena::sqlCijenaAkcija()." AS CijenaAkcija,
                    IF(".Domena::sqlCijenaAkcija()." > 0, ".Domena::sqlCijenaAkcija().", ".Domena::sqlCijena().") AS Cijenafinal,
                    GROUP_CONCAT(DISTINCT artiklikarakteristike.Velicina) AS Velicine,
                    (SELECT Slika FROM slikeartikal WHERE slikeartikal.ClanakID = artikliview.ID ORDER BY slikeartikal.Zadana DESC LIMIT 1) AS Slika,
                    ".(Domena::Hr() ? 'artikliview.GratisHr' : 'artikliview.GratisBa')." AS GratisID,
                    artikliview.Novo,
                    artikliview.Cijena30Dana".Domena::sqlTablica()." AS Cijena30Dana
                FROM artikliview
                LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikliview.ID
                WHERE KategorijaID = '$kategorija' AND Aktivan = 1 AND ".Domena::sqlTablica()." = 1 AND ".Domena::sqlCijena()." > 0
                {$this->trazi($trazi)}
                GROUP BY artikliview.ID
                {$this->velicineUpit($velicina)}
                ORDER BY ".ucwords($poredaj)." $poredaj_redoslijed
                LIMIT $pomak, $limit
            ")
                ->napravi();

        } else {

            $artikli = $this->bazaPodataka->tabela('artikliview')
                ->sirovi("
                SELECT
                    artikliview.ID, Naziv, Link, Opis, ".Domena::sqlCijena()." AS Cijena, ".Domena::sqlCijenaAkcija()." AS CijenaAkcija,
                    IF(".Domena::sqlCijenaAkcija()." > 0, ".Domena::sqlCijenaAkcija().", ".Domena::sqlCijena().") AS Cijenafinal,
                    GROUP_CONCAT(DISTINCT artiklikarakteristike.Velicina) AS Velicine,
                    (SELECT Slika FROM slikeartikal WHERE slikeartikal.ClanakID = artikliview.ID ORDER BY slikeartikal.Zadana DESC LIMIT 1) AS Slika,
                    ".(Domena::Hr() ? 'artikliview.GratisHr' : 'artikliview.GratisBa')." AS GratisID,
                    artikliview.Novo,
                    artikliview.Cijena30Dana".Domena::sqlTablica()." AS Cijena30Dana
                FROM artikliview
                LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikliview.ID
                WHERE KategorijaID = '$kategorija' AND PodKategorijaID = '$podkategorija' AND Aktivan = 1 AND ".Domena::sqlTablica()." = 1 AND ".Domena::sqlCijena()." > 0
                {$this->trazi($trazi)}
                GROUP BY artikliview.ID
                {$this->velicineUpit($velicina)}
                ORDER BY ".ucwords($poredaj)." $poredaj_redoslijed
                LIMIT $pomak, $limit
            ")
            ->napravi();

        }

        return $artikli->niz() ?: [];

    }

    /**
     * ### Izdvojeni artikle
     * @since 0.1.1.pre-alpha.M1
     *
     * @param int|string $kategorija <p>
     * ID kategorije.
     * </p>
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return array Niz artikala.
     */
    public function izdvojeno (int|string $kategorija) {

        if ($kategorija === 'akcija' || $kategorija === 'sve') {

            return [];

        }

        $artikli = $this->bazaPodataka->tabela('artikliview')
            ->sirovi("
                    SELECT
                       artikliview.ID, Naziv, Link, Opis, ".Domena::sqlCijena()." AS Cijena, ".Domena::sqlCijenaAkcija()." AS CijenaAkcija, Slika,
                       GROUP_CONCAT(DISTINCT artiklikarakteristike.Velicina) AS Velicine
                    FROM artikliview
                    LEFT JOIN slikeartikal ON ClanakID = artikliview.ID
                    LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikliview.ID
                    WHERE KategorijaID = $kategorija AND Aktivan = 1 AND Izdvojeno = 1 AND ".Domena::sqlTablica()." = 1 AND Zadana = 1
                    GROUP BY artikliview.ID
                    LIMIT 2
                ")
            ->napravi();

        return $artikli->niz() ?: [];

    }

    /**
     * ### Pronađi veličine artilala
     * @since 0.1.2.pre-alpha.M1
     *
     * @param int|string $kategorija <p>
     * ID kategorije.
     * </p>
     * @param int|string $trazi <p>
     * Traži artikl.
     * </p>
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return array Pronađene veličine.
     */
    public function velicine (int|string $kategorija, int|string $trazi):array {

        if ($kategorija === 'sve') {

            $artikli = $this->bazaPodataka->tabela('artiklikarakteristike')
                ->sirovi("
                SELECT
                    artiklikarakteristike.Velicina
                FROM artiklikarakteristike
                LEFT JOIN artikliview ON artikliview.ID = artiklikarakteristike.ArtikalID
                WHERE Aktivan = 1 AND ".Domena::sqlTablica()." = 1
                {$this->trazi($trazi)}
                GROUP BY artiklikarakteristike.Velicina
                ORDER BY artiklikarakteristike.Velicina
            ")
            ->napravi();

            return $artikli->niz() ?: [];

        } else if ($kategorija === 'akcija') {

            $artikli = $this->bazaPodataka->tabela('artiklikarakteristike')
                ->sirovi("
                SELECT
                    artiklikarakteristike.Velicina
                FROM artiklikarakteristike
                LEFT JOIN artikliview ON artikliview.ID = artiklikarakteristike.ArtikalID
                WHERE Aktivan = 1 AND ".Domena::sqlTablica()." = 1 AND ".Domena::sqlCijenaAkcija()." > 0
                GROUP BY artiklikarakteristike.Velicina
                ORDER BY artiklikarakteristike.Velicina
            ")
            ->napravi();

            return $artikli->niz() ?: [];

        }

        $artikli = $this->bazaPodataka->tabela('artiklikarakteristike')
            ->sirovi("
                SELECT
                    artiklikarakteristike.Velicina
                FROM artiklikarakteristike
                LEFT JOIN artikliview ON artikliview.ID = artiklikarakteristike.ArtikalID
                WHERE artikliview.KategorijaID = $kategorija AND Aktivan = 1 AND ".Domena::sqlTablica()." = 1
                GROUP BY artiklikarakteristike.Velicina
                ORDER BY artiklikarakteristike.Velicina
            ")
            ->napravi();

        return $artikli->niz() ?: [];

    }

    /**
     * ### Ukupnjo pronađenih redaka
     * @since 0.1.1.pre-alpha.M1
     *
     * @param int|string $kategorija <p>
     * ID kategorije.
     * </p>
     * @param int|string $podkategorija <p>
     * Podkategorija artikla.
     * </p>
     * @param int|string $velicina <p>
     * Veličina artikla.
     * </p>
     * @param int|string $trazi <p>
     * Traži artikl.
     * </p>
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return int Broj pronađenih redaka.
     */
    public function ukupnoRedaka (int|string $kategorija, int|string $podkategorija, int|string $velicina, int|string $trazi) {

        if ($kategorija === 'sve') {

            $ukupno_redaka = $this->bazaPodataka->tabela('artikliview')
                ->sirovi("
                SELECT Naziv, GROUP_CONCAT(DISTINCT artiklikarakteristike.Velicina) AS Velicine
                FROM artikliview
                LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikliview.ID
                WHERE Aktivan = 1 AND ".Domena::sqlTablica()." = 1 AND ".Domena::sqlCijena()." > 0
                {$this->trazi($trazi)}
                GROUP BY artikliview.ID
                {$this->velicineUpit($velicina)}
            ")
                ->napravi();

            return $ukupno_redaka->broj_zapisa();

        } else if ($kategorija === 'akcija') {

            $ukupno_redaka = $this->bazaPodataka->tabela('artikliview')
                ->sirovi("
                SELECT Naziv, GROUP_CONCAT(DISTINCT artiklikarakteristike.Velicina) AS Velicine
                FROM artikliview
                LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikliview.ID
                WHERE Aktivan = 1 AND ".Domena::sqlTablica()." = 1 AND ".Domena::sqlCijenaAkcija()." > 1 AND ".Domena::sqlCijena()." > 0
                {$this->trazi($trazi)}
                GROUP BY artikliview.ID
                {$this->velicineUpit($velicina)}
            ")
                ->napravi();

            return $ukupno_redaka->broj_zapisa();

        } else if ($kategorija === 'outlet') {

            $ukupno_redaka = $this->bazaPodataka->tabela('artikliview')
                ->sirovi("
                SELECT Naziv, GROUP_CONCAT(DISTINCT artiklikarakteristike.Velicina) AS Velicine
                FROM artikliview
                LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikliview.ID
                WHERE Aktivan = 1 AND ".Domena::sqlTablica()." = 1 AND ".Domena::sqlCijena()." > 0 AND ".Domena::sqlOutlet()." = 1
                {$this->trazi($trazi)}
                GROUP BY artikliview.ID
                {$this->velicineUpit($velicina)}
                ")
                ->napravi();

            return $ukupno_redaka->broj_zapisa();

        } else if ($kategorija === 'novo') {

            $ukupno_redaka = $this->bazaPodataka->tabela('artikliview')
                ->sirovi("
                SELECT Naziv, GROUP_CONCAT(DISTINCT artiklikarakteristike.Velicina) AS Velicine
                FROM artikliview
                LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikliview.ID
                WHERE Aktivan = 1 AND ".Domena::sqlTablica()." = 1 AND ".Domena::sqlCijena()." > 0 AND Novo = 1
                {$this->trazi($trazi)}
                GROUP BY artikliview.ID
                {$this->velicineUpit($velicina)}
                ")
                ->napravi();

            return $ukupno_redaka->broj_zapisa();

        }

        if ($podkategorija === 'sve') {

            $ukupno_redaka = $this->bazaPodataka->tabela('artikliview')
                ->sirovi("
                SELECT Naziv, GROUP_CONCAT(DISTINCT artiklikarakteristike.Velicina) AS Velicine
                FROM artikliview
                LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikliview.ID
                WHERE KategorijaID = $kategorija AND Aktivan = 1 AND ".Domena::sqlTablica()." = 1 AND ".Domena::sqlCijena()." > 0
                {$this->trazi($trazi)}
                GROUP BY artikliview.ID
                {$this->velicineUpit($velicina)}
            ")
                ->napravi();

        } else {

            $ukupno_redaka = $this->bazaPodataka->tabela('artikliview')
                ->sirovi("
                SELECT Naziv, GROUP_CONCAT(DISTINCT artiklikarakteristike.Velicina) AS Velicine
                FROM artikliview
                LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikliview.ID
                WHERE KategorijaID = '$kategorija' AND PodKategorijaID = '$podkategorija' AND Aktivan = 1 AND ".Domena::sqlTablica()." = 1 AND ".Domena::sqlCijena()." > 0
                {$this->trazi($trazi)}
                GROUP BY artikliview.ID
                {$this->velicineUpit($velicina)}
            ")
                ->napravi();

        }

        return $ukupno_redaka->broj_zapisa();

    }

    /**
     * ### Navigacija HTML
     * @since 0.1.1.pre-alpha.M1
     *
     * @param int|string $kategorija <p>
     * Kategorija artikla.
     * </p>
     * @param int|string $podkategorija <p>
     * Podkategorija artikla.
     * </p>
     * @param int|string $velicina <p>
     * Veličina artikla.
     * </p>
     * @param int|string $trazi <p>
     * Traži artikl.
     * </p>
     * @param int $limit <p>
     * Limit artikala.
     * </p>
     * @param string $url <p>
     * Trenutni URL.
     * </p>
     * @param int $broj_stranice <p>
     * Trenutnu broj stranice.
     * </p>
     * @param string $boja <p>
     * Boja gumbova.
     * </p>
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return string[] Lista artikala.
     */
    public function ukupnoRedakaHTML (int|string $kategorija, int|string $podkategorija, int|string $velicina, int|string $trazi, int $limit, string $url = '/', int $broj_stranice = 1, string $boja = 'boja'):array {

        $broj_zapisa = $this->ukupnoRedaka($kategorija, $podkategorija, $velicina, $trazi);

        $pocetak_link_stranice = "";
        $link_stranice = "";
        $kraj_link_stranice = "";

        $ukupno_stranica = ceil($broj_zapisa / $limit);
        if (($broj_stranice - 2) < 1) {$x = 1;} else {$x = ($broj_stranice - 2);}
        if (($broj_stranice + 2) >= $ukupno_stranica) {$y = $ukupno_stranica;} else {$y = ($broj_stranice + 2);}

        if ($broj_stranice >= 2) {

            $prosla_stranica = $broj_stranice - 1;

            if ($url) {$url_prva_stranica = "href='{$url}/1'";} else {$url_prva_stranica = "";}
            if ($url) {$url_prosla_stranica = "href='{$url}/{$prosla_stranica}'";} else {$url_prosla_stranica = "";}

            $pocetak_link_stranice .= "<li><a class='gumb ikona' {$url_prva_stranica}><svg data-boja='{$boja}'><use xlink:href=\"/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_lijevo_duplo\" /></svg></a></li>";
            $pocetak_link_stranice .= "<li><a class='gumb ikona' {$url_prosla_stranica}><svg data-boja='{$boja}'><use xlink:href=\"/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_lijevo\" /></svg></a></li>";

        }

        for ($i = $x; $i <= $y; $i++) {

            if ($url) {$url_broj_stranice = "href='{$url}/{$i}'";} else {$url_broj_stranice = "";}

            if ($i == $broj_stranice) {

                $link_stranice .= "<li><a class='gumb' data-boja='{$boja}' {$url_broj_stranice}>{$i}</a></li>";

            }  else {

                $link_stranice .= "<li><a class='gumb' {$url_broj_stranice}>{$i}</a></li>";

            }

        }

        if ($broj_stranice < $ukupno_stranica) {

            $sljedeca_stranica = $broj_stranice + 1;

            if ($url) {$url_sljedeca_stranica = "href='{$url}/{$sljedeca_stranica}'";} else {$url_sljedeca_stranica = "";}
            if ($url) {$url_ukupno_stranica = "href='{$url}/{$ukupno_stranica}'";} else {$url_ukupno_stranica = "";}

            $kraj_link_stranice .= "<li><a class='gumb ikona' {$url_sljedeca_stranica}><svg data-boja='{$boja}'><use xlink:href=\"/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno\" /></svg></a></li>";
            $kraj_link_stranice .= "<li><a class='gumb ikona' {$url_ukupno_stranica}><svg data-boja='{$boja}'><use xlink:href=\"/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno_duplo\" /></svg></a></li>";

        }

        return array('pocetak' => $pocetak_link_stranice, 'stranice' => $link_stranice, 'kraj' => $kraj_link_stranice);

    }

    /**
     * ### Traži artikl
     * @since 0.1.1.pre-alpha.M1
     *
     * @param int|string $trazi <p>
     * Traži artikl.
     * </p>
     *
     * @return string Upit za traženje.
     */
    private function trazi (int|string $trazi):string {

        if ($trazi <> 'svi artikli') {

            $trazi = explode(' ', (string)$trazi);

            $trazi_array = '';
            foreach ($trazi as $stavka) {

                $trazi_array .= "
                    AND (
                        Naziv LIKE '%{$stavka}%'
                        OR Opis LIKE '%{$stavka}%'
                    )
                ";

            }

            return $trazi_array;

        }

        return '';

    }

    /**
     * ### Upit za veličine artikla
     * @since 0.1.2.pre-alpha.M1
     *
     * @param int|string $velicina <p>
     * Veličina artikla.
     * </p>
     *
     * @return string Upit za veličine artikla.
     */
    private function velicineUpit (int|string $velicina):string {

        if ($velicina === 'sve velicine') {

            return "";

        } else {

            return "HAVING Find_In_Set('$velicina', Velicine)";

        }

    }

}