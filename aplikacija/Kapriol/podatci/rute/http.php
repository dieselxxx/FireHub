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
Rute::sve('artikl/index', [\FireHub\Aplikacija\Kapriol\Kontroler\Artikl_Kontroler::class, 'index']);
Rute::sve('slika/malaslika', [\FireHub\Aplikacija\Kapriol\Kontroler\Slika_Kontroler::class, 'malaslika']);
Rute::sve('slika/velikaslika', [\FireHub\Aplikacija\Kapriol\Kontroler\Slika_Kontroler::class, 'velikaslika']);
Rute::sve('kosarica/dodaj', [\FireHub\Aplikacija\Kapriol\Kontroler\Kosarica_Kontroler::class, 'dodaj']);