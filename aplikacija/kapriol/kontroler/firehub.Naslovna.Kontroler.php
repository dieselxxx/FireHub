<?php declare(strict_types = 1);

/**
 * Naslovna
 * @since 0.1.0.pre-alpha.M1
 *
 * @author Danijel Galić
 * @copyright 2022 Kapriol Web Trgovina
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Aplikacija\Kontroler
 */

namespace FireHub\Aplikacija\Kapriol\Kontroler;

use FireHub\Aplikacija\Kapriol\Model\Gdpr_Model;
use FireHub\Jezgra\Komponente\BazaPodataka\BazaPodataka;
use FireHub\Jezgra\Sadrzaj\Sadrzaj;
use FireHub\Aplikacija\Kapriol\Model\Kategorije_Model;
use FireHub\Aplikacija\Kapriol\Jezgra\Domena;
use FireHub\Jezgra\Kontejner\Greske\Kontejner_Greska;
use FireHub\Jezgra\Kontroler\Greske\Kontroler_Greska;

/**
 * ### Naslovna
 * @since 0.1.0.pre-alpha.M1
 *
 * @package Aplikacija\Kontroler
 */
final class Naslovna_Kontroler extends Master_Kontroler {

    /**
     * ## index
     * @since 0.1.0.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Log-a.
     * @throws Kontroler_Greska Ukoliko objekt nije validan model.
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function index (BazaPodataka $bazaPodataka = null):Sadrzaj {

        $gdpr = $this->model(Gdpr_Model::class);

        $kategorije = $this->model(Kategorije_Model::class);

        // obavijesti
        $obavijest_html = '';
        $obavijesti = $bazaPodataka->tabela('obavijesti')
            ->sirovi("
                SELECT 
                    Obavijest, artikliview.Link
                FROM obavijesti
                LEFT JOIN artikliview ON artikliview.ID = obavijesti.ArtikalID
                WHERE obavijesti.".Domena::sqlTablica()." = 1
                ORDER BY obavijesti.Redoslijed ASC
            ")->napravi();

        foreach ($obavijesti->niz() as $obavijest) {

            $link = $obavijest['Link'] ? 'href="/artikl/'.$obavijest['Link'].'"' : '' ;

            // srcset='elva-fairy-480w.jpg 480w, elva-fairy-800w.jpg 800w' sizes='(max-width: 600px) 480px, 800px' src='elva-fairy-800w.jpg'

            $obavijest_html .= "
            <a class='swiper-slide' $link>
                <img sizes='(max-width: 600px) 600px, 1024px' srcset='/slika/baner/{$obavijest['Obavijest']}/700/1400 1024w, /slika/baner/{$obavijest['Obavijest']}/300/600 600w' src='/slika/baner/{$obavijest['Obavijest']}/300/600' />
            </a>
            ";

        }

        // obavijestidno
        $obavijestdno_html = '';
        $obavijestidno = $bazaPodataka->tabela('obavijesti')
            ->sirovi("
                SELECT 
                    obavijestidno.Obavijest,
                    kategorijeview.Link AS KategorijaLink, podkategorijeview.Link AS PodKategorijaLink
                FROM obavijestidno
                LEFT JOIN kategorijeview ON kategorijeview.ID = obavijestidno.KategorijaID
                LEFT JOIN podkategorijeview ON podkategorijeview.ID = obavijestidno.PodKategorijaID
                WHERE obavijestidno.".Domena::sqlTablica()." = 1
                ORDER BY obavijestidno.Redoslijed ASC
            ")->napravi();

        $obavijestidno = $obavijestidno->niz() ?: [];

        foreach ($obavijestidno as $obavijest) {

            $kategorija_link = $obavijest['KategorijaLink'] ?: 'sve' ;
            $podkategorija_link = $obavijest['PodKategorijaLink'] ?: 'sve' ;

            $obavijestdno_html .= "
            <a class='swiper-slide' href='/rezultat/".$kategorija_link."/".$podkategorija_link."'>
                <img sizes='(max-width: 600px) 600px, 1024px' srcset='/slika/banerdno/{$obavijest['Obavijest']}/700/1400 1024w, /slika/banerdno/{$obavijest['Obavijest']}/300/600 600w' src='/slika/banerdno/{$obavijest['Obavijest']}/300/600' />
            </a>
            ";

        }

        return sadrzaj()->datoteka('naslovna.html')->podatci([
            'predlozak_naslov' => 'Naslovna',
            'facebook_link' => Domena::facebook(),
            'instagram_link' => Domena::instagram(),
            'mobitel' => Domena::mobitel(),
            'glavni_meni' => $kategorije->glavniMeni(),
            'opci_uvjeti' => Domena::opciUvjeti(),
            'glavni_meni_hamburger' => $kategorije->glavniMeniHamburger(),
            'zaglavlje_kosarica_artikli' => $this->kosaricaArtikli(),
            'zaglavlje_kosarica_artikli_html' => $this->kosaricaArtikliHTML(),
            'zaglavlje_favorit_artikli' => $this->favoritArtikli(),
            'zaglavlje_tel' => Domena::telefon(),
            'zaglavlje_adresa' => Domena::adresa(),
            'podnozje_dostava' => Domena::podnozjeDostava(),
            'kategorije' => $kategorije->kategorijeNaslovna(),
            'gdpr' => $gdpr->html(),
            'dostavaLimit' => ''.Domena::dostavaLimit().'',
            'valuta' => ''.Domena::valuta().'',
            'obavijesti' => $obavijest_html,
            'obavijestidno' => $obavijestdno_html,
            'reklama1vrijeme' => ''.filemtime(APLIKACIJA_ROOT.'../../'.konfiguracija('sustav.putanje.web').'kapriol/resursi/grafika/reklame/reklama1.jpg').''
        ]);

    }

}