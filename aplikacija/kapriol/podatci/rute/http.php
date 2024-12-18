<?php declare(strict_types = 1);

/**
 * Rute za HTTP pozive aplikacije
 * @since 0.4.1.pre-alpha.M4
 *
 * @author Danijel Galić
 * @copyright 2022 FireHub Web Application Framework
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Sustav\Jezgra
 */

use FireHub\Jezgra\Komponente\Rute\Rute;

Rute::sve('naslovna/index', [\FireHub\Aplikacija\Kapriol\Kontroler\Naslovna_Kontroler::class, 'index']);
Rute::sve('rezultat/index', [\FireHub\Aplikacija\Kapriol\Kontroler\Rezultat_Kontroler::class, 'index']);
Rute::sve('kategorija/index', [\FireHub\Aplikacija\Kapriol\Kontroler\Kategorija_Kontroler::class, 'index']);
Rute::sve('artikl/index', [\FireHub\Aplikacija\Kapriol\Kontroler\Artikl_Kontroler::class, 'index']);
Rute::sve('slika/malaslika', [\FireHub\Aplikacija\Kapriol\Kontroler\Slika_Kontroler::class, 'malaslika']);
Rute::sve('slika/velikaslika', [\FireHub\Aplikacija\Kapriol\Kontroler\Slika_Kontroler::class, 'velikaslika']);
Rute::sve('slika/kategorija', [\FireHub\Aplikacija\Kapriol\Kontroler\Slika_Kontroler::class, 'kategorija']);
Rute::sve('slika/podkategorija', [\FireHub\Aplikacija\Kapriol\Kontroler\Slika_Kontroler::class, 'podkategorija']);
Rute::sve('slika/baner', [\FireHub\Aplikacija\Kapriol\Kontroler\Slika_Kontroler::class, 'baner']);
Rute::sve('slika/banerdno', [\FireHub\Aplikacija\Kapriol\Kontroler\Slika_Kontroler::class, 'banerdno']);
Rute::sve('slika/reklama', [\FireHub\Aplikacija\Kapriol\Kontroler\Slika_Kontroler::class, 'reklama']);
Rute::sve('slika/usluga', [\FireHub\Aplikacija\Kapriol\Kontroler\Slika_Kontroler::class, 'usluga']);
Rute::sve('kosarica/index', [\FireHub\Aplikacija\Kapriol\Kontroler\Kosarica_Kontroler::class, 'index']);
Rute::sve('kosarica/narudzba', [\FireHub\Aplikacija\Kapriol\Kontroler\Kosarica_Kontroler::class, 'narudzba']);
Rute::sve('kosarica/narudzbab2b', [\FireHub\Aplikacija\Kapriol\Kontroler\Kosarica_Kontroler::class, 'narudzbab2b']);
Rute::sve('kosarica/naruci', [\FireHub\Aplikacija\Kapriol\Kontroler\Kosarica_Kontroler::class, 'naruci']);
Rute::sve('kosarica/ga4purchase', [\FireHub\Aplikacija\Kapriol\Kontroler\Kosarica_Kontroler::class, 'ga4purchase']);
Rute::sve('kosarica/odabir', [\FireHub\Aplikacija\Kapriol\Kontroler\Kosarica_Kontroler::class, 'odabir']);
Rute::sve('kosarica/ispravno', [\FireHub\Aplikacija\Kapriol\Kontroler\Kosarica_Kontroler::class, 'ispravno']);
Rute::sve('poslovnice/index', [\FireHub\Aplikacija\Kapriol\Kontroler\Poslovnice_Kontroler::class, 'index']);
Rute::sve('kontakt/index', [\FireHub\Aplikacija\Kapriol\Kontroler\Kontakt_Kontroler::class, 'index']);
Rute::sve('kontakt/ispravno', [\FireHub\Aplikacija\Kapriol\Kontroler\Kontakt_Kontroler::class, 'ispravno']);
Rute::sve('favorit/index', [\FireHub\Aplikacija\Kapriol\Kontroler\Favorit_Kontroler::class, 'ispravno']);
Rute::sve('kolacic/index', [\FireHub\Aplikacija\Kapriol\Kontroler\Kolacic_Kontroler::class, 'index']);
Rute::sve('kolacic/osobnipodatci', [\FireHub\Aplikacija\Kapriol\Kontroler\Kolacic_Kontroler::class, 'osobnipodatci']);
Rute::sve('sitemap/index', [\FireHub\Aplikacija\Kapriol\Kontroler\Sitemap_Kontroler::class, 'index']);
Rute::sve('sitemap/osnovno', [\FireHub\Aplikacija\Kapriol\Kontroler\Sitemap_Kontroler::class, 'osnovno']);
Rute::sve('sitemap/kategorije', [\FireHub\Aplikacija\Kapriol\Kontroler\Sitemap_Kontroler::class, 'kategorije']);
Rute::sve('sitemap/artikli', [\FireHub\Aplikacija\Kapriol\Kontroler\Sitemap_Kontroler::class, 'artikli']);
Rute::sve('sitemap/posalji', [\FireHub\Aplikacija\Kapriol\Kontroler\Sitemap_Kontroler::class, 'posalji']);