<?php declare(strict_types = 1);

/**
 * Artikli
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
use FireHub\Aplikacija\Administrator\Model\Kategorije_Model;
use FireHub\Jezgra\Sadrzaj\Sadrzaj;
use FireHub\Aplikacija\Administrator\Model\Artikli_Model;
use FireHub\Jezgra\HTTP\Enumeratori\Vrsta;
use FireHub\Jezgra\Sadrzaj\Enumeratori\Vrsta as Sadrzaj_Vrsta;
use FireHub\Jezgra\HTTP\Atributi\Zaglavlja;
use FireHub\Jezgra\Greske\Greska;

/**
 * ### Artikli
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Kontroler
 */
final class Artikli_Kontroler extends Master_Kontroler {

    /**
     * ## index
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function index (string $kontroler = '', string|int $kategorija = '', string|int $podkategorija = ''):Sadrzaj {

        return sadrzaj()->datoteka('artikli/lista.html')->podatci([
            'kategorija' => ''.$kategorija.'',
            'podkategorija' => ''.$podkategorija.''
        ]);

    }

    /**
     * ## Lista artikala
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    #[Zaglavlja(vrsta: Vrsta::JSON)]
    public function lista (string $kontroler = '', string $metoda = '', int $broj_stranice = 1, string $poredaj = 'Naziv', string $redoslijed = 'asc', string|int $kategorija = '', string|int $podkategorija = ''):Sadrzaj {

        try {

            // model
            $artikli = $this->model(Artikli_Model::class);

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'da',
                'Artikli' => $artikli->lista($broj_stranice, $poredaj, $redoslijed, $kategorija, $podkategorija),
                'Zaglavlje' => $artikli->IspisiZaglavlje(),
                'Navigacija' => $artikli->IspisiNavigaciju()
            ]);

        } catch (Greska $greska) {

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'ne',
                'Poruka' => $greska->getMessage()
            ]);

        }

    }

    /**
     * ## Uredi artikl
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function uredi (string $kontroler = '', string $metoda = '', int $id = 0) {

        $artikl_model = $this->model(Artikl_Model::class);
        $artikl = $artikl_model->artikl($id);

        // formatiranje rezultata
        if ($artikl['Izdvojeno'] === true) {$artikl['Izdvojeno'] = 'checked';} else {$artikl['Izdvojeno'] = '';}
        if ($artikl['Aktivan'] === true) {$artikl['Aktivan'] = 'checked';} else {$artikl['Aktivan'] = '';}
        if ($artikl['Ba'] === true) {$artikl['Ba'] = 'checked';} else {$artikl['Ba'] = '';}
        if ($artikl['Hr'] === true) {$artikl['Hr'] = 'checked';} else {$artikl['Hr'] = '';}
        if ($artikl['Outlet'] === true) {$artikl['Outlet'] = 'checked';} else {$artikl['Outlet'] = '';}
        if ($artikl['OutletHr'] === true) {$artikl['OutletHr'] = 'checked';} else {$artikl['OutletHr'] = '';}
        if ($artikl['Novo'] === true) {$artikl['Novo'] = 'checked';} else {$artikl['Novo'] = '';}

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

        // slike
        $slike = $artikl_model->slike($id);
        $slike_html = '';
        $slikeOpcija = '';
        foreach ($slike as $slika) {

            $slike_html .= '
                <tr>
                    <td>
                        <img src="/slika/malaslika/'.$slika['Slika'].'" alt="'.$slika['Slika'].'" />
                    </td>
                    <td>
                        '.$slika['Slika'].'
                    </td>
                    <td>
                        <a class="gumb" data-boja="boja" onclick="$_ArtiklIzbrisiSliku(\''.$slika['ID'].'\')">Izbriši sliku</a>
                    </td>
                </tr>
            ';

            $slikeOpcija .= "<option value='{$slika['ID']}'>{$slika['Slika']}</option>";

        }

        // cijene
        $cijene = $artikl_model->cijene($id);
        $cijene_html = '';
        foreach ($cijene as $cijena) {

            $cijene_html .= '
                <tr>
                    <td>'.$cijena['Cijena'].'</td>
                    <td>'.$cijena['Vrsta'].'</td>
                    <td>'.$cijena['Datum'].'</td>
                    <td>
                        <a class="gumb" data-boja="boja" onclick="$_ArtiklIzbrisiCijenu(\''.$cijena['ID'].'\')">Izbriši</a>
                    </td>
                </tr>
            ';

        }

        // gratis
        $gratis_model = $this->model(Artikli_Model::class);
        $gratis_artikli = $gratis_model->listaGratis();
        $gratis_artikli_html = '';
        foreach ($gratis_artikli as $artikl_gratis) {

            $gratis_artikli_html .= "<option value='{$artikl_gratis['ID']}'>{$artikl_gratis['Naziv']}</option>";

        }

        return sadrzaj()->format(Sadrzaj_Vrsta::HTMLP)->datoteka('artikli/uredi.html')->podatci([
            'id' => $artikl['ID'],
            'naziv' => $artikl['Naziv'],
            'opis' => $artikl['Opis'],
            'cijena' => $artikl['Cijena'],
            'cijena_akcija' => $artikl['CijenaAkcija'],
            'cijena_hr' => $artikl['CijenaKn'],
            'cijena_akcija_hr' => $artikl['CijenaAkcijaKn'],
            'aktivno' => $artikl['Aktivan'],
            'izdvojeno' => $artikl['Izdvojeno'],
            'ba' => $artikl['Ba'],
            'hr' => $artikl['Hr'],
            'outlet' => $artikl['Outlet'],
            'outlethr' => $artikl['OutletHr'],
            'novo' => $artikl['Novo'],
            'kategorija' => ''.$artikl['KategorijaID'].'',
            'kategorija_naziv' => $artikl['Kategorija'],
            'podkategorija' => ''.$artikl['PodKategorijaID'].'' ?? '0',
            'podkategorija_naziv' => $artikl['PodKategorija'] ? ' ->> '.$artikl['PodKategorija'] : '',
            'kategorije' => $kategorije_html,
            'slike' => $slike_html,
            'slikeOpcija' => $slikeOpcija,
            'cijene' => $cijene_html,
            'zadanaSlika' => ''.$artikl['Slika'].'' ?? 0,
            'zadanaSlika_naziv' => $artikl['SlikaNaziv'] ?? '== bez gratis artikla ==',
            'gratisBa' => ''.$artikl['GratisBa'].'' ?? 0,
            'gratisBa_naziv' => $artikl['GratisBaNaziv'] ?? '== bez gratis artikla ==',
            'gratisHr' => ''.$artikl['GratisHr'].'' ?? 0,
            'gratisHr_naziv' => $artikl['GratisHrNaziv'] ?? '== bez gratis artikla ==',
            'gratis_artikli' => $gratis_artikli_html
        ]);

    }

    /**
     * ## Novi artikl
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function novi (string $kontroler = '', string $metoda = '', int $id = 0) {

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

        return sadrzaj()->format(Sadrzaj_Vrsta::HTMLP)->datoteka('artikli/novi.html')->podatci([
            'id' => '0',
            'kategorije' => $kategorije_html
        ]);

    }

    /**
     * ### Spremi artikl
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj
     */
    #[Zaglavlja(vrsta: Vrsta::JSON)]
    public function spremi (string $kontroler = '', string $metoda = '', int $id = 0):Sadrzaj {

        try {

            // model
            $artikl = $this->model(Artikl_Model::class);
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
     * ### Spremi sliku artikla
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj
     */
    #[Zaglavlja(vrsta: Vrsta::JSON)]
    public function dodajSliku (string $kontroler = '', string $metoda = '', int $id = 0):Sadrzaj {

        try {

            // model
            $artikl = $this->model(Artikl_Model::class);
            $artikl->dodajSliku($id);

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

    /**
     * ### Izbrisi sliku artikla
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj
     */
    #[Zaglavlja(vrsta: Vrsta::JSON)]
    public function izbrisiSliku (string $kontroler = '', string $metoda = '', int $id = 0):Sadrzaj {

        try {

            // model
            $artikl = $this->model(Artikl_Model::class);
            $artikl->izbrisiSliku($id);

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'da',
                'Poruka' => _('Uspješno izbrisano')
            ]);

        } catch (Greska $greska) {

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'ne',
                'Poruka' => $greska->getMessage()
            ]);

        }

    }

