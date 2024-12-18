<?php declare(strict_types = 1);

/**
 * Datoteka za konfiguraciju glavnih parametara aplikacije
 * @since 0.3.5.pre-alpha.M3
 *
 * @author Danijel Galić
 * @copyright 2022 FireHub Web Application Framework
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Aplikacija\Konfiguracija
 */

return [

    /**
    |--------------------------------------------------------------------------
    | Informacije
    |--------------------------------------------------------------------------
     * Osnovne informacije o aplikaciji.
     * @since 0.3.5.pre-alpha.M3
     *
     * @var array<string, array<string, mixed>>
     */
    'informacije' => [
        'naziv' => env('APLIKACIJA_NAZIV',''),
        'ciklus' => env('APLIKACIJA_CIKLUS',''),
        'verzija' => env('APLIKACIJA_VERZIJA','')
    ],

    /**
    |--------------------------------------------------------------------------
    | Preduvjeti
    |--------------------------------------------------------------------------
     * Lista preduvjeta koji se moraju zadovoljiti kako bi aplikacija mogla
     * normalano raditi.
     * @since 0.3.5.pre-alpha.M3
     *
     * @var array<string, array<string, mixed>>
     */
    'preduvjeti' => [
        'firehub_verzija' => '0.6.1.alpha.M1',
        'php_verzija' => '8.1.0'
    ]

];