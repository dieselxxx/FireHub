<?php declare(strict_types = 1);

/**
 * Artikl model
 * @since 0.1.2.pre-alpha.M1
 *
 * @author Danijel Galić
 * @copyright 2022 Kapriol Web Trgovina
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Aplikacija\Model
 */

namespace FireHub\Aplikacija\Administrator\Model;

use FireHub\Jezgra\Komponente\BazaPodataka\BazaPodataka;
use FireHub\Aplikacija\Kapriol\Jezgra\Validacija;
use FireHub\Aplikacija\Administrator\Jezgra\PrijenosDatoteka;
use FireHub\Jezgra\Kontejner\Greske\Kontejner_Greska;

/**
 * ### Artikl
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Model
 */
final class Artikl_Model extends Master_Model {

    /**
     * ### Konstruktor
     * @since 0.1.2.pre-alpha.M1
     */
    public function __construct (
        private BazaPodataka $bazaPodataka
    ){

        parent::__construct();

    }

    /**
     * ### Artikl
     * @since 0.1.2.pre-alpha.M1
     *
     * @param int $id
     *
     * @throws Kontejner_Greska
     * @return array|false|mixed[]
     */
    public function artikl (int $id):array|false {

        $artikl = $this->bazaPodataka
            ->sirovi("
                SELECT
                    artikli.ID, artikli.Naziv, artikli.Opis,
                    artikli.Cijena, artikli.CijenaAkcija, artikli.CijenaKn, artikli.CijenaAkcijaKn,
                    artikli.Ba, artikli.Hr, artikli.Outlet, artikli.OutletHr, artikli.Novo,
                    artikli.Aktivan, artikli.Izdvojeno,
                    artikli.KategorijaID, kategorije.Kategorija,
                    artikli.PodKategorijaID, podkategorije.PodKategorija,
                    artikli.GratisBa, gratisBa.Naziv AS GratisBaNaziv, artikli.GratisHr, gratisHr.Naziv AS GratisHrNaziv,
                    (SELECT ID FROM slikeartikal WHERE ClanakID = $id ORDER BY Zadana DESC, ID LIMIT 1) AS Slika,
                    (SELECT Slika FROM slikeartikal WHERE ClanakID = $id ORDER BY Zadana DESC, ID LIMIT 1) AS SlikaNaziv
                FROM artikli
                LEFT JOIN kategorije ON kategorije.ID = artikli.KategorijaID
                LEFT JOIN podkategorije ON podkategorije.ID = artikli.PodKategorijaID
                LEFT JOIN artikli gratisBa ON gratisBa.ID = artikli.GratisBa
                LEFT JOIN artikli gratisHr ON gratisHr.ID = artikli.GratisHr
                WHERE artikli.ID = $id
                LIMIT 1
            ")
            ->napravi();

        $artikl = $artikl->redak();

        $artikl['Cijena'] = number_format((float)$artikl['Cijena'], 2, ',', '.');
        $artikl['CijenaAkcija'] = number_format((float)$artikl['CijenaAkcija'], 2, ',', '.');
        $artikl['CijenaKn'] = number_format((float)$artikl['CijenaKn'], 2, ',', '.');
        $artikl['CijenaAkcijaKn'] = number_format((float)$artikl['CijenaAkcijaKn'], 2, ',', '.');
        if ($artikl['Izdvojeno']) {$artikl['Izdvojeno'] = true;} else {$artikl['Izdvojeno'] = false;}
        if ($artikl['Aktivan']) {$artikl['Aktivan'] = true;} else {$artikl['Aktivan'] = false;}
        if ($artikl['Ba']) {$artikl['Ba'] = true;} else {$artikl['Ba'] = false;}
        if ($artikl['Hr']) {$artikl['Hr'] = true;} else {$artikl['Hr'] = false;}
        if ($artikl['Outlet']) {$artikl['Outlet'] = true;} else {$artikl['Outlet'] = false;}
        if ($artikl['OutletHr']) {$artikl['OutletHr'] = true;} else {$artikl['OutletHr'] = false;}
        if ($artikl['Novo']) {$artikl['Novo'] = true;} else {$artikl['Novo'] = false;}

        return $artikl;

    }

    /**
     * ### Slike artikla
     * @since 0.1.2.pre-alpha.M1
     *
     * @param int $id
     *
     * @throws Kontejner_Greska
     * @return array
     */
    public function slike (int $id):array {

        $slike = $this->bazaPodataka
            ->sirovi("
                SELECT
                    slikeartikal.ID, slikeartikal.Slika, slikeartikal.Zadana
                FROM slikeartikal
                WHERE slikeartikal.ClanakID = $id
            ")
            ->napravi();

        return $slike->niz() ?: [];

    }

    /**
     * ### Cijena artikla
     * @since 0.1.2.pre-alpha.M1
     *
     * @param int $id
     *
     * @throws Kontejner_Greska
     * @return array
     */
    public function cijene (int $id):array {

        $slike = $this->bazaPodataka
            ->sirovi("
                SELECT
                    artiklicijene.ID, artiklicijene.Cijena, artiklicijene.Vrsta, artiklicijene.Datum
                FROM artiklicijene
                WHERE artiklicijene.ArtikalID = $id
                AND artiklicijene.Datum >= (CURDATE() - INTERVAL 10 DAY)
                ORDER BY artiklicijene.ID DESC
            ")
            ->napravi();

        return $slike->niz() ?: [];

    }

    /**
     * ### Spremi artikl
     * @since 0.1.2.pre-alpha.M1
     */
    public function spremi (int $id) {

        $id = Validacija::Broj(_('ID artikla'), $id, 1, 10);

        $naziv = $_REQUEST['naziv'];
        $naziv = Validacija::String(_('Naziv artikla'), $naziv, 3, 100);

        $opis = $_REQUEST['opis'];
        $opis = Validacija::StringHTML(_('Opis artikla'), $opis, 0, 1500);

        $cijena = $_REQUEST['cijena'];
        $cijena = str_replace('.', '', $cijena);
        $cijena = str_replace(',', '.', $cijena);
        $cijena = round((float)$cijena, 2);
        $cijena = Validacija::DecimalniBroj(_('Cijena artikla'), $cijena);

        $cijena_akcija = $_REQUEST['cijena_akcija'];
        $cijena_akcija = str_replace('.', '', $cijena_akcija);
        $cijena_akcija = str_replace(',', '.', $cijena_akcija);
        $cijena_akcija = round((float)$cijena_akcija, 2);
        $cijena_akcija = Validacija::DecimalniBroj(_('Cijena artikla'), $cijena_akcija);

        $cijena_hr = $_REQUEST['cijena_hr'];
        $cijena_hr = str_replace('.', '', $cijena_hr);
        $cijena_hr = str_replace(',', '.', $cijena_hr);
        $cijena_hr = round((float)$cijena_hr, 2);
        $cijena_hr = Validacija::DecimalniBroj(_('Cijena artikla'), $cijena_hr);

        $cijena_akcija_hr = $_REQUEST['cijena_akcija_hr'];
        $cijena_akcija_hr = str_replace('.', '', $cijena_akcija_hr);
        $cijena_akcija_hr = str_replace(',', '.', $cijena_akcija_hr);
        $cijena_akcija_hr = round((float)$cijena_akcija_hr, 2);
        $cijena_akcija_hr = Validacija::DecimalniBroj(_('Cijena artikla'), $cijena_akcija_hr);

        $outlet = $_REQUEST["outlet"] ?? null;
        $outlet = Validacija::Potvrda(_('Outlet'), $outlet);
        if ($outlet == "on") {$outlet = 1;} else {$outlet = 0;}

        $outlethr = $_REQUEST["outlethr"] ?? null;
        $outlethr = Validacija::Potvrda(_('OutletHr'), $outlethr);
        if ($outlethr == "on") {$outlethr = 1;} else {$outlethr = 0;}

        $novo = $_REQUEST["novo"] ?? null;
        $novo = Validacija::Potvrda(_('Novo'), $novo);
        if ($novo == "on") {$novo = 1;} else {$novo = 0;}

        $izdvojeno = $_REQUEST["izdvojeno"] ?? null;
        $izdvojeno = Validacija::Potvrda(_('Izdvojeno'), $izdvojeno);
        if ($izdvojeno == "on") {$izdvojeno = 1;} else {$izdvojeno = 0;}

        $aktivno = $_REQUEST["aktivno"] ?? null;
        $aktivno = Validacija::Potvrda(_('Aktivno'), $aktivno);
        if ($aktivno == "on") {$aktivno = 1;} else {$aktivno = 0;}

        $ba = $_REQUEST["ba"] ?? null;
        $ba = Validacija::Potvrda(_('BA'), $ba);
        if ($ba == "on") {$ba = 1;} else {$ba = 0;}

        $hr = $_REQUEST["hr"] ?? null;
        $hr = Validacija::Potvrda(_('HR'), $hr);
        if ($hr == "on") {$hr = 1;} else {$hr = 0;}

        $kategorija_stavke= $_REQUEST['kategorija'];
        $kategorija_stavke = explode(',', $kategorija_stavke);
        $kategorija = Validacija::Broj(_('Kategorija artikla'), $kategorija_stavke[0], 1, 7);
        $podkategorija = Validacija::Broj(_('Podkategorija artikla'), (int)$kategorija_stavke[1], 1, 7);

        $gratis_ba = $_REQUEST['gratisBa'] ?? 0;
        $gratis_ba = Validacija::Broj(_('Gratis BA'), $gratis_ba, 1, 7);
        $gratis_hr = $_REQUEST['gratisHr'] ?? 0;
        $gratis_hr = Validacija::Broj(_('Gratis HR'), $gratis_hr, 1, 7);

        $zadanaSlika = $_REQUEST['zadanaSlika'] ?? 0;
        $zadanaSlika = Validacija::Broj(_('Zadana slika'), $zadanaSlika, 1, 7);

        if ($id !== 0) {

            $spremi = $this->bazaPodataka
                ->transakcija(
                    (new BazaPodataka())->tabela('artikli')
                        ->azuriraj([
                            'Naziv' => $naziv,
                            'Opis' => $opis,
                            'Cijena' => $cijena,
                            'CijenaAkcija' => $cijena_akcija,
                            'CijenaKn' => $cijena_hr,
                            'CijenaAkcijaKn' => $cijena_akcija_hr,
                            'Ba' => $ba,
                            'Hr' => $hr,
                            'Outlet' => $outlet,
                            'OutletHr' => $outlethr,
                            'Novo' => $novo,
                            'Izdvojeno' => $izdvojeno,
                            'Aktivan' => $aktivno,
                            'KategorijaID' => $kategorija,
                            'PodKategorijaID' => $podkategorija,
                            'GratisBa' => $gratis_ba,
                            'GratisHr' => $gratis_hr
                        ])
                        ->gdje('ID', '=', $id)
                )
                ->napravi();

            $spremi = $this->bazaPodataka
                ->transakcija(
                    (new BazaPodataka())->tabela('slikeartikal')
                        ->azuriraj([
                            'Zadana' => 0
                        ])
                        ->gdje('ClanakID', '=', $id)
                )
                ->napravi();

            $spremi = $this->bazaPodataka
                ->transakcija(
                    (new BazaPodataka())->tabela('slikeartikal')
                        ->azuriraj([
                            'Zadana' => 1
                        ])
                        ->gdje('ID', '=', $zadanaSlika)
                )
                ->napravi();

            $spremi = $this->bazaPodataka
                ->transakcija(
                    (new BazaPodataka())->tabela('artiklicijene')
                        ->umetni([
                            'ArtikalID' => $id,
                            'Cijena' => $cijena,
                            'Vrsta' => 'BA',
                            'Datum' => (new \DateTime())->format('Y-m-d')
                        ])
                )->napravi();

            $spremi = $this->bazaPodataka
                ->transakcija(
                    (new BazaPodataka())->tabela('artiklicijene')
                        ->umetni([
                            'ArtikalID' => $id,
                            'Cijena' => $cijena_hr,
                            'Vrsta' => 'HR',
                            'Datum' => (new \DateTime())->format('Y-m-d')
                        ])
                )->napravi();

        } else {

            $spremi = $this->bazaPodataka
                ->transakcija(
                    (new BazaPodataka())->tabela('artikli')
                        ->umetni([
                            'Naziv' => $naziv,
                            'Opis' => $opis,
                            'Cijena' => $cijena,
                            'CijenaAkcija' => $cijena_akcija,
                            'CijenaKn' => $cijena_hr,
                            'CijenaAkcijaKn' => $cijena_akcija_hr,
                            'Ba' => $ba,
                            'Hr' => $hr,
                            'Outlet' => $outlet,
                            'OutletHr' => $outlethr,
                            'Novo' => $novo,
                            'Izdvojeno' => $izdvojeno,
                            'Aktivan' => $aktivno,
                            'KategorijaID' => $kategorija,
                            'PodKategorijaID' => $podkategorija
                        ])
                )->napravi();

        }

    }

    /**
     * ### Dodaj sliku artikla
     * @since 0.1.2.pre-alpha.M1
     */
    public function dodajSliku (int $id) {

        // prenesi sliku
        $datoteka = new PrijenosDatoteka('slika');
        $datoteka->Putanja(FIREHUB_ROOT.konfiguracija('sustav.putanje.web').'kapriol'.RAZDJELNIK_MAPE.'resursi'.RAZDJELNIK_MAPE.'grafika'.RAZDJELNIK_MAPE.'artikli'.RAZDJELNIK_MAPE);
        $datoteka->NovoIme($id . '_');
        $datoteka->DozvoljeneVrste(array('image/jpeg'));
        $datoteka->DozvoljenaVelicina(1000);
        $datoteka->PrijenosDatoteke();
        $datoteka->SlikaDimenzije(800, 600);

         $this->bazaPodataka
        ->sirovi("
            INSERT INTO slikeartikal
            (ClanakID, Slika, Zadana)
            VALUES
            ('$id', '{$datoteka->ImeDatoteke()}', 0)
        ")
        ->napravi();

    }

    /**
     * ### Izbriši sliku artikla
     * @since 0.1.2.pre-alpha.M1
     */
    public function izbrisiSliku (int $id) {

        $naziv_sql = $this->bazaPodataka
            ->sirovi("
            SELECT Slika
            FROM slikeartikal
            WHERE ID = '$id'
        ")
        ->napravi();

        $naziv = $naziv_sql->redak()['Slika'];

        $this->bazaPodataka
            ->sirovi("
            DELETE FROM slikeartikal
            WHERE ID = '$id'
        ")
        ->napravi();

        unlink(FIREHUB_ROOT.konfiguracija('sustav.putanje.web').'Kapriol'.RAZDJELNIK_MAPE.'resursi'.RAZDJELNIK_MAPE.'grafika'.RAZDJELNIK_MAPE.'artikli'.RAZDJELNIK_MAPE.$naziv);

    }

    /**
     * ### Dohvati karakteristike artikla
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string|int $id <p>
     * ID artikla.
     * </p>
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return array Artikl.
     */
    public function artiklKarakteristike (string|int $id):array {

        $karakteristike = $this->bazaPodataka->tabela('artiklikarakteristike')
            ->sirovi("
                SELECT
                    artiklikarakteristike.ID, artiklikarakteristike.Sifra AS artiklikarakteristikeSifra, Velicina
                FROM artiklikarakteristike
                LEFT JOIN stanjeskladista ON stanjeskladista.Sifra = artiklikarakteristike.Sifra
                WHERE ArtikalID = '$id'
                GROUP BY Velicina
                ORDER BY artiklikarakteristike.ID
            ")
            ->napravi();

        return $karakteristike->niz() ?: [];

    }

    /**
     * ### Artikl zaliha
     * @since 0.1.2.pre-alpha.M1
     *
     * @param int $id
     *
     * @throws Kontejner_Greska
     * @return array|false|mixed[]
     */
    public function artiklzaliha (string|int $id, string|int $skladiste):array|false {

        $artikl = $this->bazaPodataka
            ->sirovi("
                SELECT
                    skladiste.NazivSkladista, stanjeskladista.StanjeSkladiste
                FROM stanjeskladista
                LEFT JOIN skladiste ON skladiste.ID = stanjeskladista.SkladisteID
                LEFT JOIN artiklikarakteristike ON artiklikarakteristike.Sifra = stanjeskladista.Sifra
                LEFT JOIN artikli ON artikli.ID = artiklikarakteristike.ArtikalID
                WHERE stanjeskladista.Sifra = '$id' AND skladiste.ID = '$skladiste'
                LIMIT 1
            ")
            ->napravi();

        $artikl = $artikl->redak();

        return $artikl;

    }

    public function skladista ():array {

        $karakteristike = $this->bazaPodataka->tabela('artiklikarakteristike')
            ->sirovi("
                SELECT
                    ID, NazivSkladista
                FROM skladiste
                ORDER BY ID
            ")
            ->napravi();

        return $karakteristike->niz();

    }

    /**
     * ### Spremi zalihu artikla
     * @since 0.1.2.pre-alpha.M1
     */
    public function zalihaspremi (int $id) {

        $id = Validacija::Broj(_('ID artikla'), $id, 1, 10);

        foreach ($_POST['zaliha'] as $karakteristika => $skladiste) {

            foreach ($skladiste as $skladisteID => $vrijednost) {

                $postoji = $this->bazaPodataka
                    ->sirovi("
                    SELECT count(*) AS broj FROM stanjeskladista WHERE SkladisteID = '$skladisteID' AND Sifra = '$karakteristika'
                    ")
                    ->napravi();

                if ($postoji->redak()['broj'] == '0') {

                    $sql = $this->bazaPodataka
                        ->sirovi("
                            INSERT INTO stanjeskladista (SkladisteID, Sifra, StanjeSkladiste) VALUES ('$skladisteID', '$karakteristika', '$vrijednost')
                        ")
                        ->napravi();

                } else {

                    $sql = $this->bazaPodataka
                        ->sirovi("
                        UPDATE stanjeskladista SET StanjeSkladiste = '$vrijednost' WHERE SkladisteID = '$skladisteID' AND Sifra = '$karakteristika'
                        ")
                        ->napravi();

                }

            }

        }

    }

    /**
     * ### Spremi šifre artikla
     * @since 0.1.2.pre-alpha.M1
     */
    public function artiklsifrespremi (int $id) {

        $id = Validacija::Broj(_('ID artikla'), $id, 1, 10);

        foreach ($_POST['zaliha'] as $karakeristikaID => $sifra) {

            $karakeristikaID = Validacija::Broj(_('ID šifre'), $karakeristikaID, 1, 10);
            $sifra = Validacija::String(_('Šifra artikla'), $sifra, 1, 50);
            $velicina = Validacija::String(_('Veličina artikla'), $_POST['velicina'][$karakeristikaID], 1, 50);

            $postoji = $this->bazaPodataka
                ->sirovi("
                    SELECT count(*) AS broj FROM artiklikarakteristike WHERE ID = '$karakeristikaID' AND ArtikalID = '$id'
                    ")
                ->napravi();

            if ($postoji->redak()['broj'] == '0') {

                $sql = $this->bazaPodataka
                    ->sirovi("
                        INSERT INTO artiklikarakteristike (ArtikalID, Sifra, Velicina) VALUES ('$id', '$sifra', '$velicina')
                    ")
                    ->napravi();

            } else {

                $sql = $this->bazaPodataka
                    ->sirovi("
                        UPDATE artiklikarakteristike SET Sifra = '$sifra', Velicina = '$velicina' WHERE ID = '$karakeristikaID' AND ArtikalID = '$id'
                    ")
                    ->napravi();

            }

        }

    }

    /**
     * ### Izbriši šifru artikla
     * @since 0.1.2.pre-alpha.M1
     */
    public function artiklsifreizbrisi (int $id) {

        $id = Validacija::Broj(_('ID artikla'), $id, 1, 10);

        $sql = $this->bazaPodataka
            ->sirovi("
                DELETE FROM artiklikarakteristike WHERE ID = '$id'
            ")
            ->napravi();

    }

}
