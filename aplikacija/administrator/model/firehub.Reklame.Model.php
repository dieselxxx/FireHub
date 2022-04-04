<?php declare(strict_types = 1);

/**
 * Reklame model
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

/**
 * ### Reklame
 *
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Model
 */
final class Reklame_Model extends Master_Model {

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
     * ### Dodaj sliku artikla
     * @since 0.1.2.pre-alpha.M1
     */
    public function dodajSliku (string $naziv_datoteke) {

        // prenesi sliku
        $datoteka = new PrijenosDatoteka($naziv_datoteke);
        $datoteka->Putanja(FIREHUB_ROOT.konfiguracija('sustav.putanje.web').'kapriol'.RAZDJELNIK_MAPE.'resursi'.RAZDJELNIK_MAPE.'grafika'.RAZDJELNIK_MAPE.'reklame'.RAZDJELNIK_MAPE);
        $datoteka->NovoIme($naziv_datoteke, false);
        $datoteka->DozvoljeneVrste(array('image/jpeg'));
        $datoteka->DozvoljenaVelicina(5000);
        $datoteka->PrijenosDatoteke();
        $datoteka->SlikaDimenzije(1400, 700);

    }

    /**
     * ### Spremi reklamu
     * @since 0.1.2.pre-alpha.M1
     */
    public function spremi (string $id) {

        $id = Validacija::String(_('ID reklame'), $id, 1, 10);

        $artikal = $_REQUEST[$id];

        $reklama = $this->bazaPodataka
            ->sirovi("
                UPDATE reklame
                    SET ArtikalID = '$artikal'
                WHERE reklame.Naziv = '$id'
            ")
            ->napravi();

    }

}
