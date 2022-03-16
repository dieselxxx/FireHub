<?php declare(strict_types = 1);

/**
 * Kategorija model
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

use FireHub\Aplikacija\Administrator\Jezgra\PrijenosDatoteka;
use FireHub\Aplikacija\Kapriol\Jezgra\Validacija;
use FireHub\Jezgra\Komponente\BazaPodataka\BazaPodataka;
use FireHub\Jezgra\Kontejner\Greske\Kontejner_Greska;

/**
 * ### Kategorija
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Model
 */
final class Kategorija_Model extends Master_Model {

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
     * ### Kateogrija
     * @since 0.1.2.pre-alpha.M1
     *
     * @param int $id
     *
     * @throws Kontejner_Greska
     * @return array|false|mixed[]
     */
    public function kategorija (int $id):array|false {

        $kategorija = $this->bazaPodataka
            ->sirovi("
                SELECT
                    kategorije.ID, kategorije.Kategorija, kategorije.Slika, kategorije.CalcVelicina
                FROM kategorije
                WHERE kategorije.ID = $id
                LIMIT 1
            ")
            ->napravi();

        $kategorija = $kategorija->redak();

        if ($kategorija['CalcVelicina']) {$kategorija['CalcVelicina'] = true;} else {$kategorija['CalcVelicina'] = false;}

        return $kategorija;

    }

    /**
     * ### Spremi kategoriju
     * @since 0.1.2.pre-alpha.M1
     */
    public function spremi (int $id) {

        $id = Validacija::Broj(_('ID kategorije'), $id, 1, 10);

        $naziv = $_REQUEST['naziv'];
        $naziv = Validacija::String(_('Naziv kategorije'), $naziv, 3, 250);

        $calc_velcina = $_REQUEST["calc_velicina"] ?? null;
        $calc_velcina = Validacija::Potvrda(_('BA'), $calc_velcina);
        if ($calc_velcina == "on") {$calc_velcina = 1;} else {$calc_velcina = 0;}

        $obavijest = $this->bazaPodataka
            ->sirovi("
                UPDATE kategorije
                    SET Kategorija = '$naziv', CalcVelicina = '$calc_velcina'
                WHERE kategorije.ID = $id
            ")
            ->napravi();

    }

    /**
     * ### Dodaj sliku kategorije
     * @since 0.1.2.pre-alpha.M1
     */
    public function dodajSliku (int $id) {

        // prenesi sliku
        $datoteka = new PrijenosDatoteka('slika');
        $datoteka->Putanja(FIREHUB_ROOT.konfiguracija('sustav.putanje.web').'kapriol'.RAZDJELNIK_MAPE.'resursi'.RAZDJELNIK_MAPE.'grafika'.RAZDJELNIK_MAPE.'kategorije'.RAZDJELNIK_MAPE);
        $datoteka->DozvoljeneVrste(array('image/jpeg', 'image/png'));
        $datoteka->DozvoljenaVelicina(1000);
        $datoteka->PrijenosDatoteke();
        $datoteka->SlikaDimenzije(800, 600);

        $this->bazaPodataka
            ->sirovi("
            UPDATE kategorije
            SET Slika = '{$datoteka->ImeDatoteke()}'
            WHERE kategorije.ID = $id
        ")
            ->napravi();

    }

}