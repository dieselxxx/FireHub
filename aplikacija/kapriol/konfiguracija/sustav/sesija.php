<?php declare(strict_types = 1);

/**
 * Datoteka za konfiguraciju parametara za baza podataka
 * @since 0.5.3.pre-alpha.M5
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
    | Vrsta sesije
    |--------------------------------------------------------------------------
     * Zadana vrsta sesije ukoliko nije navedena u konfiguracijskim
     * datotekama aplikacije.
     * @since 0.5.3.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'vrsta' => env('SESIJA_VRSTA','datoteka'),

    /**
    |--------------------------------------------------------------------------
    | Naziv sesije
    |--------------------------------------------------------------------------
     * Zadani naziv sesije ukoliko nije naveden u poslužitelju.
     * @since 0.5.3.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'naziv' => env('SESIJA_NAZIV','FireHub_Sesija'),

    /**
    |--------------------------------------------------------------------------
    | Putanja sesije
    |--------------------------------------------------------------------------
     * Putanja u kojoj se sesije spremaju.
     * @since 0.5.3.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'lokacija' => FIREHUB_ROOT . env('SESIJA_LOKACIJA', 'sesije'),

    /**
     * --------------------------------------------------------------------------
     * Vrijeme
     * --------------------------------------------------------------------------
     * Maksimalno vrijeme kolačića.
     * @since 0.5.3.pre-alpha.M5
     *
     * @var array<string, int>
     */
    'vrijeme' => env('SESIJA_VRIJEME', '86400'),

    /**
     * --------------------------------------------------------------------------
     * Putanja
     * --------------------------------------------------------------------------
     * Putanja za koju vrijedi kolačić.
     * @since 0.5.3.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'putanja' => env('SESIJA_PUTANJA', '/'),

    /**
     * --------------------------------------------------------------------------
     * Domena
     * --------------------------------------------------------------------------
     * Domena za koju vrijedi kolačić.
     * @since 0.5.3.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'domena' => env('SESIJA_DOMENA', ''),

    /**
     * --------------------------------------------------------------------------
     * SSL
     * --------------------------------------------------------------------------
     * Da li kolačić zahtjeva SSL enkriptiranu vezu.
     * @since 0.5.3.pre-alpha.M5
     *
     * @var array<string, bool>
     */
    'ssl' => env('SESIJA_SSL', false),

    /**
     * --------------------------------------------------------------------------
     * HTTP
     * --------------------------------------------------------------------------
     * Da li je kolačić dostupan samo u HTTP protokolu.
     * @since 0.5.3.pre-alpha.M5
     *
     * @var array<string, bool>
     */
    'http' => env('SESIJA_HTTP', true),

    /**
     * --------------------------------------------------------------------------
     * Ista stranica
     * --------------------------------------------------------------------------
     * Restrikcija kolačića.
     * @since 0.5.3.pre-alpha.M5
     *
     * @var array<string, \FireHub\Jezgra\Komponente\Kolacic\Enumeratori\IstaStranica>
     */
    'ista_stranica' => \FireHub\Jezgra\Komponente\Kolacic\Enumeratori\IstaStranica::LAX

];