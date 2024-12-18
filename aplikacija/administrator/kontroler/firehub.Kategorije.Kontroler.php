<?php declare(strict_types = 1);

/**
 * Kategorije
 * @since 0.1.2.pre-alpha.M1
 *
 * @author Danijel Galić
 * @copyright 2022 Kapriol Web Trgovina
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Aplikacija\Kontroler
 */

namespace FireHub\Aplikacija\Administrator\Kontroler;

use FireHub\Aplikacija\Administrator\Model\Kategorija_Model;
use FireHub\Aplikacija\Administrator\Model\Kategorije_Model;
use FireHub\Jezgra\Greske\Greska;
use FireHub\Jezgra\HTTP\Atributi\Zaglavlja;
use FireHub\Jezgra\HTTP\Enumeratori\Vrsta;
use FireHub\Jezgra\Sadrzaj\Enumeratori\Vrsta as Sadrzaj_Vrsta;
use FireHub\Jezgra\Sadrzaj\Sadrzaj;

/**
 * ### Kategorije
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Kontroler
 */
final class Kategorije_Kontroler extends Master_Kontroler {

    /**
     * ## index
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function index ():Sadrzaj {

        return sadrzaj()->datoteka('kategorije/lista.html')->podatci([]);

    }

    /**
     * ## Lista kategorija
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    #[Zaglavlja(vrsta: Vrsta::JSON)]
    public function lista (string $kontroler = '', string $metoda = '', int $broj_stranice = 1, string $poredaj = 'Kategorija', string $redoslijed = 'asc'):Sadrzaj {

        try {

            // model
            $kategorije = $this->model(Kategorije_Model::class);

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'da',
                'Kategorije' => $kategorije->lista($broj_stranice, $poredaj, $redoslijed),
                'Zaglavlje' => $kategorije->IspisiZaglavlje(),
                'Navigacija' => $kategorije->IspisiNavigaciju()
            ]);

        } catch (Greska $greska) {

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'ne',
                'Poruka' => $greska->getMessage()
            ]);

        }

    }

    /**
     * ## Uredi kategorije
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function uredi (string $kontroler = '', string $metoda = '', int $id = 0) {

        $kategorija_model = $this->model(Kategorija_Model::class);
        $kategorija = $kategorija_model->kategorija($id);

        // formatiranje rezultata
        if ($kategorija['CalcVelicina'] === true) {$kategorija['CalcVelicina'] = 'checked';} else {$kategorija['CalcVelicina'] = '';}

        return sadrzaj()->format(Sadrzaj_Vrsta::HTMLP)->datoteka('kategorije/uredi.html')->podatci([
            'id' => $kategorija['ID'],
            'naziv' => $kategorija['Kategorija'],
            'slika' => $kategorija['Slika'] ?? '',
            'redoslijed' => $kategorija['Prioritet'],
            'calc_velicina' => $kategorija['CalcVelicina']
        ]);

    }

    /**
     * ## Nova kategorija
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function nova (string $kontroler = '', string $metoda = '', int $id = 0) {

        // kategorije
        $kategorije_model = $this->model(Kategorije_Model::class);

        return sadrzaj()->format(Sadrzaj_Vrsta::HTMLP)->datoteka('kategorije/nova.html')->podatci([
            'id' => '0'
        ]);

    }

    /**
     * ## Spremi kategoriju
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function spremi (string $kontroler = '', string $metoda = '', int $id = 0) {

        try {

            // model
            $kategorija = $this->model(Kategorija_Model::class);
            $kategorija->spremi($id);

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'da',
                'Poruka' => _('Postavke spremljene')
            ]);

        } catch (Greska $greska) {

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'ne',
                'Poruka' => $greska->getMessage()
            ]);

        }

    }

    /**
     * ## Izbrisi kategoriju
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function izbrisi (string $kontroler = '', string $metoda = '', int $id = 0) {

        try {

            // model
            $kategorija = $this->model(Kategorija_Model::class);
            $kategorija->izbrisi($id);

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'da',
                'Poruka' => _('Postavke spremljene')
            ]);

        } catch (Greska $greska) {

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'ne',
                'Poruka' => $greska->getMessage()
            ]);

        }

    }

    /**
     * ### Spremi sliku kateogrije
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj
     */
    #[Zaglavlja(vrsta: Vrsta::JSON)]
    public function dodajSliku (string $kontroler = '', string $metoda = '', int $id = 0):Sadrzaj {

        try {

            // model
            $artikl = $this->model(Kategorija_Model::class);
            $artikl->dodajSliku($id);

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'da',
                'Poruka' => _('Uspješno spremljeno')
            ]);

        } catch (Greska $greska) {

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'ne',
                'Poruka' => $greska->getMessage()
            ]);

        }

    }

}