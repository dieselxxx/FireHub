<?php declare(strict_types = 1);

/**
 * Reklame
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

use FireHub\Aplikacija\Administrator\Model\Artikl_Model;
use FireHub\Aplikacija\Administrator\Model\Artikli_Model;
use FireHub\Aplikacija\Administrator\Model\Kategorije_Model;
use FireHub\Aplikacija\Administrator\Model\Reklame_Model;
use FireHub\Jezgra\Greske\Greska;
use FireHub\Jezgra\HTTP\Atributi\Zaglavlja;
use FireHub\Jezgra\HTTP\Enumeratori\Vrsta;
use FireHub\Jezgra\Sadrzaj\Enumeratori\Vrsta as Sadrzaj_Vrsta;
use FireHub\Jezgra\Sadrzaj\Sadrzaj;

/**
 * ### Reklame
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Kontroler
 */
final class Reklame_Kontroler extends Master_Kontroler {

    /**
     * ## index
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function index ():Sadrzaj {

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

        return sadrzaj()->datoteka('reklame.html')->podatci([
            'kategorije' => $kategorije_html
        ]);

    }

    /**
     * ### Spremi sliku artikla
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj
     */
    #[Zaglavlja(vrsta: Vrsta::JSON)]
    public function dodajSliku (string $kontroler = '', string $metoda = '', string $naziv_datoteke = ''):Sadrzaj {

        try {

            // model
            $artikl = $this->model(Reklame_Model::class);
            $artikl->dodajSliku($naziv_datoteke);

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