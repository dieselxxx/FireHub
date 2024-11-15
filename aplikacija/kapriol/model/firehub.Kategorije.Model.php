<?php declare(strict_types = 1);

/**
 * Kategorije model
 * @since 0.1.1.pre-alpha.M1
 *
 * @author Danijel Galić
 * @copyright 2022 Kapriol Web Trgovina
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Aplikacija\Model
 */

namespace FireHub\Aplikacija\Kapriol\Model;

use FireHub\Jezgra\Komponente\BazaPodataka\BazaPodataka;
use FireHub\Aplikacija\Kapriol\Jezgra\Domena;
use FireHub\Jezgra\Kontejner\Greske\Kontejner_Greska;

/**
 * ### Kategorije model
 * @since 0.1.1.pre-alpha.M1
 *
 * @package Aplikacija\Model
 */
final class Kategorije_Model extends Master_Model {

    /**
     * ### Konstruktor
     * @since 0.1.1.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Sesije.
     */
    public function __construct (
        private BazaPodataka $bazaPodataka
    ) {

        parent::__construct();

    }

    /**
     * ### Sve kategorije za glavni meni sa uključenim menijom
     * @since 0.1.1.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return string HTML za glavni meni sa uključenim menijom.
     */
    public function glavniMeni ():string {

        $kategorije_meni = array_filter(
            $this->kategorije(),
            function ($kategorija) {
                if ($kategorija['Meni'] === '1') {
                    return $kategorija;
                }
                return [];
            }
        );

        $rezultat = '
            <li>
                <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#akcija_sale"></use></svg>
                <span><a href="/rezultat/outlet">Outlet</a></span>
            </li>
            <li>
                <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#akcija_novi2"></use></svg>
                <span><a href="/rezultat/akcija">Na akciji</a></span>
            </li>
            <li>
                <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#novo"></use></svg>
                <span><a href="/rezultat/novo">Novo</a></span>
            </li>
        ';
        foreach ($kategorije_meni as $kategorija) {

            $rezultat .= '
                <li>
                    <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno_duplo2"></use></svg>
                    <span><a href="/rezultat/'.$kategorija['Link'].'">'.$kategorija['Kategorija'].'</a></span>
                </li>
            ';

        }

        return $rezultat;

    }

    /**
     * ### Sve kategorije za glavni meni
     * @since 0.1.1.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return string HTML za glavni meni.
     */
    public function glavniMeniHamburger ():string {

        $rezultat = '
            <li>
                <label class="hamburger-menu_gumb" for="hamburger-menu-toggle">
                    <p>Zatvori</p>
                    <span></span>
                </label>
            </li>
        ';
        foreach ($this->kategorije() as $kategorija) {

            $ikona = !is_null($kategorija['Ikona']) ? $kategorija['Ikona'] : 'strelica_desno_duplo2';

            $href = $kategorija['Podkategorija'] > 0 ? "/kategorija/{$kategorija['Link']}" : "/rezultat/{$kategorija['Link']}";

            $rezultat .= '
                <li>
                    <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno_duplo2"></use></svg>
                    <span><a href='.$href.'>'.$kategorija['Kategorija'].'</a></span>
                </li>
            ';

        }

        return $rezultat;

    }

    /**
     * ### Sve kategorije za naslovnu
     * @since 0.1.2.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return string Ketegorije za naslovnu.
     */
    public function kategorijeNaslovna ():string {

        // kategorije
        $kategorija_html = '';
        foreach ($this->kategorije() as $kategorija) {

            $href = $kategorija['Podkategorija'] > 0 ? "/kategorija/{$kategorija['Link']}" : "/rezultat/{$kategorija['Link']}";

            $kategorija_html .= <<<Kategorija
            
                <a class="kategorija" href=$href>
                    <img
                        srcset="
                            slika/kategorija/{$kategorija['Slika']}/300/400,
                            /slika/kategorija/{$kategorija['Slika']}/200/250,
                            /slika/kategorija/{$kategorija['Slika']}/125/150"
                        src="/slika/kategorija/{$kategorija['Slika']}/300/400"
                        alt="" loading="lazy"
                    />
                    <span class="naslov">{$kategorija['Kategorija']}</span>
                </a>

            Kategorija;

        }

        return $kategorija_html;

    }

    /**
     * ### Dohvati kategoriju
     * @since 0.1.1.pre-alpha.M1
     *
     * @param string $kategorija <p>
     * Link kategorije.
     * </p>
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return array Kategorija.
     */
    public function kategorija (string $kategorija, string $naziv = ''):array {

        if ($kategorija === 'sve') {

            return [
                'ID' => 'sve',
                'Kategorija' => 'Sve kategorije',
                'Link' => 'sve'
            ];

        } else if ($kategorija === 'akcija') {

            return [
                'ID' => 'akcija',
                'Kategorija' => 'Akcija',
                'Link' => 'akcija'
            ];

        } else if ($kategorija === 'outlet') {

            return [
                'ID' => 'outlet',
                'Kategorija' => 'Outlet',
                'Link' => 'outlet'
            ];

        } else if ($kategorija === 'novo') {

            return [
                'ID' => 'novo',
                'Kategorija' => 'Novo',
                'Link' => 'novo'
            ];

        }

        if ($naziv !== '') {

            $id = $this->bazaPodataka->tabela('kategorijeview')
                ->odaberi(['ID', 'Kategorija', 'Link', 'CalcVelicina'])
                ->gdje('Kategorija', '=', $naziv)
                ->napravi();

        } else {

            $id = $this->bazaPodataka->tabela('kategorijeview')
                ->odaberi(['ID', 'Kategorija', 'Link', 'CalcVelicina'])
                ->gdje('Link', '=', $kategorija)
                ->napravi();

        }

        if (!$redak = $id->redak()) {

            $redak = [
                'ID' => 0,
                'Kategorija' => 'Kategorija ne postoji',
                'Link' => ''
            ];

        }

        return $redak;

    }

