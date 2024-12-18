<?php declare(strict_types = 1);

/**
 * Datoteka za konfiguraciju HTML teme aplikacije
 * @since 0.4.4.pre-alpha.M4
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
    | Lista tema
    |--------------------------------------------------------------------------
     * Lista dostupnih tema za oblikovanje HTML sadržaja aplikacije.
     * @since 0.4.4.pre-alpha.M4
     *
     * @var array<string, array<string, string>>
     */
    'lista' => [
        'danijel'
    ],

    /**
    |--------------------------------------------------------------------------
    | Zadana tema
    |--------------------------------------------------------------------------
     * Zadana baza za oblikovanje HTML sadržaja aplikacije.
     * @since 0.4.4.pre-alpha.M4
     *
     * @var array<string, string>
     */
    'odabrano' => env('ZADANA_TEMA','kapriol')

];