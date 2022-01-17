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
     * @param string $trazi [optional] <p>
     * Traži artikl.
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
    public function index (string $kontroler = '', string $kategorija = 'sve', string $trazi = 'sve', string $poredaj = 'naziv', string $poredaj_redoslijed = 'asc', int $stranica = 1):Sadrzaj {

        $kategorije = $this->model(Kategorije_Model::class);

        $trenutna_kategorija = $kategorije->kategorija($kategorija);

        $limit = 12;
        $artikli = $this->model(Artikli_Model::class)->artikli($trenutna_kategorija['ID'], ($stranica - 1) * $limit, $limit, $trazi, $poredaj, $poredaj_redoslijed);

        $navigacija = $this->model(Artikli_Model::class)->ukupnoRedakaHTML($trenutna_kategorija['ID'], $trazi, 12, '/rezultat/'.$trenutna_kategorija['Link'].'/'.$trazi.'/'.$poredaj.'/'.$poredaj_redoslijed, $stranica);

        $navigacija_html = '';
        foreach ($navigacija as $redak) {

            $navigacija_html .= $redak;

        }

        $artikli_html = '';
        foreach ($artikli as $artikal) {

            $artikli_html .= <<<Artikal
            
                <div class="artikal">
                    <img src="/slika/malaslika/{$artikal['Slika']}" alt="" loading="lazy"/>
                    <a class="naslov" href="/artikl/{$artikal['Link']}">{$artikal['Naziv']}</a>
                    <span class="cijena">{$artikal['Cijena']} KM</span>
                    <span class="zaliha"></span>
                </div>

            Artikal;

        }

        // poredaj izbornik
        if ($poredaj === 'naziv' && $poredaj_redoslijed == 'asc') {$poredaj_izbornik_odabrano_1 = 'selected';} else {$poredaj_izbornik_odabrano_1 = '';}
        if ($poredaj === 'naziv' && $poredaj_redoslijed == 'desc') {$poredaj_izbornik_odabrano_2 = 'selected';} else {$poredaj_izbornik_odabrano_2 = '';}
        if ($poredaj === 'cijena' && $poredaj_redoslijed == 'asc') {$poredaj_izbornik_odabrano_3 = 'selected';} else {$poredaj_izbornik_odabrano_3 = '';}
        if ($poredaj === 'cijena' && $poredaj_redoslijed == 'desc') {$poredaj_izbornik_odabrano_4 = 'selected';} else {$poredaj_izbornik_odabrano_4 = '';}

        $poredaj_izbornik = '
            <option value="/rezultat/'.$kategorija.'/'.$trazi.'/naziv/asc/" '.$poredaj_izbornik_odabrano_1.'>Naziv A-Z</option>
            <option value="/rezultat/'.$kategorija.'/'.$trazi.'/naziv/desc/" '.$poredaj_izbornik_odabrano_2.'>Naziv Z-A</option>
            <option value="/rezultat/'.$kategorija.'/'.$trazi.'/cijena/asc/" '.$poredaj_izbornik_odabrano_3.'>Cijena manja prema većoj</option>
            <option value="/rezultat/'.$kategorija.'/'.$trazi.'/cijena/desc/" '.$poredaj_izbornik_odabrano_4.'>Cijena veća prema manjoj</option>
        ';

        return sadrzaj()->datoteka('rezultat.html')->podatci([
            'predlozak_naslov' => $trenutna_kategorija['Kategorija'],
            'glavni_meni' => $kategorije->glavniMeni(),
            'glavni_meni_hamburger' => $kategorije->glavniMeniHamburger(),
            'vi_ste_ovdje' => 'Vi ste ovdje : <a href="/">Kapriol Web Trgovina</a> \\\\ ' . $trenutna_kategorija['Kategorija'] . ' \\\\ ' . $trazi,
            'artikli' => $artikli_html,
            'navigacija' => $navigacija_html,
            "poredaj_izbornik" => $poredaj_izbornik
        ]);

    }

}