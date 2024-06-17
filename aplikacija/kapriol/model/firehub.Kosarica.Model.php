<?php declare(strict_types = 1);

/**
 * Košarica model
 * @since 0.1.2.pre-alpha.M1
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
use FireHub\Aplikacija\Kapriol\Jezgra\Validacija;
use FireHub\Aplikacija\Kapriol\Jezgra\Email;
use FireHub\Aplikacija\Kapriol\Jezgra\Domena;
use FireHub\Jezgra\Komponente\Log\Enumeratori\Level;
use FireHub\Jezgra\Greske\Greska;
use FireHub\Jezgra\Kontroler\Greske\Kontroler_Greska;
use FireHub\Jezgra\Kontejner\Greske\Kontejner_Greska;

/**
 * ### Kategorije model
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Model
 */
final class Kosarica_Model extends Master_Model {

    /**
     * ### Konstruktor
     * @since 0.1.2.pre-alpha.M1
     *
     * @param BazaPodataka $bazaPodataka <p>
     * Baza podataka.
     * </p>
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Sesije.
     */
    public function __construct (
        private BazaPodataka $bazaPodataka
    ) {

        parent::__construct();

    }

    /**
     * ### Artikli iz košarice
     * @since 0.1.2.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return array Niz artikala.
     */
    public function artikli ():array {

        if ($this->sesija->procitaj('kosarica')) {

            $id = array_keys($this->sesija->procitaj('kosarica'));

            $sifra_array = '';
            foreach ($id as $kljuc => $vrijednost) {

                if ($kljuc === array_key_first($id)) {

                    $sifra_array .= "
                        artiklikarakteristike.Sifra = '$vrijednost'
                    ";

                } else {

                    $sifra_array .= "
                        OR artiklikarakteristike.Sifra = '$vrijednost'
                    ";

                }

            }

            $artikli = $this->bazaPodataka->tabela('artikliview')
                ->sirovi("
                    SELECT
                           artikliview.ID, artikliview.Naziv, artikliview.Link, artikliview.".Domena::sqlCijena()." AS Cijena, artikliview.".Domena::sqlCijenaAkcija()." AS CijenaAkcija,
                           artiklikarakteristike.Sifra, artiklikarakteristike.Velicina,
                           (SELECT Slika FROM slikeartikal WHERE slikeartikal.ClanakID = artikliview.ID ORDER BY slikeartikal.Zadana DESC LIMIT 1) AS Slika,
                           ".(Domena::Hr() ? 'artikliview.GratisHr' : 'artikliview.GratisBa')." AS GratisID
                    FROM artikliview
                    LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikliview.ID
                    WHERE artikliview.Aktivan = 1 AND artikliview.".Domena::sqlTablica()." = 1
                    AND ($sifra_array)
                    ORDER BY Naziv ASC
                ")
                ->napravi();

            $rezultat = $artikli->niz();

            $kosarica = $this->sesija->procitaj('kosarica');

            // ukupne cijene i količine
            $total_cijena = 0;
            foreach ($kosarica as $stavka => $vrijednost) {

                $kljuc = array_search($stavka, array_column($rezultat, 'Sifra'));

                // ukupno cijena
                if ($rezultat[$kljuc]['CijenaAkcija'] > 0) {
                    $rezultat[$kljuc]['CijenaUkupno'] = $vrijednost * $rezultat[$kljuc]['CijenaAkcija'];
                } else if (Domena::blackFriday()) {
                    $rezultat[$kljuc]['CijenaUkupno'] = $vrijednost * ($rezultat[$kljuc]['Cijena'] - ($rezultat[$kljuc]['Cijena'] * Domena::blackFridayPopust()));
                } else {
                    $rezultat[$kljuc]['CijenaUkupno'] = $vrijednost * $rezultat[$kljuc]['Cijena'];
                }

                // kolicina
                $rezultat[$kljuc]['Kolicina'] = $vrijednost;

                // ukupno
                $total_cijena += $rezultat[$kljuc]['CijenaUkupno'];

            }

            // dostava
            if ($total_cijena <= Domena::dostavaLimit()) {

                $dostava = [
                    'ID' => '0',
                    'Naziv' => 'Dostava',
                    'Link' => '/',
                    'Cijena' => ''.Domena::dostavaIznos().'',
                    'CijenaAkcija' => '0',
                    'Slika' => 'dostava.jpg',
                    'Sifra' => '0',
                    'Velicina' => '0',
                    'CijenaUkupno' => Domena::dostavaIznos(),
                    'Kolicina' => 1
                ];

                array_push($rezultat, $dostava);

            }

            return $rezultat ?: [];

        }

        return [];

    }

    /**
     * ### Artikli iz košarice
     * @since 0.1.2.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return array Niz artikala.
     */
    public function artikliGratis ():array {

        if ($this->sesija->procitaj('kosarica_gratis')) {

            $id = array_keys($this->sesija->procitaj('kosarica_gratis'));

            $sifra_array = '';
            foreach ($id as $kljuc => $vrijednost) {

                if ($kljuc === array_key_first($id)) {

                    $sifra_array .= "
                        artiklikarakteristike.Sifra = '$vrijednost'
                    ";

                } else {

                    $sifra_array .= "
                        OR artiklikarakteristike.Sifra = '$vrijednost'
                    ";

                }

            }

            $artikli = $this->bazaPodataka->tabela('artikliview')
                ->sirovi("
                    SELECT
                           artikliview.ID, artikliview.Naziv, artikliview.Link, artikliview.".Domena::sqlCijena()." AS Cijena, artikliview.".Domena::sqlCijenaAkcija()." AS CijenaAkcija,
                           artiklikarakteristike.Sifra, artiklikarakteristike.Velicina,
                           (SELECT Slika FROM slikeartikal WHERE slikeartikal.ClanakID = artikliview.ID ORDER BY slikeartikal.Zadana DESC LIMIT 1) AS Slika,
                           ".(Domena::Hr() ? 'artikliview.GratisHr' : 'artikliview.GratisBa')." AS GratisID
                    FROM artikliview
                    LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikliview.ID
                    WHERE artikliview.Aktivan = 1 AND artikliview.".Domena::sqlTablica()." = 1
                    AND ($sifra_array)
                    ORDER BY Naziv ASC
                ")
                ->napravi();

            $rezultat = $artikli->niz();

            $kosarica = $this->sesija->procitaj('kosarica_gratis');

            foreach ($kosarica as $stavka => $vrijednost) {

                $kljuc = array_search($stavka, array_column($rezultat, 'Sifra'));

                // kolicina
                $rezultat[$kljuc]['Kolicina'] = array_sum($vrijednost);

            }

            return $rezultat ?: [];

        }

        return [];

    }

    /**
     * ### Dodaj artikl u košaricu
     * @since 0.1.2.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Baze podataka Log-a.
     * @throws Kontroler_Greska Ukoliko objekt nije validan model.
     *
     * @return bool Da li je artikl dodan u košaricu.
     */
    public function dodaj (string $velicina = '', int $vrijednost = 0, string|false $velicina_gratis = false, int $vrijednost_gratis = 0):bool {

        // provjera zaliha
        if (Domena::Hr()) {

            $velicina_baza = $this->bazaPodataka->tabela('artiklikarakteristike')
                ->sirovi("
                SELECT
                    SUM(StanjeSkladiste) AS StanjeSkladiste
                FROM artiklikarakteristike
                LEFT JOIN stanjeskladista ON stanjeskladista.Sifra = artiklikarakteristike.Sifra
                WHERE artiklikarakteristike.Sifra = '$velicina'
                AND (SkladisteID = 3)
                GROUP BY Velicina
            ")
            ->napravi();

        } else {

            $velicina_baza = $this->bazaPodataka->tabela('artiklikarakteristike')
                ->sirovi("
                SELECT
                    SUM(StanjeSkladiste) AS StanjeSkladiste
                FROM artiklikarakteristike
                LEFT JOIN stanjeskladista ON stanjeskladista.Sifra = artiklikarakteristike.Sifra
                WHERE artiklikarakteristike.Sifra = '$velicina'
                AND (SkladisteID = 1 OR SkladisteID = 2)
                GROUP BY Velicina
            ")
            ->napravi();

        }

        if ((int)$velicina_baza->redak()['StanjeSkladiste'] < 1) {

            zapisnik(Level::KRITICNO, sprintf(_('Šifre artikla: "%s" nema na stanju!'), $velicina_baza));
            throw new Kontroler_Greska(_('Ne mogu pokrenuti sustav, obratite se administratoru.'));

        }

        if (!$vrijednost > 0) {

            return false;

        }

        // provjera zaliha gratis
        if ($velicina_gratis) {
            if (Domena::Hr()) {

                $velicina_baza = $this->bazaPodataka->tabela('artiklikarakteristike')
                    ->sirovi("
                    SELECT
                        SUM(StanjeSkladiste) AS StanjeSkladiste
                    FROM artiklikarakteristike
                    LEFT JOIN stanjeskladista ON stanjeskladista.Sifra = artiklikarakteristike.Sifra
                    WHERE artiklikarakteristike.Sifra = '$velicina_gratis'
                    AND (SkladisteID = 3)
                    GROUP BY Velicina
                ")
                    ->napravi();

            } else {

                $velicina_baza = $this->bazaPodataka->tabela('artiklikarakteristike')
                    ->sirovi("
                    SELECT
                        SUM(StanjeSkladiste) AS StanjeSkladiste
                    FROM artiklikarakteristike
                    LEFT JOIN stanjeskladista ON stanjeskladista.Sifra = artiklikarakteristike.Sifra
                    WHERE artiklikarakteristike.Sifra = '$velicina_gratis'
                    AND (SkladisteID = 1 OR SkladisteID = 2)
                    GROUP BY Velicina
                ")
                    ->napravi();

            }

            if ((int)$velicina_baza->redak()['StanjeSkladiste'] < 1) {

                zapisnik(Level::KRITICNO, sprintf(_('Šifre artikla: "%s" nema na stanju!'), $velicina_baza));
                throw new Kontroler_Greska(_('Ne mogu pokrenuti sustav, obratite se administratoru.'));

            }

            if (!$vrijednost_gratis > 0) {

                return false;

            }

            $postoji = $this->bazaPodataka->tabela('artiklikarakteristike')
                ->sirovi("
                    SELECT
                        gratis.ID
                    FROM artiklikarakteristike
                    LEFT JOIN artikliview ON artikliview.ID = artiklikarakteristike.ArtikalID
                    LEFT JOIN artiklikarakteristike gratis ON gratis.ArtikalID = ".(Domena::Hr() ? 'artikliview.GratisHr' : 'artikliview.GratisBa')." AND gratis.Sifra = '$velicina_gratis'
                    WHERE artiklikarakteristike.Sifra = '$velicina'
                    LIMIT 1
                ")
                ->napravi();

            if ($postoji->redak()['ID'] === 'null') {

                zapisnik(Level::KRITICNO, sprintf(_('Šifre artikla: "%s" nije gratis!'), $velicina_gratis));
                throw new Kontroler_Greska(_('Ne mogu pokrenuti sustav, obratite se administratoru.'));

            }

        }

        if (isset($this->sesija->procitaj('kosarica')[$velicina])) {

            $this->sesija->dodaj('kosarica', $velicina, $vrijednost + $this->sesija->procitaj('kosarica')[$velicina]);

        } else {

            $this->sesija->dodaj('kosarica', $velicina, $vrijednost);

        }

        if ($velicina_gratis) {

            if (isset($this->sesija->procitaj('kosarica_gratis')[$velicina_gratis])) {

                if (isset($this->sesija->procitaj('kosarica_gratis')[$velicina_gratis][$velicina])) {
                    $_SESSION['kosarica_gratis'][$velicina_gratis][$velicina] = $_SESSION['kosarica_gratis'][$velicina_gratis][$velicina] + $vrijednost;
                } else {
                    $this->sesija->dodaj(
                        'kosarica_gratis',
                        $velicina_gratis,
                        $this->sesija->procitaj('kosarica_gratis')[$velicina_gratis] + [$velicina => $vrijednost]
                    );
                }

            } else {

                $this->sesija->dodaj(
                    'kosarica_gratis',
                    $velicina_gratis,
                    [$velicina => $vrijednost]
                );

            }

        }

        return true;

    }

    /**
     * ### Dodaj artikl u košaricu
     * @since 0.1.2.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Baze podataka Log-a.
     * @throws Kontroler_Greska Ukoliko objekt nije validan model.
     *
     * @return bool Da li je artikl dodan u košaricu.
     */
    public function izmijeni (string $velicina = '', int $vrijednost = 0):bool {

        $velicina_baza = $this->bazaPodataka->tabela('artiklikarakteristike')
            ->sirovi("
                SELECT
                    SUM(StanjeSkladiste) AS StanjeSkladiste
                FROM artiklikarakteristike
                LEFT JOIN stanjeskladista ON stanjeskladista.Sifra = artiklikarakteristike.Sifra
                WHERE artiklikarakteristike.Sifra = '$velicina'
                GROUP BY Velicina
            ")
            ->napravi();

        if (!$velicina_baza->redak()['StanjeSkladiste'] > 1) {

            zapisnik(Level::KRITICNO, sprintf(_('Šifre artikla: "%s" nema na stanju!'), $velicina_baza));
            throw new Kontroler_Greska(_('Ne mogu pokrenuti sustav, obratite se administratoru.'));

        }

        if (!$vrijednost > 0) {

            return false;

        }

        if (!isset($this->sesija->procitaj('kosarica')[$velicina])) {

            return false;

        }

        $this->sesija->dodaj('kosarica', $velicina, $vrijednost);

        return true;

    }

    /**
     * ### Dodaj artikl u košaricu
     * @since 0.1.2.pre-alpha.M1
     *
     * @return bool Da li je artikl izbrisan iz košarice.
     */
    public function izbrisi (string $velicina = ''):bool {

        if (!isset($this->sesija->procitaj('kosarica')[$velicina])) {

            return false;

        }

        $this->sesija->izbrisiNiz('kosarica', $velicina);

        if ($this->sesija->procitaj('kosarica_gratis')) {
            foreach ($this->sesija->procitaj('kosarica_gratis') as $key => $value) {
                foreach ($value as $gratis_key => $gratis_value) {
                    if ($gratis_key == $velicina) {
                        unset($_SESSION['kosarica_gratis'][$key][$gratis_key]);
                        if (empty($_SESSION['kosarica_gratis'][$key])) {
                            unset($_SESSION['kosarica_gratis'][$key]);
                        }
                    }
                }
            }
        }

        return true;

    }

    /**
     * ### Naruči
     *
     * @throws Greska
     * @throws Kontejner_Greska
     *
     * @return void
     */
    public function naruci ():void {

        $ime = $_POST['ime'];
        $email = $_POST['email'];
        $grad = $_POST['grad'];
        $telefon = $_POST['telefon'];
        $adresa = $_POST['adresa'];
        $zip = $_POST['zip'];
        $napomena = $_POST['napomena'];

        $ime = Validacija::Prilagodjen('/^[a-zšđčćžA-ZŠĐČĆŽ -]+$/i', "Vaše ime", $ime, 5, 50);
        $email = Validacija::Email("Vaš email", $email, 5, 100);
        $grad = Validacija::Prilagodjen('/^[a-zšđčćžA-ZŠĐČĆŽ -]+$/i', "Vaš grad", $grad,  3, 50);
        $telefon = Validacija::Telefon(_('Vaš broj telefona'), $telefon, 9, 15);
        $adresa = Validacija::String(_('Vaša adresa'), $adresa, 5, 300);
        $zip = Validacija::Broj(_('Vaš poštanski broj'), $zip, 5, 5);
        $napomena = Validacija::String("Vaša napomena", $napomena, 0, 1000);

        // pošalji email
        $email_artikli_korisnik = '';
        $artikli = $this->artikli();
        $total_kolicina = 0;
        $total_cijena = 0;
        foreach ($artikli as $artikal) {

            // cijene
            if ($artikal['CijenaAkcija'] > 0) {

                $artikl_cijena = number_format((float)$artikal['CijenaAkcija'], 2, ',', '.');

            } else {

                $artikl_cijena = number_format((float)$artikal['Cijena'], 2, ',', '.');

            }

            // ukupno
            $total_kolicina += $artikal['Kolicina'];
            $total_cijena += $artikal['CijenaUkupno'];

            $email_artikli_korisnik .= "
            <tr>
                <td>{$artikal['Sifra']}</td>
                <td>{$artikal['Naziv']}</td>
                <td>{$artikal['Velicina']}</td>
                <td>{$artikal['Kolicina']} kom</td>
                <td>$artikl_cijena ".Domena::valuta()."</td>
                <td>{$artikal['CijenaUkupno']} ".Domena::valuta()."</td>
            </tr>";
        }

        $total_cijena = number_format((float)$total_cijena, 2, ',', '.');

        $email_slanje = new Email('narudzba_korisnik.html');
        $email_slanje->Naslov('Vaša narudžba je zaprimljena');
        $email_slanje->Adresa(array(
            array("adresa" => $email, "ime" => $ime)
        ));
        $email_slanje->PredlozakKomponente(array(
            "ime" => $ime,
            "email" => $email,
            "grad" => $grad,
            "telefon" => $telefon,
            "adresa" => $adresa,
            "zip" => $zip,
            "napomena" => $napomena,
            "artikli" => $email_artikli_korisnik,
            "total_kolicina" => $total_kolicina . ' kom',
            "total_cijena" => $total_cijena . ' '.Domena::valuta()
        ));
        $email_slanje->Posalji();

        $email_slanje_tvrtka = new Email('narudzba_tvrtka.html');
        $email_slanje_tvrtka->Naslov('Vaša narudžba je zaprimljena');
        $email_slanje_tvrtka->Adresa(array(
            array("adresa" => 'danijel.galic@outlook.com', "ime" => 'Kapriol')
        ));
        $email_slanje_tvrtka->PredlozakKomponente(array(
            "ime" => $ime,
            "email" => $email,
            "grad" => $grad,
            "telefon" => $telefon,
            "adresa" => $adresa,
            "zip" => $zip,
            "napomena" => $napomena,
            "artikli" => $email_artikli_korisnik,
            "total_kolicina" => $total_kolicina . ' kom',
            "total_cijena" => $total_cijena . ' '.Domena::valuta()
        ));
        $email_slanje_tvrtka->Posalji();

        $this->sesija->unisti();

    }

    /**
     * ### Naruči
     *
     * @throws Greska
     * @throws Kontejner_Greska
     *
     * @return void
     */
    public function narucib2b ():void {

        $ime = $_POST['ime'];
        $email = $_POST['email'];
        $telefon = $_POST['telefon'];
        $r1 = $_POST['r1'] ?? false;
        $grad = $_POST['grad'];
        $adresa = $_POST['adresa'];
        $zip = $_POST['zip'];
        $tvrtka = $_POST['tvrtka'];
        $oib = $_POST['oib'];
        $tvrtkaadresa = $_POST['tvrtkaadresa'];
        $placanje = $_POST['placanje'];
        $napomena = $_POST['napomena'];

        //$ime = Validacija::Prilagodjen('/^[a-zšđčćžA-ZŠĐČĆŽ -]+$/i', "Vaše ime", $ime, 2, 100);
        $ime = Validacija::String(_('Vaše ime'), $ime, 2, 100);
        //$email = Validacija::Prilagodjen('', "Vaš email", $email, 2, 100);
        //$telefon = Validacija::Telefon(_('Vaš broj telefona'), $telefon, 1, 15);
        $telefon = Validacija::String(_('Vaš broj telefona'), $telefon, 5, 20);
        //$adresa = Validacija::String(_('Vaša adresa'), $adresa, 5, 300);
        //$zip = Validacija::Broj(_('Vaš poštanski broj'), $zip, 5, 5);
        //if($tvrtka <> '') {$tvrtka = Validacija::Prilagodjen('/^[a-zšđčćžA-ZŠĐČĆŽ0-9-. ]+$/i', _('Vaša tvrtka'), $tvrtka, 4, 100);}
        if($tvrtka <> '') {$tvrtka = Validacija::String(_('Vaša tvrtka'), $tvrtka, 4, 100);}
        if($oib <> '') {$oib = Validacija::String(_('Vaš OIB \ PDV \ ID tvrtke'), $oib, 1, 20);}
        if($tvrtkaadresa <> '') {$tvrtkaadresa = Validacija::String(_('Vaša adresa tvrtka'), $tvrtkaadresa, 4, 100);}
        $placanje = Validacija::Broj(_('Plaćanje'), $placanje, 1, 1);
        $napomena = Validacija::String("Vaša napomena", $napomena, 0, 1000);

        // pošalji email
        $email_artikli_korisnik = '';
        $artikli = $this->artikli();
        if (empty($artikli)) {
            zapisnik(Level::KRITICNO, 'Prazna košarica!');
            throw new Kontroler_Greska(_('Vaša košarica je prazna.'));
        }
        $total_kolicina = 0;
        $total_cijena = 0;
        foreach ($artikli as $artikal) {

            // cijene
            if ($artikal['CijenaAkcija'] > 0) {

                $artikl_cijena = number_format((float)$artikal['CijenaAkcija'], 2, ',', '.');

            } else if (Domena::blackFriday() && $artikal['ID'] !== '0') {

                $artikl_cijena = number_format((float)$artikal['Cijena'] - ((float)$artikal['Cijena'] * Domena::blackFridayPopust()), 2, ',', '.');

            } else {

                $artikl_cijena = number_format((float)$artikal['Cijena'], 2, ',', '.');

            }

            // ukupno
            $total_kolicina += $artikal['Kolicina'];
            $total_cijena += $artikal['CijenaUkupno'];
            $artikal['CijenaUkupno'] = number_format((float)$artikal['CijenaUkupno'], 2, ',', '.');

            $email_artikli_korisnik .= "
            <tr>
                <td align='center'>{$artikal['Sifra']}</td>
                <td align='center' style='text-align: center;'>{$artikal['Velicina']}</td>
                <td align='left'>{$artikal['Naziv']}</td>
                <td align='center' style='text-align: center;'>{$artikal['Kolicina']} kom</td>
                <td align='right' style='text-align: right;'>$artikl_cijena ".Domena::valuta()."</td>
                <td align='right' style='text-align: right;'>{$artikal['CijenaUkupno']} ".Domena::valuta()."</td>
            </tr>";
        }

        $email_artikli_gratis_korisnik = '';
        $artikli_gratis = $this->artikliGratis();
        foreach ($artikli_gratis as $artikal) {

            $email_artikli_korisnik .= "
            <tr>
                <td align='center'>{$artikal['Sifra']}</td>
                <td align='center' style='text-align: center;'>{$artikal['Velicina']}</td>
                <td align='left'>{$artikal['Naziv']}</td>
                <td align='center' style='text-align: center;'>{$artikal['Kolicina']} kom</td>
                <td align='right' style='text-align: right;'>GRATIS</td>
                <td align='right' style='text-align: right;'>GRATIS</td>
            </tr>";
        }

        $total_cijena_format = number_format((float)$total_cijena, 2, ',', '.');

        $email_slanje = new Email('narudzba_b2b_korisnik.html');
        $email_slanje->Naslov('Vaša narudžba je zaprimljena');
        $email_slanje->Adresa(array(
            array("adresa" => $email, "ime" => $ime)
        ));
        $email_slanje->PredlozakKomponente(array(
            "ime" => $ime,
            "email" => $email,
            "telefon" => $telefon,
            "r1" => $r1 ? 'Potreban R1 račun: <b>da</b>' : '',
            "grad" => $grad,
            "adresa" => $adresa,
            "zip" => $zip,
            "tvrtka" => $tvrtka ? 'Tvrtka: <b>'.$tvrtka.'</b>': '',
            "oib" => $oib ? ''.Domena::OIBPDV().': <b>'.$oib.'</b>' : '',
            "tvrtkaadresa" => $tvrtkaadresa ? 'Adresa tvrtke: <b>'.$tvrtkaadresa.'</b>' : '',
            "placanje" => $placanje == 1 ? 'Plaćanje pouzećem - gotovina' : 'Virman',
            "napomena" => $napomena,
            "datum" =>  date("d.m.Y"),
            "valuta" => Domena::valuta(),
            "artikli" => $email_artikli_korisnik,
            "artikli_gratis" => $email_artikli_gratis_korisnik,
            "total_kolicina" => $total_kolicina . ' kom',
            "total_cijena" => $total_cijena_format . ' '.Domena::valuta(),
            "tvrtka_adresa" => Domena::adresa(),
            "tvrtka_telefon" => Domena::telefon(),
            "tvrtka_mobitel" => Domena::mobitel()
        ));
        $email_slanje->Posalji();

        $email_slanje_tvrtka = new Email('narudzba_b2b_tvrtka.html');
        $email_slanje_tvrtka->Naslov('Vaša narudžba je zaprimljena');
        $email_slanje_tvrtka->Adresa(Domena::emailNarudzbe());
        $email_slanje_tvrtka->PredlozakKomponente(array(
            "ime" => $ime,
            "email" => $email,
            "telefon" => $telefon,
            "r1" => $r1 ? 'Potreban R1 račun: <b>da</b>' : '',
            "grad" => $grad,
            "adresa" => $adresa,
            "zip" => $zip,
            "tvrtka" => $tvrtka ? 'Tvrtka: <b>'.$tvrtka.'</b>': '',
            "oib" => $oib ? ''.Domena::OIBPDV().': <b>'.$oib.'</b>' : '',
            "tvrtkaadresa" => $tvrtkaadresa ? 'Adresa tvrtke: <b>'.$tvrtkaadresa.'</b>' : '',
            "placanje" => $placanje == 1 ? 'Plaćanje pouzećem - gotovina' : 'Virman',
            "napomena" => $napomena,
            "datum" =>  date("d.m.Y"),
            "valuta" => Domena::valuta(),
            "artikli" => $email_artikli_korisnik,
            "artikli_gratis" => $email_artikli_gratis_korisnik,
            "total_kolicina" => $total_kolicina . ' kom',
            "total_cijena" => $total_cijena_format . ' '.Domena::valuta(),
            "tvrtka_adresa" => Domena::adresa(),
            "tvrtka_telefon" => Domena::telefon(),
            "tvrtka_mobitel" => Domena::mobitel()
        ));
        $email_slanje_tvrtka->Posalji();

        $email_slanje_tvrtka = new Email('narudzba_b2b_tvrtka.html');
        $email_slanje_tvrtka->Naslov('Vaša narudžba je zaprimljena');
        $email_slanje_tvrtka->Adresa(array(
            array("adresa" => 'danijel.galic@outlook.com', "ime" => 'Danijel Galic'),
            array("adresa" => 'kapriol.ba@gmail.com', "ime" => 'Kapriol')
        ));
        $email_slanje_tvrtka->PredlozakKomponente(array(
            "ime" => $ime,
            "email" => $email,
            "telefon" => $telefon,
            "r1" => $r1 ? 'Potreban R1 račun: <b>da</b>' : '',
            "grad" => $grad,
            "adresa" => $adresa,
            "zip" => $zip,
            "tvrtka" => $tvrtka ? 'Tvrtka: <b>'.$tvrtka.'</b>': '',
            "oib" => $oib ? ''.Domena::OIBPDV().': <b>'.$oib.'</b>' : '',
            "tvrtkaadresa" => $tvrtkaadresa ? 'Adresa tvrtke: <b>'.$tvrtkaadresa.'</b>' : '',
            "placanje" => $placanje == 1 ? 'Plaćanje pouzećem - gotovina' : 'Virman',
            "napomena" => $napomena,
            "datum" =>  date("d.m.Y"),
            "valuta" => Domena::valuta(),
            "artikli" => $email_artikli_korisnik,
            "artikli_gratis" => $email_artikli_gratis_korisnik,
            "total_kolicina" => $total_kolicina . ' kom',
            "tvrtka_adresa" => Domena::adresa(),
            "tvrtka_telefon" => Domena::telefon(),
            "tvrtka_mobitel" => Domena::mobitel()
        ));
        $email_slanje_tvrtka->Posalji();

        //$this->sesija->unisti();

    }

    /**
     * ### Unisti sesiju.
     * @since 0.1.2.pre-alpha.M1
     *
     * @return void
     */
    public function ispravno ():void {

        $this->sesija->unisti();

    }

    /**
     * ### Kreira prvi broj.
     * @since 0.1.2.pre-alpha.M1
     *
     * @return int
     */
    public static function RandomBroj1 ():int {

        return random_int(0, 10);

    }

    /**
     * Kreira drugi broj.
     * @since 0.1.2.pre-alpha.M1
     *
     * @return int
     */
    public static function RandomBroj2 ():int {

        return random_int(0, 10);

    }

}