    /**
     * ### Sve kategorije
     * @since 0.1.1.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return array Niz kategorija.
     */
    public function kategorije ():array {

        $kategorije = $this->bazaPodataka->tabela('kategorijeview')
            ->sirovi("
                SELECT 
                    kategorijeview.Kategorija, COUNT(podkategorijeview.KategorijaID) AS Podkategorija, kategorijeview.Link, kategorijeview.Slika, kategorijeview.Ikona, kategorijeview.Meni
                FROM kategorijeview
                LEFT JOIN podkategorijeview ON podkategorijeview.KategorijaID = kategorijeview.ID
                LEFT JOIN artikli ON artikli.KategorijaID = kategorijeview.ID AND artikli.".Domena::sqlTablica()." = 1
                LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikli.ID
                LEFT JOIN stanjeskladista ON stanjeskladista.Sifra = artiklikarakteristike.Sifra
                GROUP BY kategorijeview.Kategorija, kategorijeview.Link, kategorijeview.Ikona, kategorijeview.Meni
                HAVING SUM(StanjeSkladiste) > 0
                ORDER BY kategorijeview.Prioritet ASC
            ")->napravi();

        return $kategorije->niz();

    }

    /**
     * ### Dohvati podkategoriju
     * @since 0.1.1.pre-alpha.M1
     *
     * @param string $podkategorija <p>
     * Link podkategorije.
     * </p>
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return array Podkategorija.
     */
    public function podkategorija (string $podkategorija, string $naziv = ''):array {

        if ($podkategorija === 'sve') {

            return [
                'ID' => 'sve',
                'Podkategorija' => 'Sve podkategorije',
                'Link' => 'sve'
            ];

        } else if ($podkategorija === 'akcija') {

            return [
                'ID' => 'akcija',
                'Podkategorija' => 'Akcija',
                'Link' => 'akcija'
            ];

        }

        if ($naziv !== '') {

            $id = $this->bazaPodataka->tabela('podkategorijeview')
                ->odaberi(['ID', 'Podkategorija', 'Link'])
                ->gdje('Podkategorija', '=', $naziv)
                ->napravi();

        } else {

            $id = $this->bazaPodataka->tabela('podkategorijeview')
                ->odaberi(['ID', 'Podkategorija', 'Link'])
                ->gdje('Link', '=', $podkategorija)
                ->napravi();

        }

        if (!$redak = $id->redak()) {

            $redak = [
                'ID' => 0,
                'Podkategorija' => 'Podkategorija ne postoji',
                'Link' => ''
            ];

        }

        return $redak;

    }

    /**
     * ### Sve podkategorije neke kategorije
     * @since 0.1.1.pre-alpha.M1
     *
     * @param int|string $kategorija <p>
     * ID kategorije.
     * </p>
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return array Niz kategorija.
     */
    public function podkategorije (int|string $kategorijaID):array {

        $podkategorije = $this->bazaPodataka->tabela('podkategorijeview')
            ->sirovi("
                SELECT 
                    podkategorijeview.ID, podkategorijeview.PodKategorija, podkategorijeview.Link, podkategorijeview.Slika
                FROM podkategorijeview
                LEFT JOIN kategorijeview ON kategorijeview.ID = podkategorijeview.KategorijaID
                LEFT JOIN artikli ON artikli.KategorijaID = kategorijeview.ID AND artikli.".Domena::sqlTablica()." = 1
                LEFT JOIN artiklikarakteristike ON artiklikarakteristike.ArtikalID = artikli.ID
                LEFT JOIN stanjeskladista ON stanjeskladista.Sifra = artiklikarakteristike.Sifra
                WHERE podkategorijeview.KategorijaID = '$kategorijaID'
                GROUP BY podkategorijeview.ID, podkategorijeview.PodKategorija, podkategorijeview.Link
                HAVING SUM(StanjeSkladiste) > 0
                ORDER BY podkategorijeview.PodKategorija ASC
            ")->napravi();

        return $podkategorije->niz() ?: [];

    }

    /**
     * ### Sve podkategorije za kategoriju
     * @since 0.1.2.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return string Ketegorije za naslovnu.
     */
    public function podkategorijeKategorija (int|string $id, string $link):string {

        // podkategorije
        $podkategorija_html = '';
        foreach ($this->podkategorije($id) as $podkategorija) {

            $podkategorija_html .= <<<PodKategorija
            
                <a class="podkategorija" href="/rezultat/{$link}/{$podkategorija['Link']}">
                    <img
                        src="/slika/podkategorija/{$podkategorija['Slika']}/300/400"
                        alt="" loading="lazy"
                    />
                    <span class="naslov">{$podkategorija['Podkategorija']}</span>
                </a>

            PodKategorija;

        }

        return $podkategorija_html;

    }

}