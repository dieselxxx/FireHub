<?php declare(strict_types = 1);

/**
 * Kosarica
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

use FireHub\Jezgra\Sadrzaj\Sadrzaj;
use FireHub\Aplikacija\Kapriol\Model\Kategorije_Model;
use FireHub\Aplikacija\Kapriol\Model\Kosarica_Model;
use FireHub\Jezgra\Kontejner\Greske\Kontejner_Greska;
use FireHub\Jezgra\Kontroler\Greske\Kontroler_Greska;

/**
 * ### Kosarica
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Kontroler
 */
final class Kosarica_Kontroler extends Master_Kontroler {

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

        $kategorije = $this->model(Kategorije_Model::class);

        $kosarica_model = $this->model(Kosarica_Model::class);

        // artikli
        $kosarica_artikli = $kosarica_model->artikli();
        $artikli_html = '';
        $kosarica_artikli_ukupno = '';
        $total_kolicina = 0;
        $total_cijena = 0;
        if (!empty($kosarica_artikli)) {

            foreach ($kosarica_artikli as $artikal) {

                // cijene
                if ($artikal['CijenaAkcija'] > 0) {

                    $artikl_cijena = '
                        <span class="akcija">'.number_format((float)$artikal['CijenaAkcija'], 2, ',', '.').' KM</span>
                        <span class="prekrizi">'.number_format((float)$artikal['Cijena'], 2, ',', '.').' KM</span>
                    ';

                } else {

                    $artikl_cijena = '
                        <span>'.number_format((float)$artikal['Cijena'], 2, ',', '.').' KM</span>
                    ';

                }

                // artikli
                $artikli_html .= '
                    <form class="artikl" method="post" enctype="multipart/form-data" action="">
                        <img src="/slika/malaslika/'.$artikal['Slika'].'" alt="" loading="lazy"/>
                        <a class="naslov" href="/artikl/'.$artikal['Link'].'">'.$artikal['Naziv'].'</a>
                        <span class="velicina">Veličina: '.$artikal['Velicina'].'</span>
                        <span class="cijena">'.$artikl_cijena.'</span>
                        <h3 class="ukupno">Ukupno: '.number_format((float)$artikal['CijenaUkupno'], 2, ',', '.').'</h3>
                        <div class="kosarica">
                            <button type="button" class="gumb" onclick="ArtikalPlusMinus(this, $vrsta = \'minus\');">-</button>
                            <label data-boja="boja" class="unos">
                                <input type="hidden" name="velicina" value="'.$artikal['Sifra'].'">
                                <input type="number" name="vrijednost" value="'.$artikal['Kolicina'].'" data-pakiranje="1" data-maxpakiranje="1000" value="0" min="0" max="100" step="1" autocomplete="off" pattern="0-9">
                                <span class="naslov">
                                    <span></span>
                                </span>
                                <span class="granica"></span>
                            </label>
                            <button type="button" class="gumb" onclick="ArtikalPlusMinus(this, $vrsta = \'plus\');">+</button>
                            <button type="submit" class="gumb ikona" name="kosarica_izmijeni">
                                <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#uredi"></use></svg>
                                <span>Izmijeni</span>
                            </button>
                            <button type="submit" class="gumb ikona" name="kosarica_izbrisi">
                                <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#izbrisi"></use></svg>
                                <span>Izbriši</span>
                            </button>
                        </div>
                    </form>
                ';

                // ukupno
                $total_kolicina += $artikal['Kolicina'];
                $total_cijena += $artikal['CijenaUkupno'];

                $kosarica_artikli_ukupno = '
                    <ul>
                        <li>Ukupna količina: '.$total_kolicina.'</li>
                        <li class="ukupno">Ukupna cijena: <span>'.number_format((float)$total_cijena, 2, ',', '.').' KM</span></li>
                    </ul>
                    <a data-boja="boja" class="gumb ikona" href="/kosarica/narudzba">
                        <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno_duplo2"></use></svg>
                        <span>Nastavi</span>
                    </a>
                ';

            }

        } else {

            $artikli_html = '<h2>Vaša košarica je prazna!</h2>';

        }

        return sadrzaj()->datoteka('kosarica.html')->podatci([
            'predlozak_naslov' => 'Košarica',
            'glavni_meni' => $kategorije->glavniMeni(),
            'glavni_meni_hamburger' => $kategorije->glavniMeniHamburger(),
            'vi_ste_ovdje' => '<a href="/">Kapriol Web Trgovina</a> \\\\ Košarica',
            'kosarica_artikli' => $artikli_html,
            'kosarica_artikli_ukupno' => $kosarica_artikli_ukupno
        ]);

    }

    /**
     * ### Narudzba
     * @since 0.1.2.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Log-a.
     * @throws Kontroler_Greska Ukoliko objekt nije validan model.
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function narudzba ():Sadrzaj {

        $kategorije = $this->model(Kategorije_Model::class);

        $kosarica_model = $this->model(Kosarica_Model::class);

        return sadrzaj()->datoteka('narudzba.html')->podatci([
            'predlozak_naslov' => 'Narudžba',
            'glavni_meni' => $kategorije->glavniMeni(),
            'glavni_meni_hamburger' => $kategorije->glavniMeniHamburger(),
            'vi_ste_ovdje' => '<a href="/">Kapriol Web Trgovina</a> \\\\ Narudžba'
        ]);

    }

}