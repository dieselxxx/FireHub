<?php declare(strict_types = 1);

/**
 * Rezultat
 * @since 0.1.1.pre-alpha.M1
 *
 * @author Danijel Galić
 * @copyright 2022 Kapriol Web Trgovina
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Aplikacija\Kontroler
 */

namespace FireHub\Aplikacija\Kapriol\Kontroler;

use FireHub\Jezgra\Kontroler\Kontroler;
use FireHub\Jezgra\Sadrzaj\Sadrzaj;
use FireHub\Aplikacija\Kapriol\Model\Kategorije_Model;
use FireHub\Aplikacija\Kapriol\Model\Artikli_Model;
use FireHub\Jezgra\Kontejner\Greske\Kontejner_Greska;
use FireHub\Jezgra\Kontroler\Greske\Kontroler_Greska;

/**
 * ### Rezultat
 * @since 0.1.1.pre-alpha.M1
 *
 * @package Aplikacija\Kontroler
 */
final class Rezultat_Kontroler extends Kontroler {

    /**
     * ## index
     * @since 0.1.0.pre-alpha.M1
     *
     * @param string $kontroler [optional] <p>
     * Trenutni kontroler.
     * </p>
     * @param string $kategorija [optional] <p>
     * Trenutna kategorija.
     * </p>
     * @param int $stranica [optional] <p>
     * Trenutna stranica.
     * </p>
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Log-a.
     * @throws Kontroler_Greska Ukoliko objekt nije validan model.
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function index (string $kontroler = '', string $kategorija = '', int $stranica = 1):Sadrzaj {

        $kategorije = $this->model(Kategorije_Model::class);

        $trenutna_kategorija = $kategorije->kategorija($kategorija);

        $limit = 12;
        $artikli = $this->model(Artikli_Model::class)->artikli($trenutna_kategorija['ID'], ($stranica - 1) * $limit, $limit);

        $artikli_html = '';
        foreach ($artikli as $artikal) {

            $artikli_html .= <<<Artikal
            
                <div class="artikal">
                    <img src="/kapriol/resursi/grafika/ikone/kapriol_siva.svg" alt=""/>
                    <span class="naslov">{$artikal['Naziv']}</span>
                    <span class="cijena">{$artikal['Cijena']} KM</span>
                    <span class="zaliha"></span>
                </div>

            Artikal;

        }

        return sadrzaj()->datoteka('rezultat.html')->podatci([
            'predlozak_naslov' => $trenutna_kategorija['Kategorija'],
            'kategorije' => $kategorije->glavni_meni(),
            'vi_ste_ovdje' => 'Vi ste ovdje : <a href="/">Kapriol Web Trgovina</a> \\\\ ' . $trenutna_kategorija['Kategorija'],
            'artikli' => $artikli_html
        ]);

    }

}