    /**
     * ### Izbrisi cijenu artikla
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj
     */
    #[Zaglavlja(vrsta: Vrsta::JSON)]
    public function izbrisiCijenu (string $kontroler = '', string $metoda = '', int $id = 0):Sadrzaj {

        try {

            // model
            $artikl = $this->model(Artikl_Model::class);
            $artikl->izbrisiCijenu($id);

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'da',
                'Poruka' => _('Uspješno izbrisano')
            ]);

        } catch (Greska $greska) {

            return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
                'Validacija' => 'ne',
                'Poruka' => $greska->getMessage()
            ]);

        }

    }

    /**
     * ## Uredi zalihu artikla
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function uredizalihu (string $kontroler = '', string $metoda = '', int $id = 0):Sadrzaj {

        $artikl_model = $this->model(Artikl_Model::class);
        $artiklKarakteristike = $artikl_model->artiklKarakteristike($id);

        $artiklKarakteristikehtml = '';
        foreach ($artiklKarakteristike as $karakteristika) {

            $skladista = $artikl_model->skladista();

            $skladistehtml = '';
            foreach ($skladista as $skladiste) {

                $artiklZaliha = $artikl_model->artiklzaliha($karakteristika['artiklikarakteristikeSifra'], $skladiste['ID']);

                $zaliha = $artiklZaliha['StanjeSkladiste'] ?? 0;

                $skladistehtml .= '
                    <label data-boja="boja" class="unos">
                        <input type="number" name="zaliha['.$karakteristika['artiklikarakteristikeSifra'].']['.$skladiste['ID'].']" value="'.$zaliha.'" maxlength="10" autocomplete="off" placeholder="0">
                        <span class="naslov">
                            <span>'.$skladiste['NazivSkladista'].'</span>
                        </span>
                        <span class="granica"></span>
                        <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#cijena"></use></svg>
                        <span class="upozorenje"></span>
                    </label>
                ';

            }

            $artiklKarakteristikehtml .= '
            <fieldset class="detalji">
                <legend>'.$karakteristika['artiklikarakteristikeSifra'].'</legend>
                <table class="podatci">
                    <tbody>
                    <tr>
                        <td>
                            '.$skladistehtml.'
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
            ';

        }

        return sadrzaj()->format(Sadrzaj_Vrsta::HTMLP)->datoteka('artikli/uredizalihu.html')->podatci([
            'id' => ''.$id.'',
            'artiklKarakteristikehtml' => $artiklKarakteristikehtml
        ]);

    }

    /**
     * ### Spremi artikl
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj
     */
    #[Zaglavlja(vrsta: Vrsta::JSON)]
    public function zalihaspremi (string $kontroler = '', string $metoda = '', int $id = 0):Sadrzaj {

        try {

            // model
            $artikl = $this->model(Artikl_Model::class);
            $artikl->zalihaspremi($id);

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
     * ## Uredi šifre artikla
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    public function artiklsifre (string $kontroler = '', string $metoda = '', int $id = 0):Sadrzaj {

        $artikl_model = $this->model(Artikl_Model::class);
        $artiklKarakteristike = $artikl_model->artiklKarakteristike($id);

        $artiklKarakteristikehtml = '';
        foreach ($artiklKarakteristike as $karakteristika) {

            $artiklKarakteristikehtml .= '
                <tr>
                    <td>
                        <label data-boja="boja" class="unos">
                            <input type="text" name="zaliha['.$karakteristika['ID'].']" value="'.$karakteristika['artiklikarakteristikeSifra'].'" maxlength="50" autocomplete="off">
                            <span class="naslov">
                                <span>Šifra</span>
                            </span>
                            <span class="granica"></span>
                            <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#cijena"></use></svg>
                            <span class="upozorenje"></span>
                        </label>
                    </td>
                    <td>
                        <label data-boja="boja" class="unos">
                            <input type="text" name="velicina['.$karakteristika['ID'].']" value="'.$karakteristika['Velicina'].'" maxlength="50" autocomplete="off">
                            <span class="naslov">
                                <span>Veličina</span>
                            </span>
                            <span class="granica"></span>
                            <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#cijena"></use></svg>
                            <span class="upozorenje"></span>
                        </label>
                    </td>
                    <td>
                        <button type="button" class="ikona" onclick="$_ArtiklSifreIzbrisi(this, '.$karakteristika['ID'].', '.$id.')"><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#izbrisi"></use></svg><span></span></button>
                    </td>
                </tr>
            ';

        }

        return sadrzaj()->format(Sadrzaj_Vrsta::HTMLP)->datoteka('artikli/uredisifre.html')->podatci([
            'id' => ''.$id.'',
            'artiklKarakteristikehtml' => $artiklKarakteristikehtml
        ]);

    }

    /**
     * ### Spremi šifre artikla
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj
     */
    #[Zaglavlja(vrsta: Vrsta::JSON)]
    public function artiklsifrespremi (string $kontroler = '', string $metoda = '', int $id = 0):Sadrzaj {

        try {

            // model
            $artikl = $this->model(Artikl_Model::class);
            $artikl->artiklsifrespremi($id);

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
     * ### Spremi šifre artikla
     * @since 0.1.2.pre-alpha.M1
     *
     * @return Sadrzaj
     */
    #[Zaglavlja(vrsta: Vrsta::JSON)]
    public function artiklsifreizbrisi (string $kontroler = '', string $metoda = '', int $id = 0):Sadrzaj {

        try {

            // model
            $artikl = $this->model(Artikl_Model::class);
            $artikl->artiklsifreizbrisi($id);

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

}