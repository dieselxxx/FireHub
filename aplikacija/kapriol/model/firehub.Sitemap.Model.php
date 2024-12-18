<?php declare(strict_types = 1);

/**
 * Sitemap model
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

use FireHub\Aplikacija\Kapriol\Jezgra\Server;

/**
 * ### Sitemap model
 *
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Model
 */
final class Sitemap_Model extends Master_Model {

    /**
     * URL-ovi tražilica
     *
     * @var array|string
     */
    private array $pretrazivaci = [
        [
            "http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid=USERID&url=",
            "http://search.yahooapis.com/SiteExplorerService/V1/ping?sitemap=",
        ],
        "http://www.google.com/ping?sitemap=",
        "http://submissions.ask.com/ping?sitemap=",
        "http://www.bing.com/ping?sitemap=",
    ];

    /**
     * URL sitemap datoteke
     *
     * @var string
     */
    private string $sitemap;

    /**
     * Šalje datoteku tražilicama
     * Google, Ask, Bing and Yahoo
     * Limit 1 u 24 sata.
     *
     * @since 0.0.69 GTGwebShop alpha
     *
     * @param string|null $yahoo_identifikator Yahoo appid.
     *
     * @return array $rezultat
     */
    public function posalji (string $yahoo_identifikator = null):array {

        // putanja sitema index-a
        $this->sitemap = Server::Protokol() . '://'.Server::Domena().'/sitemap';

        // ukoliko je dodan yahoo identifikator
        $this->pretrazivaci[0] = isset($yahoo_identifikator) ? str_replace("USERID", $yahoo_identifikator, $this->pretrazivaci[0][0]) : $this->pretrazivaci[0][1];

        $rezultat = [];
        for ($i = 0; $i < count($this->pretrazivaci); $i++) {

            // pošalji
            $posalji_stranicu = curl_init($this->pretrazivaci[$i] . htmlspecialchars($this->sitemap, ENT_QUOTES, 'UTF-8'));
            curl_setopt($posalji_stranicu, CURLOPT_RETURNTRANSFER, true);

            // odgovor
            $odgovor_sadrzaj = curl_exec($posalji_stranicu);
            $odgovor = curl_getinfo($posalji_stranicu);

            $posalji_stranicu_dijelovi = array_reverse(explode(".", parse_url($this->pretrazivaci[$i], PHP_URL_HOST)));

            // rezultat
            $rezultat[] = [
                "Tražilica" => $posalji_stranicu_dijelovi[1] . "." . $posalji_stranicu_dijelovi[0],
                "Stranica" => $this->pretrazivaci[$i] . htmlspecialchars($this->sitemap, ENT_QUOTES, 'UTF-8'),
                "Http kod statusa" => $odgovor['http_code'],
                "Poruka" => str_replace("\n", " ", strip_tags((string)$odgovor_sadrzaj)),
            ];

        }

        return $rezultat;

    }

}