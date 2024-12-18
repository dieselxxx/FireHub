<?php declare(strict_types = 1);

/**
 * Master
 * @since 0.1.2.pre-alpha.M1
 *
 * @author Danijel Galić
 * @copyright 2022 Kapriol Web Trgovina
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Aplikacija\Kontroler
 */

namespace FireHub\Aplikacija\Kapriol\Kontroler;

use FireHub\Aplikacija\Kapriol\Jezgra\Server;
use FireHub\Aplikacija\Kapriol\Model\Favorit_Model;
use FireHub\Jezgra\Kontroler\Kontroler;
use FireHub\Aplikacija\Kapriol\Model\Kosarica_Model;
use FireHub\Aplikacija\Kapriol\Jezgra\Validacija;
use FireHub\Jezgra\Kontejner\Greske\Kontejner_Greska;
use FireHub\Jezgra\Kontroler\Greske\Kontroler_Greska;

/**
 * ### Master
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Kontroler
 */
abstract class Master_Kontroler extends Kontroler {

    /**
     * ### Konstruktor
     * @since 0.1.2.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Baze podataka Log-a.
     * @throws Kontroler_Greska Ukoliko objekt nije validan model.
     */
    public function __construct () {

        if (isset($_POST['kosarica_izmijeni'])) {

            if (isset($_POST['velicina'])) {

                $velicina =  Validacija::String('Veličina', $_POST['velicina'], 1, 10);

                $this->model(Kosarica_Model::class)->izmijeni($velicina, (int)$_POST['vrijednost'] ?? 0);

                header("Location: ".$_SERVER['REQUEST_URI']);

            }

        }

        if (isset($_POST['kosarica_izbrisi'])) {

            if (isset($_POST['velicina'])) {

                $velicina =  Validacija::String('Veličina', $_POST['velicina'], 1, 10);

                $this->model(Kosarica_Model::class)->izbrisi($velicina);

                header("Location: ".$_SERVER['REQUEST_URI']);

            }

        }

        if (isset($_POST['favorit_izbrisi'])) {

            if (isset($_POST['ID'])) {

                $id =  Validacija::Broj('Veličina', $_POST['ID'], 1, 10);

                $this->model(Favorit_Model::class)->izbrisi($id);

            }

        }

    }

    /**
     * ### Broj komada u košarici
     * @since 0.1.2.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Log-a.
     * @throws Kontroler_Greska Ukoliko objekt nije validan model.
     *
     * @return string Broj komada u košarici.
     */
    protected function kosaricaArtikli ():string {

        $kosarica_model = $this->model(Kosarica_Model::class);

        // artikli
        $kosarica_artikli = $kosarica_model->artikli();
        $total_kolicina = 0;
        $total_cijena = 0;
        if (!empty($kosarica_artikli)) {

            foreach ($kosarica_artikli as $artikal) {

                if ($artikal['Naziv'] !== 'Dostava') {

                    // ukupno količina
                    $total_kolicina += $artikal['Kolicina'];

                }

                // ukupno cijena
                $total_cijena += $artikal['CijenaUkupno'];

            }

            return ''.$total_kolicina.'';

        } else {

            return '0';

        }

    }

    /**
     * ### Broj komada u košarici HTML
     * @since 0.1.2.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Log-a.
     * @throws Kontroler_Greska Ukoliko objekt nije validan model.
     *
     * @return string Broj komada u košarici.
     */
    protected function kosaricaArtikliHTML ():string {

        if ($this->kosaricaArtikli() === '0') {

            return '';

        }

        return '
        <a data-boja="boja" id="kosarica_fixed" href="/kosarica">
            <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#kosarica"></use></svg>
            <span>('.$this->kosaricaArtikli().')</span>
        </a>
        ';

    }

    /**
     * ### Broj komada u košarici
     * @since 0.1.2.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Log-a.
     * @throws Kontroler_Greska Ukoliko objekt nije validan model.
     *
     * @return string Broj komada u košarici.
     */
    protected function favoritArtikli ():string {

        $artikli = $this->model(Favorit_Model::class);

        return $artikli->artikli() > 0 ? ''.$artikli->artikli().'' : '0';

    }

}