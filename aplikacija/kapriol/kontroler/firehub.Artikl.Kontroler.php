<?php declare(strict_types = 1);

/**
 * Artikl
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

use FireHub\Aplikacija\Kapriol\Jezgra\Server;
use FireHub\Aplikacija\Kapriol\Model\Favorit_Model;
use FireHub\Aplikacija\Kapriol\Model\Gdpr_Model;
use FireHub\Jezgra\Sadrzaj\Sadrzaj;
use FireHub\Aplikacija\Kapriol\Model\Kategorije_Model;
use FireHub\Aplikacija\Kapriol\Model\Artikl_Model;
use FireHub\Aplikacija\Kapriol\Jezgra\Domena;
use FireHub\Aplikacija\Kapriol\Jezgra\Validacija;
use FireHub\Aplikacija\Kapriol\Model\Kosarica_Model;
use FireHub\Jezgra\Kontejner\Greske\Kontejner_Greska;
use FireHub\Jezgra\Kontroler\Greske\Kontroler_Greska;

/**
 * ### Artikl
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Kontroler
 */
final class Artikl_Kontroler extends Master_Kontroler {

    /**
     * ## index
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $kontroler [optional] <p>
     * Trenutni kontroler.
     * </p>
     * @param string $artikl [optional] <p>
     * Naziv artikla.
     * </p>
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Log-a.
     * @throws Kontroler_Greska Ukoliko objekt nije validan model.
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function index (string $kontroler = '', string $artikl = ''):Sadrzaj {

        $gdpr = $this->model(Gdpr_Model::class);

        $kategorije = $this->model(Kategorije_Model::class);

        $artikl_model = $this->model(Artikl_Model::class);

        $trenutni_artikl = $artikl_model->artikl($artikl);

        $artikl_zaliha = $artikl_model->zaliha($trenutni_artikl['ID']);

        if ($trenutni_artikl['ID'] === 0 || empty($artikl_zaliha)) {

            return sadrzaj()->datoteka('artikl_ne_postoji.html')->podatci([
                'predlozak_opis' => Domena::opis(),
                'predlozak_GA' => Domena::GA(),
                'predlozak_naslov' => $trenutni_artikl['Naziv'],
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
                "google_artikal" => '',
                'vi_ste_ovdje' => 'Vi ste ovdje : <a href="/">Kapriol Web Trgovina</a> \\\\ '.$trenutni_artikl['Kategorija'].' \\\\ ' . $trenutni_artikl['Naziv']
            ]);

        }

        // slike
        $artikl_slike = $artikl_model->slike($trenutni_artikl['ID']);
        $artikl_slike_html = '';
        foreach ($artikl_slike as $slike) {

            $artikl_slike_html .= '
                <li>
                    <a data-vrsta="slika" href="/slika/velikaslika/'.$slike['Slika'].'"><img src="/slika/malaslika/'.$slike['Slika'].'" alt=""></a>
                </li>';

        }

        // cijene
        $najnize_cijena = Domena::Hr()
            ? '<span style="font-size: 0.7rem">
                    najniža cijena u posljednih 30 dana: '.$trenutni_artikl['Cijena30Dana'] .' '.Domena::valuta().'
                    </span>'
            : '';

        if ($trenutni_artikl['CijenaAkcija'] > 0) {

            $artikal_popust = -($trenutni_artikl['Cijena'] - $trenutni_artikl['CijenaAkcija']) / ($trenutni_artikl['Cijena']) * 100;

            $artikl_cijena = '
                <span class="prekrizi">'.number_format((float)$trenutni_artikl['Cijena'], 2, ',', '.').' '.Domena::valuta().'</span>
                <h2 class="akcija">'.number_format((float)$trenutni_artikl['CijenaAkcija'], 2, ',', '.').' '.Domena::valuta().'</h2>
                '.$najnize_cijena.'
                <span class="popust">'.number_format($artikal_popust, 2, ',').' %</span>
            ';

        } else if (Domena::blackFriday()) {

            $bf_cijena = $trenutni_artikl['Cijena'] - ($trenutni_artikl['Cijena'] * Domena::blackFridayPopust());
            $artikl_cijena = '
                <img alt="bf" src="/kapriol/resursi/grafika/logo/bf.png">
                <h2 style="margin-top: 10px;">'.number_format((float)$bf_cijena, 2, ',', '.').' '.Domena::valuta().'</h2>
                <span class="prekrizi">'.number_format((float)$trenutni_artikl['Cijena'], 2, ',', '.').' '.Domena::valuta().'</span>
                '.$najnize_cijena.'
            ';

        } else {

            $artikl_cijena = '
                <h2>'.number_format((float)$trenutni_artikl['Cijena'], 2, ',', '.').' '.Domena::valuta().'</h2>
                '.$najnize_cijena.'
            ';

        }

        // zaliha
        $artikl_zaliha_html = '<h3>Odaberite veličinu :</h3>';
        $artikl_kosarica_velicine = '';
        foreach ($artikl_zaliha as $zaliha) {

            if ((int)$zaliha['StanjeSkladisteTF'] === 1 && count($artikl_zaliha) === 1 && $artikl_zaliha[0]['Velicina'] === 'uni') {

                $artikl_zaliha_html = '';
                $artikl_kosarica_velicine .= '';

            } else if ((int)$zaliha['StanjeSkladisteTF'] === 1) {

                $artikl_zaliha_html .= '
                <li>
                    <div class="sifraArtikla radio" data-tippy-content="'.$zaliha['artiklikarakteristikeSifra'].'">
                        <input id="'.$zaliha['Velicina'].'" type="radio" name="velicina" value="'.$zaliha['artiklikarakteristikeSifra'].'">
                        <label for="'.$zaliha['Velicina'].'">'.$zaliha['Velicina'].'</label>
                    </div>
                </li>';

                $artikl_kosarica_velicine .= '<option value="'.$zaliha['artiklikarakteristikeSifra'].'">'.$zaliha['Velicina'].'</option>';

            } else {

                $artikl_zaliha_html .= '
                <li>
                    <div class="radio">
                        <input id="'.$zaliha['Velicina'].'" type="radio" name="velicina" value="'.$zaliha['artiklikarakteristikeSifra'].'" disabled>
                        <label for="'.$zaliha['Velicina'].'">'.$zaliha['Velicina'].'</label>
                    </div>
                </li>';

            }

        }

        // gratis
        $artikl_gratis_zaliha_html = '';
        $artikl_gratis_html = '';
        if ($trenutni_artikl['GratisNaziv']) {

            $artikl_gratis_zaliha_html = '
                <span><b style="font-size: 1rem;">KOD NARUČIVANJA MORATE ODABRATI OBAVEZNO I VELIČINU GRATIS ARTIKLA!</b> <br><h4>'.$trenutni_artikl['GratisNaziv'].'</h4></span>
            
                <div class="gratis_slika">
                    <h4>GRATIS</h4>
                    <img src="/slika/malaslika/'.$trenutni_artikl['GratisSlika'].'" alt="" loading="lazy"/>
                    <span><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#gratis"></use></svg></span>
                </div>
            ';

            $artikl_gratis_html = '<svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#gratis"></use></svg>';

            $artikl_gratis_zaliha = $artikl_model->zaliha($trenutni_artikl['GratisID']);
            foreach ($artikl_gratis_zaliha as $zaliha) {

                if ((int)$zaliha['StanjeSkladisteTF'] === 1) {

                    $artikl_gratis_zaliha_html .= '
                    <li>
                        <div class="sifraArtikla radio" data-tippy-content="'.$zaliha['artiklikarakteristikeSifra'].'">
                            <input id="gratis-'.$zaliha['Velicina'].'" type="radio" name="gratis" value="'.$zaliha['artiklikarakteristikeSifra'].'">
                            <label for="gratis-'.$zaliha['Velicina'].'">'.$zaliha['Velicina'].'</label>
                        </div>
                    </li>';

                    $artikl_kosarica_velicine .= '<option value="'.$zaliha['artiklikarakteristikeSifra'].'">'.$zaliha['Velicina'].'</option>';

                } else {

                    $artikl_gratis_zaliha_html .= '
                    <li>
                        <div class="radio">
                            <input id="gratis-'.$zaliha['Velicina'].'" type="radio" name="gratis" value="'.$zaliha['artiklikarakteristikeSifra'].'" disabled>
                            <label for="gratis-'.$zaliha['Velicina'].'">'.$zaliha['Velicina'].'</label>
                        </div>
                    </li>';

                }

            }
        }

        // greške
        $kosarica_greska = '';
        if (isset($_POST['kosarica_dodaj'])) {

            try {

                if ($trenutni_artikl['GratisNaziv'] && !isset($_POST['gratis'])) {

                    throw new Kontroler_Greska('Molimo odaberite veličinu gratis artikla!');

                }

                if (isset($_POST['velicina'])) {

                    $velicina = Validacija::String('Veličina', $_POST['velicina'], 1, 10);

                    if (isset($_POST['gratis'])) {

                        $velicina_gratis = Validacija::String('Gratis veličina', $_POST['gratis'], 1, 10);
                        $this->model(Kosarica_Model::class)->dodaj($velicina, (int)$_POST['vrijednost'] ?? 0, $velicina_gratis, (int)$_POST['vrijednost'] ?? 0);

                    } else {

                        $this->model(Kosarica_Model::class)->dodaj($velicina, (int)$_POST['vrijednost'] ?? 0);

                    }

                    header("Location: ".$_SERVER['REQUEST_URI']);

                } else {

                    if (count($artikl_zaliha) === 1 && $artikl_zaliha[0]['Velicina'] === 'uni') {

                        $this->model(Kosarica_Model::class)->dodaj($artikl_zaliha[0]['artiklikarakteristikeSifra'], (int)$_POST['vrijednost'] ?? 0);

                        header("Location: ".$_SERVER['REQUEST_URI']);

                    } else {

                        throw new Kontroler_Greska('Molimo odaberite veličinu artikla!');

                    }

                }

            } catch (\Throwable $greska) {

                $kosarica_greska = $greska->getMessage();

            }

        }

        // favoriti
        if (isset($_POST['favorit'])) {

            if (isset($_POST['ID'])) {

                $id =  Validacija::Broj('ID', $_POST['ID'], 1, 10);

                $this->model(Favorit_Model::class)->dodaj($id);

            }

        }

        // calc veličine
        $calc_velicina = $kategorije->kategorija('', $trenutni_artikl['Kategorija'])['CalcVelicina'] === '1'
            ? '<img src="/kapriol/resursi/grafika/kapriol_size_guide2.png" />'
            : '';

        return sadrzaj()->datoteka('artikl.html')->podatci([
            'predlozak_opis' => Domena::opis(),
            'predlozak_GA' => Domena::GA(),
            'linkerRetargeting' => Domena::linkerRetargeting(),
            'predlozak_naslov' => $trenutni_artikl['Naziv'],
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
            'vi_ste_ovdje' => 'Vi ste ovdje : <a href="/">Kapriol Web Trgovina</a> \\\\ <a href="/rezultat/'.$trenutni_artikl['KategorijaLink'].'">' . $trenutni_artikl['Kategorija'] . '</a> \\\\ <a href="/rezultat/'.$trenutni_artikl['KategorijaLink'].'/'.$trenutni_artikl['PodkategorijaLink'].'">' . $trenutni_artikl['Podkategorija'] . '</a> \\\\ ' . $trenutni_artikl['Naziv'],
            'opci_uvjeti' => Domena::opciUvjeti(),
            'artikl_id' => $trenutni_artikl['ID'],
            'artikl_slika' => ''.$trenutni_artikl['Slika'].'',
            'artikl_slike' => $artikl_slike_html,
            'artikl_naziv' => $trenutni_artikl['Naziv'],
            'artikl_cijena' => $artikl_cijena,
            'artikl_zaliha' => $artikl_zaliha_html,
            'artikl_gratis' => $artikl_gratis_html,
            'artikl_gratis_zaliha' => $artikl_gratis_zaliha_html,
            'artikl_kosarica_velicine' => $artikl_kosarica_velicine,
            'artikl_opis' => $trenutni_artikl['Opis'],
            'calc_velicina' => $calc_velicina,
            "google_artikal" => $this->GoogleArtikal($trenutni_artikl, $artikl_model->slike($trenutni_artikl['ID'])),
            "ga4_artikal" => $this->GA4artikal($trenutni_artikl),
            'kosarica_greska' => $kosarica_greska
        ]);

    }

    /**
     * GA4 artikal
     */
    private function GA4artikal ($trenutni_artikl):string {

        // cijene
        $cijena = $trenutni_artikl['CijenaAkcija'] > 0 ? $trenutni_artikl['CijenaAkcija']: $trenutni_artikl['Cijena'];

        $artikal_popust = $trenutni_artikl['CijenaAkcija'] > 0 ? $trenutni_artikl['Cijena'] - $trenutni_artikl['CijenaAkcija'] : $trenutni_artikl['CijenaAkcija'];

        return '
        <!--GA4 podatci-->
        <script>
            gtag("event", "view_item", {
              currency: "'.Domena::valutaISO().'",
              value: '.$cijena.',
              items: [
                {
                  item_id: "'.$trenutni_artikl['ID'].'",
                  item_name: "'.$trenutni_artikl['Naziv'].'",
                  currency: "'.Domena::valutaISO().'",
                  discount: '.$artikal_popust.',
                  index: 0,
                  item_category: "'.$trenutni_artikl['Kategorija'].'",
                  item_category2: "'.$trenutni_artikl['Podkategorija'].'",
                  price: '.$trenutni_artikl['Cijena'].',
                  quantity: 1
                }
              ]
            });
        </script>
        ';

    }

