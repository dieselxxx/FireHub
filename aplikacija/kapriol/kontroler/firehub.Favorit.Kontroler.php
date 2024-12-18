<?php declare(strict_types = 1);

/**
 * Favorit
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

use FireHub\Aplikacija\Kapriol\Jezgra\Domena;
use FireHub\Aplikacija\Kapriol\Model\Favorit_Model;
use FireHub\Aplikacija\Kapriol\Model\Gdpr_Model;
use FireHub\Aplikacija\Kapriol\Model\Kategorije_Model;
use FireHub\Jezgra\Kontejner\Greske\Kontejner_Greska;
use FireHub\Jezgra\Kontroler\Greske\Kontroler_Greska;
use FireHub\Jezgra\Sadrzaj\Sadrzaj;

/**
 * ### Favorit
 *
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Kontroler
 */
final class Favorit_Kontroler extends Master_Kontroler {

    /**
     * ### index
     * @since 0.1.2.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Log-a.
     * @throws Kontroler_Greska Ukoliko objekt nije validan model.
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function index ():Sadrzaj {

        $gdpr = $this->model(Gdpr_Model::class);

        $kategorije = $this->model(Kategorije_Model::class);

        $favorit_model = $this->model(Favorit_Model::class);

        // favoriti
        $favorit_artikli = $favorit_model->artikliFavorit();
        $artikli_html = '';
        if (!empty($favorit_artikli)) {

            foreach ($favorit_artikli as $artikal) {

                // cijene
                $euro_cijena = Domena::Hr() ? '<span style="font-size: 0.7rem">('.number_format((float)$artikal['Cijena'] * 7.5345, 2, ',', '.').' kn)</span>' : '';
                if ($artikal['CijenaAkcija'] > 0) {

                    $euro_cijena_akcija = Domena::Hr() ? '<span style="font-size: 0.8rem">('.number_format((float)$artikal['CijenaAkcija'] * 7.5345, 2, ',', '.').' kn)</span>' : '';

                    $artikl_cijena = '
                        <span class="akcija">'.number_format((float)$artikal['CijenaAkcija'], 2, ',', '.').' '.Domena::valuta().$euro_cijena_akcija.'</span>
                        <span class="prekrizi">'.number_format((float)$artikal['Cijena'], 2, ',', '.').' '.Domena::valuta().$euro_cijena.'</span>
                    ';

                } else {

                    $artikl_cijena = '
                        <span>'.number_format((float)$artikal['Cijena'], 2, ',', '.').' '.Domena::valuta().$euro_cijena.'</span>
                    ';

                }

                // artikli
                $artikli_html .= '
                    <form class="artikl" method="post" enctype="multipart/form-data" action="">
                        <input type="hidden" name="ID" value="'.$artikal['ID'].'">
                        <img src="/slika/malaslika/'.$artikal['Slika'].'" alt="" loading="lazy"/>
                        <a class="naslov" href="/artikl/'.$artikal['Link'].'">'.$artikal['Naziv'].'</a>
                        <span class="cijena">'.$artikl_cijena.'</span>
                        <div class="kosarica">
                            <button type="submit" class="gumb ikona" name="favorit_izbrisi">
                                <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#izbrisi"></use></svg>
                                <span>Izbriši</span>
                            </button>
                        </div>
                    </form>
                ';

            }

        }

        return sadrzaj()->datoteka('favoriti.html')->podatci([
            'predlozak_opis' => Domena::opis(),
            'predlozak_GA' => Domena::GA(),
            'predlozak_naslov' => 'Favoriti',
            'linkerRetargeting' => Domena::linkerRetargeting(),
            'facebook_link' => Domena::facebook(),
            'instagram_link' => Domena::instagram(),
            'mobitel' => Domena::mobitel(),
            'prodajni_predstavik' => Domena::prodajniPredstavnik(),
            'glavni_meni' => $kategorije->glavniMeni(),
            'glavni_meni_hamburger' => $kategorije->glavniMeniHamburger(),
            'zaglavlje_kosarica_artikli' => $this->kosaricaArtikli(),
            'zaglavlje_kosarica_artikli_html' => $this->kosaricaArtikliHTML(),
            'zaglavlje_favorit_artikli' => $this->favoritArtikli(),
            'zaglavlje_tel' => Domena::telefon(),
            'zaglavlje_adresa' => Domena::adresa(),
            'podnozje_dostava' => Domena::podnozjeDostava(),
            'gdpr' => $gdpr->html(),
            'vi_ste_ovdje' => '<a href="/">Kapriol Web Trgovina</a> \\\\ Favoriti',
            'opci_uvjeti' => Domena::opciUvjeti(),
            'favorit_artikli' => $artikli_html
        ]);

    }

}