<?php declare(strict_types = 1);

/**
 * Obavijesti
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

use FireHub\Aplikacija\Administrator\Model\Artikli_Model;
use FireHub\Aplikacija\Administrator\Model\Obavijest_Model;
use FireHub\Aplikacija\Administrator\Model\Obavijesti_Model;
use FireHub\Jezgra\Greske\Greska;
use FireHub\Jezgra\HTTP\Atributi\Zaglavlja;
use FireHub\Jezgra\HTTP\Enumeratori\Vrsta;
use FireHub\Jezgra\Sadrzaj\Enumeratori\Vrsta as Sadrzaj_Vrsta;
use FireHub\Jezgra\Sadrzaj\Sadrzaj;

/**
 * ### Obavijesti
 *
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Kontroler
 */
final class Obavijesti_Kontroler extends Master_Kontroler {

    /**
     * ## index
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function index ():Sadrzaj {

        return sadrzaj()->datoteka('obavijesti/lista.html')->podatci([]);

    }

    /**
     * ## Lista artikala
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    #[Zaglavlja(vrsta: Vrsta::JSON)]
    public function lista (string $kontroler = '', string $metoda = '', int $broj_stranice = 1, string $poredaj = 'Obavijest', string $redoslijed = 'asc'):Sadrzaj {

        try {

            // model
            $obavijesti = $this->model(Obavijesti_Model::class);

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'da',
                'Obavijesti' => $obavijesti->lista($broj_stranice, $poredaj, $redoslijed),
                'Zaglavlje' => $obavijesti->IspisiZaglavlje(),
                'Navigacija' => $obavijesti->IspisiNavigaciju()
            ]);

        } catch (Greska $greska) {

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'ne',
                'Poruka' => $greska->getMessage()
            ]);

        }

    }

    /**
     * ## Uredi obavijest
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function uredi (string $kontroler = '', string $metoda = '', int $id = 0) {

        $obavijest_model = $this->model(Obavijest_Model::class);
        $obavijest = $obavijest_model->obavijest($id);

        // formatiranje rezultata
        if ($obavijest['Ba'] === true) {$obavijest['Ba'] = 'checked';} else {$obavijest['Ba'] = '';}
        if ($obavijest['Hr'] === true) {$obavijest['Hr'] = 'checked';} else {$obavijest['Hr'] = '';}

        // artikli
        $artikli_model = $this->model(Artikli_Model::class);
        $artikli_model->limit_zapisa_po_stranici = 10000;
        $artikli = $artikli_model->lista();
        $artikli_html = '';
        foreach ($artikli as $artikl) {

            $artikli_html .= "<option value='{$artikl['ID']}'>{$artikl['Naziv']}</option>";

        }

        return sadrzaj()->format(Sadrzaj_Vrsta::HTMLP)->datoteka('obavijesti/uredi.html')->podatci([
            'id' => $obavijest['ID'],
            'redoslijed' => $obavijest['Redoslijed'],
            'ba' => $obavijest['Ba'],
            'hr' => $obavijest['Hr'],
            'artikal' => is_null($obavijest['ArtikalID']) ? '' : $obavijest['ArtikalID'],
            'artikal_naziv' => is_null($obavijest['Naziv']) ? '' : $obavijest['Naziv'],
            'artikli' => $artikli_html,
            'linkBA' => is_null($obavijest['LinkHR']) ? '' : ''.$obavijest['LinkBA'].'',
            'linkHR' => is_null($obavijest['LinkHR']) ? '' : ''.$obavijest['LinkHR'].''
        ]);

    }

    /**
     * ## Spremi obavijest
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function spremi (string $kontroler = '', string $metoda = '', int $id = 0) {

        try {

            // model
            $artikl = $this->model(Obavijest_Model::class);
            $artikl->spremi($id);

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
     * ## Uredi obavijest
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function izbrisi (string $kontroler = '', string $metoda = '', int $id = 0) {

        $obavijest_model = $this->model(Obavijest_Model::class);

        try {

            $obavijest = $obavijest_model->izbrisi($id);

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'da'
            ]);

        } catch (Greska $greska) {

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'ne',
                'Poruka' => $greska->getMessage()
            ]);

        }

    }

    /**
     * ### Spremi reklamu
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj
     */
    #[Zaglavlja(vrsta: Vrsta::JSON)]
    public function dodaj (string $kontroler = '', string $metoda = '', string $naziv_datoteke = ''):Sadrzaj {

        try {

            // model
            $artikl = $this->model(Obavijest_Model::class);
            $artikl->dodaj($naziv_datoteke);

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