    /**
     * Google artikal
     */
    private function GoogleArtikal ($trenutni_artikl, $slike):string {

        // slike za google artikal
        $google_artikal_slike = array();
        foreach ($slike as $artikal_slika) {

            $google_artikal_slike[] = Server::URL() . '/slika/velikaslika/'.$artikal_slika['Slika'];

        }
        $google_artikal_slike_vrijednosti = array_values($google_artikal_slike);
        $slike = json_encode($google_artikal_slike_vrijednosti, JSON_UNESCAPED_SLASHES);

        return '
        <!--google obogaćeni podatci-->
        <script type="application/ld+json">
            {
                "@context": "https://schema.org/",
                "@type": "Product",
                "name": "'.$trenutni_artikl["Naziv"].'",
                "image": '.$slike.',
                "description": "'.$trenutni_artikl["Opis"].'",
                "sku": "'.$trenutni_artikl["ID"].'",
                "brand": {
                    "@type": "Brand",
                    "name": "Kapriol"
                },
                "offers": {
                    "@type": "Offer",
                    "url": "'. Server::URL() . '/artikl/' . $trenutni_artikl["Link"] .'",
                    "priceCurrency": "'.Domena::Valuta().'",
                    "price": "'.$trenutni_artikl["Cijena"].'",
                    "priceValidUntil": "'.date("Y-m-d").'",
                    "itemCondition": "http://schema.org/NewCondition",
                    "availability": "http://schema.org/InStock"
                }

            }
        </script>
        ';

    }

}