<?php declare(strict_types = 1);

/**
 * Artikl model
 * @since 0.1.2.pre-alpha.M1
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
 * ### Artikl model
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Model
 */
final class Artikl_Model extends Master_Model {

    /**
     * ### Konstruktor
     * @since 0.1.2.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Sesije.
     */
    public function __construct (
        private BazaPodataka $bazaPodataka
    ){

        parent::__construct();

    }

    /**
     * ### Dohvati artikl
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $link <p>
     * Link artikla.
     * </p>
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return array Artikl.
     */
    public function artikl (string $link) {

        $artikl = $this->bazaPodataka->tabela('artikliview')
            ->sirovi("
                SELECT
                    artikliview.ID, artikliview.Naziv, artikliview.Opis, artikliview.".Domena::sqlCijena()." AS Cijena, artikliview.".Domena::sqlCijenaAkcija()." AS CijenaAkcija, artikliview.Novo,
                    kategorijeview.Kategorija, kategorijeview.Link as KategorijaLink, IFNULL(podkategorijeview.Podkategorija, 'Sve podkategorije') as Podkategorija, IFNULL(podkategorijeview.Link, 'sve') as PodkategorijaLink, slikeartikal.Slika, artikliview.Link,
                    ".(Domena::Hr() ? 'artikliview.GratisHr' : 'artikliview.GratisBa')." AS GratisID, gratis.Naziv AS GratisNaziv, gratisslika.Slika AS GratisSlika, gratis.Link AS GratisLink,
                    artikliview.Cijena30Dana".Domena::sqlTablica()." AS Cijena30Dana
                FROM artikliview
                LEFT JOIN kategorijeview ON kategorijeview.ID = artikliview.KategorijaID
                LEFT JOIN podkategorijeview ON podkategorijeview.ID = artikliview.PodKategorijaID
                LEFT JOIN slikeartikal ON slikeartikal.ClanakID = artikliview.ID
                LEFT JOIN artikliview gratis ON gratis.ID = ".(Domena::Hr() ? 'artikliview.GratisHr' : 'artikliview.GratisBa')." AND gratis.Aktivan = 1 AND ".(Domena::Hr() ? 'gratis.Hr' : 'gratis.Ba')." = 1
                LEFT JOIN slikeartikal gratisslika ON gratisslika.ClanakID = gratis.ID
                WHERE artikliview.Link = '$link' AND artikliview.Aktivan = 1 AND artikliview.".Domena::sqlTablica()." = 1
                ORDER BY slikeartikal.Zadana DESC
                LIMIT 1
            ")
            ->napravi();

        if (!$redak = $artikl->redak()) {

            $redak = [
                'ID' => 0,
                'Naziv' => 'Artikl ne postoji',
                'Kategorija' => 'Sve'
            ];

        }

        return $redak;

    }

    /**
     * ### Dohvati slike artikla
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string|int $artiklID <p>
     * ID artikla.
     * </p>
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return array Artikl.
     */
    public function slike (string|int $artiklID):array {

        $slike = $this->bazaPodataka->tabela('slikeartikal')
            ->odaberi(['Slika'])
            ->gdje('ClanakID', '=', $artiklID)
            ->poredaj('Zadana', 'DESC')->napravi();

        return $slike->niz() ?: [];

    }

    /**
     * ### Dohvati karakteristike artikla
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string|int $artiklID <p>
     * ID artikla.
     * </p>
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     *
     * @return array Artikl.
     */
    public function zaliha (string|int $artiklID):array {

        if (Domena::Hr()) {

            $karakteristike = $this->bazaPodataka->tabela('artiklikarakteristike')
                ->sirovi("
                SELECT
                    SUM(StanjeSkladiste) AS StanjeSkladiste, IF(SUM(StanjeSkladiste) > 0, TRUE, FALSE) AS StanjeSkladisteTF,
                    artiklikarakteristike.Sifra AS artiklikarakteristikeSifra, Velicina
                FROM artiklikarakteristike
                LEFT JOIN stanjeskladista ON stanjeskladista.Sifra = artiklikarakteristike.Sifra
                WHERE ArtikalID = $artiklID
                AND (SkladisteID = 3)
                GROUP BY Velicina
                ORDER BY artiklikarakteristike.ID
            ")
                ->napravi();

        } else {

            $karakteristike = $this->bazaPodataka->tabela('artiklikarakteristike')
                ->sirovi("
                SELECT
                    SUM(StanjeSkladiste) AS StanjeSkladiste, IF(SUM(StanjeSkladiste) > 0, TRUE, FALSE) AS StanjeSkladisteTF,
                    artiklikarakteristike.Sifra AS artiklikarakteristikeSifra, Velicina
                FROM artiklikarakteristike
                LEFT JOIN stanjeskladista ON stanjeskladista.Sifra = artiklikarakteristike.Sifra
                WHERE ArtikalID = $artiklID
                AND (SkladisteID = 1 OR SkladisteID = 2)
                GROUP BY Velicina
                ORDER BY artiklikarakteristike.ID
            ")
                ->napravi();

        }

        return $karakteristike->niz() ?: [];

    }

}