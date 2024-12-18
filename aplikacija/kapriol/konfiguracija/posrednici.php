<?php declare(strict_types = 1);

/**
 * Konfiguracijska datoteka za mapiranje grupa posrednika u aplikaciji
 * @since 0.4.0.pre-alpha.M4
 *
 * @author Danijel Galić
 * @copyright 2022 FireHub Web Application Framework
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Sustav\HTTP
 */

return [

    /**
    |--------------------------------------------------------------------------
    | HTTP posrednici aplikacije
    |--------------------------------------------------------------------------
     * Lista posrednika koje vrijede za sve HTTP zahtjeve unutar aplikacije.
     * @since 0.4.0.pre-alpha.M4
     *
     * @var array<string, array<\FireHub\Jezgra\Posrednici\Posrednik::class>>
     */
    'http' => []

];