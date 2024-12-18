<?php declare(strict_types = 1);

/**
 * Kosarica
 * @since 0.1.2.pre-alpha.M1
 *
 * @author Danijel Galić
 * @copyright 2022 Kapriol Web Trgovina
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version $Id
 * @package Aplikacija\Kontroler
 */

namespace FireHub\Aplikacija\Kapriol\Kontroler;

use FireHub\Aplikacija\Kapriol\Model\Gdpr_Model;
use FireHub\Jezgra\Sadrzaj\Sadrzaj;
use FireHub\Aplikacija\Kapriol\Model\Kategorije_Model;
use FireHub\Aplikacija\Kapriol\Model\Kosarica_Model;
use FireHub\Aplikacija\Kapriol\Jezgra\Server;
use FireHub\Aplikacija\Kapriol\Jezgra\Domena;
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

        $gdpr = $this->model(Gdpr_Model::class);

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

                if ($artikal['CijenaAkcija'] > 0) {

                    $artikl_cijena = '
                        <span class="akcija">'.number_format((float)$artikal['CijenaAkcija'], 2, ',', '.').'</span>
                    ';

                } else if (Domena::blackFriday()) {

                    $artikl_cijena = '
                        <span>'.number_format((float)$artikal['Cijena'] - ((float)$artikal['Cijena'] * Domena::blackFridayPopust()), 2, ',', '.').' '.Domena::valuta().'</span>
                    ';

                } else {

                    $artikl_cijena = '
                        <span>'.number_format((float)$artikal['Cijena'], 2, ',', '.').' '.Domena::valuta().'</span>
                    ';

                }

                // ako nije dostava
                if ($artikal['ID'] !== '0') {

                    // gratis
                    $gumbovi = '
                        <span>Ima gratis artikl, ukoliko želite izmjene, izbrišite ga i ponovo odaberite artikl sa gratisom!</span>
                        <label data-boja="boja" class="unos">
                            <input type="hidden" name="velicina" value="'.$artikal['Sifra'].'">
                            <input type="number" name="vrijednost" value="'.$artikal['Kolicina'].'" data-pakiranje="1" data-maxpakiranje="1000" value="0" min="0" max="100" step="1" autocomplete="off" pattern="0-9" readonly>
                            <span class="naslov">
                                <span></span>
                            </span>
                            <span class="granica"></span>
                        </label>
                        ';
                    if ($artikal['GratisID'] === null || $artikal['GratisID'] === '0') {
                        $gumbovi = '
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
                        ';
                    }

                    // artikli
                    $artikli_html .= '
                    <form class="artikl" method="post" enctype="multipart/form-data" action="">
                        <img src="/slika/malaslika/'.$artikal['Slika'].'" alt="" loading="lazy"/>
                        <a class="naslov" href="/artikl/'.$artikal['Link'].'">'.$artikal['Naziv'].'</a>
                        <span class="velicina">Veličina: '.$artikal['Velicina'].'</span>
                        <span class="cijena">'.$artikl_cijena.'</span>
                        <h3 class="ukupno">Ukupno: '.number_format((float)$artikal['CijenaUkupno'], 2, ',', '.').' '.Domena::valuta().'</h3>
                        <div class="kosarica">
                            '.$gumbovi.'
                            <button type="submit" class="gumb ikona" name="kosarica_izbrisi">
                                <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#izbrisi"></use></svg>
                                <span>Izbriši</span>
                            </button>
                        </div>
                    </form>
                ';

                } else {

                    // artikli
                    $artikli_html .= '
                    <form class="artikl" method="post" enctype="multipart/form-data" action="">
                        <img src="/slika/malaslika/'.$artikal['Slika'].'" alt="" loading="lazy"/>
                        <a class="naslov" href="/artikl/'.$artikal['Link'].'">'.$artikal['Naziv'].'</a>
                        <h3 class="ukupno">Ukupno: '.number_format((float)$artikal['CijenaUkupno'], 2, ',', '.').' '.Domena::valuta().'</h3>
                    </form>
                ';


                }

                // ukupno
                if ($artikal['Naziv'] !== 'Dostava') {

                    // ukupno količina
                    $total_kolicina += $artikal['Kolicina'];

                }
                $total_cijena += $artikal['CijenaUkupno'];

                $kosarica_artikli_ukupno = '
                    <ul>
                        <li>Ukupna količina: '.$total_kolicina.'</li>
                        <li class="ukupno">Ukupna cijena: <span>'.number_format((float)$total_cijena, 2, ',', '.').' '.Domena::valuta().'</span></li>
                    </ul>
                    <a data-boja="boja" class="gumb ikona" href="/kosarica/narudzbab2b">
                        <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno_duplo2"></use></svg>
                        <span>Nastavi</span>
                    </a>
                ';

            }

        } else {

            $artikli_html = '<h2>Vaša košarica je prazna!</h2>';

        }

        // artikli gratis
        $kosarica_artikli_gratis = $kosarica_model->artikliGratis();
        $artikli_gratis_html = '';
        if (!empty($kosarica_artikli_gratis)) {

            foreach ($kosarica_artikli_gratis as $artikal) {

                $artikli_gratis_html .= '
                    <form class="artikl" method="post" enctype="multipart/form-data" action="">
                        <img src="/slika/malaslika/'.$artikal['Slika'].'" alt="" loading="lazy"/>
                        <a class="naslov" href="/artikl/'.$artikal['Link'].'">'.$artikal['Naziv'].'</a>
                        <span class="velicina">Veličina: '.$artikal['Velicina'].'</span>
                        <span class="cijena">GRATIS</span>
                        <div class="kosarica">
                            <label data-boja="boja" class="unos">
                                <input type="hidden" name="velicina" value="'.$artikal['Sifra'].'">
                                <input type="number" name="vrijednost" value="'.$artikal['Kolicina'].'" data-pakiranje="1" data-maxpakiranje="1000" value="0" min="0" max="100" step="1" autocomplete="off" pattern="0-9" readonly>
                                <span class="naslov">
                                    <span></span>
                                </span>
                                <span class="granica"></span>
                            </label>
                        </div>
                    </form>
                ';

            }

        }

        return sadrzaj()->datoteka('kosarica.html')->podatci([
            'predlozak_opis' => Domena::opis(),
            'predlozak_GA' => Domena::GA(),
            'predlozak_naslov' => 'Košarica',
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
            'vi_ste_ovdje' => '<a href="/">Kapriol Web Trgovina</a> \\\\ Košarica',
            'opci_uvjeti' => Domena::opciUvjeti(),
            'kosarica_artikli' => $artikli_html,
            'kosarica_artikli_gratis' => $artikli_gratis_html,
            'kosarica_artikli_ukupno' => $kosarica_artikli_ukupno,
            "ga4_kosarica" => $this->GA4Kosarica()
        ]);

    }

    /**
     * GA4 kosarica
     */
    private function GA4Kosarica ():string {

        $kosarica_model = $this->model(Kosarica_Model::class);

        $artikli = $this->model(Kosarica_Model::class)->artikli();

        $items = '';
        $total_cijena = 0;
        $index = 0;
        foreach ($artikli as $item) {

            $artikal_popust = $item['CijenaAkcija'] > 0 ? $item['Cijena'] - $item['CijenaAkcija'] : $item['CijenaAkcija'];

            $total_cijena += $item['CijenaUkupno'];

            $items .= '
                {
                  item_id: "'.$item['ID'].'",
                  item_name: "'.$item['Naziv'].'",
                  currency: "'.Domena::valutaISO().'",
                  discount: '.$artikal_popust.',
                  index: '.$index++.',
                  price: '.$item['Cijena'].',
                  quantity: '.$item['Kolicina'].'
                },
            ';
        }

        return '
            <script>
                gtag("event", "view_cart", {
                    value: '.$total_cijena.',
                    currency: "'.Domena::valutaISO().'",
                    items: [
                        '.$items.'
                        ]
                });
            </script>
        ';

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

        $gdpr = $this->model(Gdpr_Model::class);

        if ($this->kosaricaArtikli() === '0') {

            header("Location: ".Server::URL());

        }

        $kategorije = $this->model(Kategorije_Model::class);

        $kosarica_model = $this->model(Kosarica_Model::class);

        $narudzba_greska = '';
        if (isset($_POST['naruci'])) {

            try {

                $this->model(Kosarica_Model::class)->naruci();

                header("Location: ".Server::URL()."/kosarica/ispravno");

            } catch (\Throwable $greska) {

                $narudzba_greska = $greska->getMessage();

            }

        }

        return sadrzaj()->datoteka('narudzba.html')->podatci([
            'predlozak_opis' => Domena::opis(),
            'predlozak_GA' => Domena::GA(),
            'linkerRetargeting' => Domena::linkerRetargeting(),
            'predlozak_naslov' => 'Narudžba',
            'facebook_link' => Domena::facebook(),
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
            'vi_ste_ovdje' => '<a href="/">Kapriol Web Trgovina</a> \\\\ Narudžba',
            'opci_uvjeti' => Domena::opciUvjeti(),
            'narudzba_greska' => $narudzba_greska,
            'forma_ime' => $_POST['ime'] ?? '',
            'forma_email' => $_POST['email'] ?? '',
            'forma_telefon' => $_POST['telefon'] ?? '',
            'forma_adresa' => $_POST['adresa'] ?? '',
            'forma_zip' => $_POST['zip'] ?? '',
            'forma_napomena' => $_POST['napomena'] ?? ''
        ]);

    }

    /**
     * ### Narudzba B2B
     * @since 0.1.2.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Log-a.
     * @throws Kontroler_Greska Ukoliko objekt nije validan model.
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function narudzbab2b ():Sadrzaj {

        $gdpr = $this->model(Gdpr_Model::class);

        if ($this->kosaricaArtikli() === '0') {

            header("Location: ".Server::URL());

        }

        $kategorije = $this->model(Kategorije_Model::class);

        $kosarica_model = $this->model(Kosarica_Model::class);

        return sadrzaj()->datoteka('narudzba_b2b.html')->podatci([
            'predlozak_opis' => Domena::opis(),
            'predlozak_GA' => Domena::GA(),
            'predlozak_naslov' => 'Narudžba',
            'linkerRetargeting' => Domena::linkerRetargeting(),
            'facebook_link' => Domena::facebook(),
            'instagram_link' => Domena::instagram(),
            'glavni_meni' => $kategorije->glavniMeni(),
            'glavni_meni_hamburger' => $kategorije->glavniMeniHamburger(),
            'zaglavlje_kosarica_artikli' => $this->kosaricaArtikli(),
            'zaglavlje_kosarica_artikli_html' => $this->kosaricaArtikliHTML(),
            'zaglavlje_favorit_artikli' => $this->favoritArtikli(),
            'zaglavlje_tel' => Domena::telefon(),
            'zaglavlje_adresa' => Domena::adresa(),
            'podnozje_dostava' => Domena::podnozjeDostava(),
            'gdpr' => $gdpr->html(),
            'vi_ste_ovdje' => '<a href="/">Kapriol Web Trgovina</a> \\\\ Narudžba',
            'opci_uvjeti' => Domena::opciUvjeti(),
            'domena_oibpdv' => Domena::OIBPDV(),
            'domena_valuta' => Domena::valuta(),
            'narudzba_greska' => '',
            'forma_ime' => $_POST['ime'] ?? '',
            'forma_email' => $_POST['email'] ?? '',
            'forma_telefon' => $_POST['telefon'] ?? '',
            'forma_grad' => $_POST['grad'] ?? '',
            'forma_adresa' => $_POST['adresa'] ?? '',
            'forma_zip' => $_POST['zip'] ?? '',
            'forma_tvrtka' => $_POST['tvrtka'] ?? '',
            'forma_oib' => $_POST['oib'] ?? '',
            'forma_tvrtka_adresa' => $_POST['tvrtkaadresa'] ?? '',
            'forma_placanje' => $_POST['placanje'] ?? '',
            'forma_napomena' => $_POST['napomena'] ?? '',
            "ga4_pocni_narudzbu" => $this->GA4PocniNarudzbu()
        ]);

    }

    /**
     * GA4 počni narudžbu
     */
    private function GA4PocniNarudzbu ():string {

        $kosarica_model = $this->model(Kosarica_Model::class);

        $artikli = $this->model(Kosarica_Model::class)->artikli();

        $items = '';
        $total_cijena = 0;
        $index = 0;
        foreach ($artikli as $item) {

            $artikal_popust = $item['CijenaAkcija'] > 0 ? $item['Cijena'] - $item['CijenaAkcija'] : $item['CijenaAkcija'];

            $total_cijena += $item['CijenaUkupno'];

            $items .= '
                {
                  item_id: "'.$item['ID'].'",
                  item_name: "'.$item['Naziv'].'",
                  currency: "'.Domena::valutaISO().'",
                  discount: '.$artikal_popust.',
                  index: '.$index++.',
                  price: '.$item['Cijena'].',
                  quantity: '.$item['Kolicina'].'
                },
            ';
        }

        return '
            <script>
                gtag("event", "begin_checkout", {
                    value: '.$total_cijena.',
                    currency: "'.Domena::valutaISO().'",
                    items: [
                        '.$items.'
                        ]
                });
            </script>
        ';

    }

    /**
     * ### Naruci
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function naruci ():Sadrzaj {

        $gdpr = $this->model(Gdpr_Model::class);

        $kategorije = $this->model(Kategorije_Model::class);

        $this->model(Kosarica_Model::class)->narucib2b();

        return sadrzaj()->datoteka('narudzba_b2b.html')->podatci([
            'predlozak_opis' => Domena::opis(),
            'predlozak_GA' => Domena::GA(),
            'predlozak_naslov' => 'Narudžba',
            'linkerRetargeting' => Domena::linkerRetargeting(),
            'facebook_link' => Domena::facebook(),
            'instagram_link' => Domena::instagram(),
            'glavni_meni' => $kategorije->glavniMeni(),
            'glavni_meni_hamburger' => $kategorije->glavniMeniHamburger(),
            'zaglavlje_kosarica_artikli' => $this->kosaricaArtikli(),
            'zaglavlje_kosarica_artikli_html' => $this->kosaricaArtikliHTML(),
            'zaglavlje_favorit_artikli' => $this->favoritArtikli(),
            'zaglavlje_tel' => Domena::telefon(),
            'zaglavlje_adresa' => Domena::adresa(),
            'podnozje_dostava' => Domena::podnozjeDostava(),
            'gdpr' => $gdpr->html(),
            'vi_ste_ovdje' => '<a href="/">Kapriol Web Trgovina</a> \\\\ Narudžba',
            'opci_uvjeti' => Domena::opciUvjeti(),
            'domena_oibpdv' => Domena::OIBPDV(),
            'domena_valuta' => Domena::valuta(),
            'forma_ime' => $_POST['ime'] ?? '',
            'forma_email' => $_POST['email'] ?? '',
            'forma_telefon' => $_POST['telefon'] ?? '',
            'forma_grad' => $_POST['grad'] ?? '',
            'forma_adresa' => $_POST['adresa'] ?? '',
            'forma_zip' => $_POST['zip'] ?? '',
            'forma_tvrtka' => $_POST['tvrtka'] ?? '',
            'forma_oib' => $_POST['oib'] ?? '',
            'forma_tvrtka_adresa' => $_POST['tvrtkaadresa'] ?? '',
            'forma_placanje' => $_POST['placanje'] ?? '',
            'forma_napomena' => $_POST['napomena'] ?? ''
        ]);

    }

    /**
     * ### GA4 kupnja
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function ga4purchase ():Sadrzaj {

        $gdpr = $this->model(Gdpr_Model::class);

        $kosarica_model = $this->model(Kosarica_Model::class);

        $artikli = $this->model(Kosarica_Model::class)->artikli();

        $items = '';
        $total_cijena = 0;
        $index = 0;
        foreach ($artikli as $item) {

            $artikal_popust = $item['CijenaAkcija'] > 0 ? $item['Cijena'] - $item['CijenaAkcija'] : $item['CijenaAkcija'];

            $total_cijena += $item['CijenaUkupno'];

            $items .= '
                {
                  item_id: "'.$item['ID'].'",
                  item_name: "'.$item['Naziv'].'",
                  currency: "'.Domena::valutaISO().'",
                  discount: '.$artikal_popust.',
                  index: '.$index++.',
                  price: '.$item['Cijena'].',
                  quantity: '.$item['Kolicina'].'
                },
            ';
        }

        $gap = '
        <script>
            gtag("event", "purchase", {
                transaction_id: "'.time().'",
                value: '.$total_cijena.',
                currency: "'.Domena::valutaISO().'",
                items: [
                    '.$items.'
                    ]
            });
        </script>
        ';

        return sadrzaj()->datoteka('ga4purchase.html')->podatci([
            'predlozak_opis' => Domena::opis(),
            'predlozak_GA' => Domena::GA(),
            'predlozak_naslov' => 'Narudžba',
            'linkerRetargeting' => Domena::linkerRetargeting(),
            'facebook_link' => Domena::facebook(),
            'instagram_link' => Domena::instagram(),
            'zaglavlje_kosarica_artikli' => $this->kosaricaArtikli(),
            'zaglavlje_kosarica_artikli_html' => $this->kosaricaArtikliHTML(),
            'zaglavlje_favorit_artikli' => $this->favoritArtikli(),
            'zaglavlje_tel' => Domena::telefon(),
            'zaglavlje_adresa' => Domena::adresa(),
            'podnozje_dostava' => Domena::podnozjeDostava(),
            'gdpr' => $gdpr->html(),
            'vi_ste_ovdje' => '<a href="/">Kapriol Web Trgovina</a> \\\\ Narudžba',
            'opci_uvjeti' => Domena::opciUvjeti(),
            'domena_oibpdv' => Domena::OIBPDV(),
            'domena_valuta' => Domena::valuta(),
            'ga4purchase' => $gap
        ]);

    }

    /**
     * ### Odabir vrste narudžbe
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function odabir ():Sadrzaj {

        $gdpr = $this->model(Gdpr_Model::class);

        $kategorije = $this->model(Kategorije_Model::class);

        return sadrzaj()->datoteka('narudzba_vrsta.html')->podatci([
            'predlozak_opis' => Domena::opis(),
            'predlozak_GA' => Domena::GA(),
            'predlozak_naslov' => 'Vrsta narudžbe',
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
            'vi_ste_ovdje' => '<a href="/">Kapriol Web Trgovina</a> \\\\ Vrsta narudžbe',
            'opci_uvjeti' => Domena::opciUvjeti()
        ]);

    }

    /**
     * ### Ispravna narudžba
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function ispravno ():Sadrzaj {

        $gdpr = $this->model(Gdpr_Model::class);

        $kategorije = $this->model(Kategorije_Model::class);

        $kosarica = $this->model(Kosarica_Model::class);
        $kosarica->ispravno();

        return sadrzaj()->datoteka('narudzba_ispravno.html')->podatci([
            'predlozak_opis' => Domena::opis(),
            'predlozak_GA' => Domena::GA(),
            'predlozak_naslov' => 'Naslovna',
            'linkerKonverzija' => Domena::linkerKonverzija(),
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
            'vi_ste_ovdje' => 'Vi ste ovdje : <a href="/">Kapriol Web Trgovina</a>',
            'opci_uvjeti' => Domena::opciUvjeti()
        ]);

    }

}