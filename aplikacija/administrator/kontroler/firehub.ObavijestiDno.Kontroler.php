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

use FireHub\Aplikacija\Administrator\Model\Kategorije_Model;
use FireHub\Aplikacija\Administrator\Model\ObavijestDno_Model;
use FireHub\Aplikacija\Administrator\Model\ObavijestiDno_Model;
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
final class ObavijestiDno_Kontroler extends Master_Kontroler {

    /**
     * ## index
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function index ():Sadrzaj {

        return sadrzaj()->datoteka('obavijestidno/lista.html')->podatci([]);

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
            $obavijesti = $this->model(ObavijestiDno_Model::class);

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'da',
                'ObavijestiDno' => $obavijesti->lista($broj_stranice, $poredaj, $redoslijed),
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

        $obavijest_model = $this->model(ObavijestDno_Model::class);
        $obavijest = $obavijest_model->obavijest($id);

        // formatiranje rezultata
        if ($obavijest['Ba'] === true) {$obavijest['Ba'] = 'checked';} else {$obavijest['Ba'] = '';}
        if ($obavijest['Hr'] === true) {$obavijest['Hr'] = 'checked';} else {$obavijest['Hr'] = '';}

        // kategorije
        $kategorije_model = $this->model(Kategorije_Model::class);
        $kategorije = $kategorije_model->lista(limit_zapisa_po_stranici: 100);
        $kategorije_html = '';
        foreach ($kategorije as $kategorija) {

            $kategorije_html .= "<option value='{$kategorija['ID']},0'>{$kategorija['Kategorija']}</option>";

            // podkategorije
            $podkategorije = $kategorije_model->podkategorije($kategorija['ID']);

            foreach ($podkategorije as $podkategorija) {

                $kategorije_html .= "<option value='{$kategorija['ID']},{$podkategorija['ID']}'>{$kategorija['Kategorija']} ->> {$podkategorija['Podkategorija']}</option>";

            }

        }

        return sadrzaj()->format(Sadrzaj_Vrsta::HTMLP)->datoteka('obavijestidno/uredi.html')->podatci([
            'id' => $obavijest['ID'],
            'redoslijed' => $obavijest['Redoslijed'],
            'kategorija' => ''.$obavijest['KategorijaID'].'',
            'kategorija_naziv' => ''.$obavijest['Kategorija'].'',
            'podkategorija' => ''.$obavijest['PodKategorijaID'].'' ?? '0',
            'podkategorija_naziv' => ''.$obavijest['PodKategorija'].'' ? ' ->> '.$obavijest['PodKategorija'] : '',
            'kategorije' => $kategorije_html,
            'ba' => $obavijest['Ba'],
            'hr' => $obavijest['Hr']
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
            $artikl = $this->model(ObavijestDno_Model::class);
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

        $obavijest_model = $this->model(ObavijestDno_Model::class);

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
            $artikl = $this->model(ObavijestDno_Model::class);
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