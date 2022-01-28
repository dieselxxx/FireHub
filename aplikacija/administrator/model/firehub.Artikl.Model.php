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
use FireHub\Jezgra\Greske\Greska;
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
     * @param int $artikal
     *
     * @throws Kontejner_Greska
     * @return array|false|mixed[]
     */
    public function artikl (int $id) {

        $artikl = $this->bazaPodataka
            ->sirovi("
                SELECT
                    artikli.ID, artikli.Naziv, artikli.Opis,
                    artikli.Cijena, artikli.CijenaAkcija, artikli.CijenaKn, artikli.CijenaAkcijaKn,
                    artikli.Ba, artikli.Hr,
                    artikli.Aktivan, artikli.Izdvojeno
                FROM artikli
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

        return $artikl;

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
        $opis = Validacija::String(_('Opis artikla'), $opis, 0, 1500);

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

        $izdvojeno = $_REQUEST["izdvojeno"] ?? null;
        $izdvojeno = Validacija::Potvrda(_('Izdvojeno'), $izdvojeno);
        if ($izdvojeno == "on") {$izdvojeno = 1;} else {$izdvojeno = 0;}

        $aktivno = $_REQUEST["aktivno"] ?? null;
        $aktivno = Validacija::Potvrda(_('Aktivno'), $aktivno);
        if ($aktivno == "on") {$aktivno = 1;} else {$aktivno = 0;}

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
                        'Izdvojeno' => $izdvojeno,
                        'Aktivan' => $aktivno
                    ])
                    ->gdje('ID', '=', $id)
            )->napravi();

    }

}