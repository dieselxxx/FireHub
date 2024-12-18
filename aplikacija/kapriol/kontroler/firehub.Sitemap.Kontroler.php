<?php declare(strict_types = 1);

/**
 * Sitemap
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
use FireHub\Aplikacija\Kapriol\Model\Artikli_Model;
use FireHub\Aplikacija\Kapriol\Model\Kategorije_Model;
use FireHub\Aplikacija\Kapriol\Model\Sitemap_Model;
use FireHub\Jezgra\HTTP\Atributi\Zaglavlja;
use FireHub\Jezgra\HTTP\Zahtjev;
use FireHub\Jezgra\Kontejner\Greske\Kontejner_Greska;
use FireHub\Jezgra\Kontroler\Greske\Kontroler_Greska;
use FireHub\Jezgra\HTTP\Enumeratori\Vrsta;
use FireHub\Jezgra\Sadrzaj\Enumeratori\Vrsta as Sadrzaj_Vrsta;
use FireHub\Jezgra\Sadrzaj\Sadrzaj;

/**
 * ### Sitemap
 *
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Kontroler
 */
final class Sitemap_Kontroler extends Master_Kontroler {

    /**
     * ## index
     * @since 0.1.2.pre-alpha.M1
     *
     * @param ?Zahtjev $zahtjev [optional] <p>
     * Zahtjev.
     * </p>
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    #[Zaglavlja(vrsta: Vrsta::XML)]
    public function index (Zahtjev $zahtjev = null):Sadrzaj {

        $domena = Server::Protokol() . '://'.Server::Domena();

        $podatci = <<<XML
            <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
                <sitemap>
                    <loc>$domena/sitemap/osnovno</loc>
                </sitemap>
                <sitemap>
                    <loc>$domena/sitemap/artikli</loc>
                </sitemap>
                <sitemap>
                    <loc>$domena/sitemap/kategorije</loc>
                </sitemap>
            </sitemapindex>
        XML;

        return sadrzaj()->format(Sadrzaj_Vrsta::HTMLP)->datoteka('xml.html')->podatci([
            'podatci' => $podatci
        ]);

    }

    /**
     * ## Sitemap
     * @since 0.1.0.pre-alpha.M1
     *
     * @param Zahtjev $zahtjev [optional] <p>
     * Zahtjev.
     * </p>
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    #[Zaglavlja(vrsta: Vrsta::XML)]
    public function osnovno (Zahtjev $zahtjev):Sadrzaj {

        $domena = Server::Protokol() . '://'.Server::Domena();

        $podatci = <<<XML
            <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
                <url>
                    <loc>$domena/</loc>
                </url>
                <url>
                    <loc>$domena/poslovnice/</loc>
                </url>
                <url>
                    <loc>$domena/onama/</loc>
                </url>
                <url>
                    <loc>$domena/opciuvjeti/</loc>
                </url>
            </urlset>
        XML;

        return sadrzaj()->format(Sadrzaj_Vrsta::HTMLP)->datoteka('xml.html')->podatci([
            'podatci' => $podatci
        ]);

    }

    /**
     * ## Sitemap
     * @since 0.1.0.pre-alpha.M1
     *
     * @param Zahtjev $zahtjev [optional] <p>
     * Zahtjev.
     * </p>
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    #[Zaglavlja(vrsta: Vrsta::XML)]
    public function kategorije (Zahtjev $zahtjev):Sadrzaj {

        $domena = Server::Protokol() . '://'.Server::Domena();

        $kategorije = $this->model(Kategorije_Model::class);

        $podatci = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($kategorije->kategorije() as $kategorija) {

            $podatci .= <<<XML
                <url>
                    <loc>$domena/rezultat/{$kategorija['Link']}/</loc>
                </url>
            XML;

        }
        $podatci .= '</urlset>';

        return sadrzaj()->format(Sadrzaj_Vrsta::HTMLP)->datoteka('xml.html')->podatci([
            'podatci' => $podatci
        ]);

    }

    /**
     * ## Sitemap
     * @since 0.1.0.pre-alpha.M1
     *
     * @param Zahtjev $zahtjev [optional] <p>
     * Zahtjev.
     * </p>
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    #[Zaglavlja(vrsta: Vrsta::XML)]
    public function artikli (Zahtjev $zahtjev):Sadrzaj {

        $domena = Server::Protokol() . '://'.Server::Domena();

        $artikli_model = $this->model(Artikli_Model::class);
        $artikli = $artikli_model->artikli('sve', 'sve', 0, 1000, 'sve velicine', 'svi artikli', 'cijenafinal', 'desc');

        $podatci = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($artikli as $artikl) {

            $podatci .= <<<XML
                <url>
                    <loc>$domena/artikl/{$artikl['Link']}/</loc>
                </url>
            XML;

        }
        $podatci .= '</urlset>';

        return sadrzaj()->format(Sadrzaj_Vrsta::HTMLP)->datoteka('xml.html')->podatci([
            'podatci' => $podatci
        ]);

    }

    /**
     * ## Posalji sitemap
     * @since 0.1.0.pre-alpha.M1
     *
     * @return Sadrzaj Sadržaj stranice.
     */
    #[Zaglavlja(vrsta: Vrsta::JSON)]
    public function posalji ():Sadrzaj {

        $sitemap_model = $this->model(Sitemap_Model::class);

        return sadrzaj()->format(Sadrzaj_Vrsta::JSON)->podatci([
            'Poruka' => $sitemap_model->posalji()
        ]);

    }

}