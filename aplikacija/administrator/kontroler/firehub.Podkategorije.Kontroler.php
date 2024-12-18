<?php declare(strict_types = 1);

/**
 * Podkategorije
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

use FireHub\Aplikacija\Administrator\Model\Kategorije_Model;
use FireHub\Aplikacija\Administrator\Model\PodKategorija_Model;
use FireHub\Aplikacija\Administrator\Model\PodKategorije_Model;
use FireHub\Jezgra\Greske\Greska;
use FireHub\Jezgra\HTTP\Atributi\Zaglavlja;
use FireHub\Jezgra\HTTP\Enumeratori\Vrsta;
use FireHub\Jezgra\Sadrzaj\Enumeratori\Vrsta as Sadrzaj_Vrsta;
use FireHub\Jezgra\Sadrzaj\Sadrzaj;

/**
 * ### Podkategorije
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Kontroler
 */
final class Podkategorije_Kontroler extends Master_Kontroler {

    /**
     * ## index
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function index ():Sadrzaj {

        return sadrzaj()->datoteka('podkategorije/lista.html')->podatci([]);

    }

    /**
     * ## Lista kategorija
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    #[Zaglavlja(vrsta: Vrsta::JSON)]
    public function lista (string $kontroler = '', string $metoda = '', int $broj_stranice = 1, string $poredaj = 'PodKategorija', string $redoslijed = 'asc'):Sadrzaj {

        try {

            // model
            $kategorije = $this->model(PodKategorije_Model::class);

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'da',
                'PodKategorije' => $kategorije->lista($broj_stranice, $poredaj, $redoslijed),
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

        $kategorija_model = $this->model(PodKategorija_Model::class);
        $kategorija = $kategorija_model->podkategorija($id);

        return sadrzaj()->format(Sadrzaj_Vrsta::HTMLP)->datoteka('podkategorije/uredi.html')->podatci([
            'id' =>''.$kategorija['ID'].'',
            'naziv' => ''.$kategorija['PodKategorija'].'',
            'slika' => $kategorija['Slika'] ?? '',
            'kategorija' => ''.$kategorija['Kategorija'].''
        ]);

    }

    /**
     * ## Nova podkategorija
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function nova (string $kontroler = '', string $metoda = '', int $id = 0) {

        // podkategorije
        $podkategorije_model = $this->model(PodKategorije_Model::class);

        // kategorije
        $kategorije_model = $this->model(Kategorije_Model::class);
        $kategorije = $kategorije_model->lista(limit_zapisa_po_stranici: 1000);

        $kategorije_html = '';
        foreach ($kategorije as $kategorija) {

            $kategorije_html .= "<option value='{$kategorija['ID']}'>{$kategorija['Kategorija']}</option>";

        }

        return sadrzaj()->format(Sadrzaj_Vrsta::HTMLP)->datoteka('podkategorije/nova.html')->podatci([
            'id' => '0',
            'kategorije' => $kategorije_html
        ]);

    }

    /**
     * ## Spremi podkategoriju
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function spremi (string $kontroler = '', string $metoda = '', int $id = 0) {

        try {

            // model
            $kategorija = $this->model(PodKategorija_Model::class);
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
     * ## Izbrisi podkategoriju
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function izbrisi (string $kontroler = '', string $metoda = '', int $id = 0) {

        try {

            // model
            $kategorija = $this->model(PodKategorija_Model::class);
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
     * ### Spremi sliku podkateogrije
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj
     */
    #[Zaglavlja(vrsta: Vrsta::JSON)]
    public function dodajSliku (string $kontroler = '', string $metoda = '', int $id = 0):Sadrzaj {

        try {

            // model
            $artikl = $this->model(PodKategorija_Model::class);
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