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

use FireHub\Jezgra\Sadrzaj\Sadrzaj;
use FireHub\Aplikacija\Kapriol\Model\Kategorije_Model;
use FireHub\Aplikacija\Kapriol\Model\Artikli_Model;
use FireHub\Aplikacija\Kapriol\Jezgra\Domena;
use FireHub\Jezgra\Kontejner\Greske\Kontejner_Greska;
use FireHub\Jezgra\Kontroler\Greske\Kontroler_Greska;

/**
 * ### Rezultat
 * @since 0.1.1.pre-alpha.M1
 *
 * @package Aplikacija\Kontroler
 */
final class Rezultat_Kontroler extends Master_Kontroler {

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
     * @param int|string $trazi [optional] <p>
     * Traži artikl.
     * </p>
     * @param int|string $velicina [optional] <p>
     * Veličina artikla.
     * </p>
     * @param string $poredaj [optional] <p>
     * Poredaj rezultate artikala.
     * </p>
     * @param string $poredaj_redoslijed [optional] <p>
     * ASC ili DESC.
     * </p>
     * @param int $stranica [optional] <p>
     * Trenutna stranica.
     * </p.
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Log-a.
     * @throws Kontroler_Greska Ukoliko objekt nije validan model.
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function index (string $kontroler = '', string $kategorija = 'sve', int|string $velicina = 'sve velicine', int|string $trazi = 'svi artikli', string $poredaj = 'naziv', string $poredaj_redoslijed = 'asc', int $stranica = 1):Sadrzaj {

        $kategorije = $this->model(Kategorije_Model::class);

        $trenutna_kategorija = $kategorije->kategorija($kategorija);

        // izdvojeno
        $izdvojeno_html = '';
        $izdvojeno = $this->model(Artikli_Model::class)->izdvojeno($trenutna_kategorija['ID']);
        foreach ($izdvojeno as $artikal) {

            // cijene
            if ($artikal['CijenaAkcija'] > 0) {

                $artikal_popust = -($artikal['Cijena'] - $artikal['CijenaAkcija']) / ($artikal['Cijena']) * 100;

                $artikl_cijena = '
                <span class="prekrizi">'.number_format((float)$artikal['Cijena'], 2, ',', '.').' '.Domena::valuta().'</span>
                <h2 class="akcija">'.number_format((float)$artikal['CijenaAkcija'], 2, ',', '.').' '.Domena::valuta().'</h2>
                <span class="popust">'.number_format($artikal_popust, 2, ',').' %</span>
            ';

            } else {

                $artikl_cijena = '
                <h2>'.number_format((float)$artikal['Cijena'], 2, ',', '.').' '.Domena::valuta().'</h2>
            ';

            }

            $izdvojeno_html .= <<<Artikal
            
                <a class="artikal" href="/artikl/{$artikal['Link']}">
                    <img src="/slika/malaslika/{$artikal['Slika']}" alt="" loading="lazy"/>
                    <span class="naslov">{$artikal['Naziv']}</span>
                    <span class="cijena">$artikl_cijena</span>
                    <span class="zaliha"></span>
                </a>

            Artikal;

        }
        if (!empty($izdvojeno_html)) {$izdvojeno_html = '<span>Izdvojeni artikli</span>' . $izdvojeno_html;}

        // navigacija
        $limit = 12;
        $artikli = $this->model(Artikli_Model::class)->artikli($trenutna_kategorija['ID'], ($stranica - 1) * $limit, $limit, $velicina, $trazi, $poredaj, $poredaj_redoslijed);
        $navigacija = $this->model(Artikli_Model::class)->ukupnoRedakaHTML($trenutna_kategorija['ID'], $velicina, $trazi, 12, '/rezultat/'.$trenutna_kategorija['Link'].'/'.$velicina.'/'.$trazi.'/'.$poredaj.'/'.$poredaj_redoslijed, $stranica);
        $navigacija_html = implode('', $navigacija);

        // artikli
        $artikli_html = '';
        foreach ($artikli as $artikal) {

            // cijene
            if ($artikal['CijenaAkcija'] > 0) {

                $artikal_popust = -($artikal['Cijena'] - $artikal['CijenaAkcija']) / ($artikal['Cijena']) * 100;

                $artikl_cijena = '
                <span class="prekrizi">'.number_format((float)$artikal['Cijena'], 2, ',', '.').' '.Domena::valuta().'</span>
                <h2 class="akcija">'.number_format((float)$artikal['CijenaAkcija'], 2, ',', '.').' '.Domena::valuta().'</h2>
                <span class="popust">'.number_format($artikal_popust, 2, ',').' %</span>
            ';

            } else {

                $artikl_cijena = '
                <h2>'.number_format((float)$artikal['Cijena'], 2, ',', '.').' '.Domena::valuta().'</h2>
            ';

            }

            $artikli_html .= <<<Artikal
            
                <a class="artikal" href="/artikl/{$artikal['Link']}">
                    <img src="/slika/malaslika/{$artikal['Slika']}" alt="" loading="lazy"/>
                    <span class="naslov">{$artikal['Naziv']}</span>
                    <span class="cijena">$artikl_cijena</span>
                    <span class="zaliha"></span>
                </a>

            Artikal;

        }

        // poredaj izbornik
        if ($poredaj === 'naziv' && $poredaj_redoslijed == 'asc') {$poredaj_izbornik_odabrano_1 = 'selected';} else {$poredaj_izbornik_odabrano_1 = '';}
        if ($poredaj === 'naziv' && $poredaj_redoslijed == 'desc') {$poredaj_izbornik_odabrano_2 = 'selected';} else {$poredaj_izbornik_odabrano_2 = '';}
        if ($poredaj === 'cijena' && $poredaj_redoslijed == 'asc') {$poredaj_izbornik_odabrano_3 = 'selected';} else {$poredaj_izbornik_odabrano_3 = '';}
        if ($poredaj === 'cijena' && $poredaj_redoslijed == 'desc') {$poredaj_izbornik_odabrano_4 = 'selected';} else {$poredaj_izbornik_odabrano_4 = '';}

        $poredaj_izbornik = '
            <option value="/rezultat/'.$kategorija.'/'.$velicina.'/'.$trazi.'/naziv/asc/" '.$poredaj_izbornik_odabrano_1.'>Naziv A-Z</option>
            <option value="/rezultat/'.$kategorija.'/'.$velicina.'/'.$trazi.'/naziv/desc/" '.$poredaj_izbornik_odabrano_2.'>Naziv Z-A</option>
            <option value="/rezultat/'.$kategorija.'/'.$velicina.'/'.$trazi.'/cijena/asc/" '.$poredaj_izbornik_odabrano_3.'>Cijena manja prema većoj</option>
            <option value="/rezultat/'.$kategorija.'/'.$velicina.'/'.$trazi.'/cijena/desc/" '.$poredaj_izbornik_odabrano_4.'>Cijena veća prema manjoj</option>
        ';

        // veličine
        //$velicine = $this->model(Artikli_Model::class)->velicine($trenutna_kategorija['ID'], $trazi);
        //$velicine_html = '';
        //foreach ($velicine as $velicina_artikla) {
            //$velicine_html .= '<li><a class="gumb mali" href="/rezultat/'.$trenutna_kategorija['Link'].'/'.$velicina_artikla['Velicina'].'/'.$trazi.'/'.$poredaj.'/'.$poredaj_redoslijed.'">'.$velicina_artikla['Velicina'].'</a></li>';
        //}

        return sadrzaj()->datoteka('rezultat.html')->podatci([
            'predlozak_naslov' => $trenutna_kategorija['Kategorija'],
            'facebook_link' => Domena::facebook(),
            'mobitel' => Domena::mobitel(),
            'glavni_meni' => $kategorije->glavniMeni(),
            'glavni_meni_hamburger' => $kategorije->glavniMeniHamburger(),
            'zaglavlje_kosarica_artikli' => $this->kosaricaArtikli(),
            'zaglavlje_tel' => Domena::telefon(),
            'zaglavlje_adresa' => Domena::adresa(),
            'podnozje_dostava' => Domena::podnozjeDostava(),
            'vi_ste_ovdje' => 'Vi ste ovdje : <a href="/">Kapriol Web Trgovina</a> \\\\ ' . $trenutna_kategorija['Kategorija'] . ' \\\\ ' . $velicina . ' \\\\ ' . $trazi,
            'izdvojeno' => $izdvojeno_html,
            'artikli' => $artikli_html,
            'navigacija' => $navigacija_html,
            "poredaj_izbornik" => $poredaj_izbornik,
            //"rezultat_velicine" => $velicine_html
        ]);

    }

}