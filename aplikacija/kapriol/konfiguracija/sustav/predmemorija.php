<?php declare(strict_types = 1);

/**
 * Datoteka za konfiguraciju parametara za predmemoriju
 * @since 0.5.0.pre-alpha.M5
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
    | Koristi predmemoriju
    |--------------------------------------------------------------------------
     * Da li aplikacija i sustav koriste predmemoriju.
     * Ova opcija ne utječe na korištenje predmemorije unutar apliakcije.
     * @since 0.5.0.pre-alpha.M5
     *
     * @var array<string, bool>
     */
    'ukljuceno' => env('PREDMEMORIJA',false),

    /**
    |--------------------------------------------------------------------------
    | Server predmemorije
    |--------------------------------------------------------------------------
     * Zadana predmemorija koju aplikacija koristi.
     * @since 0.5.0.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'server' => env('PREDMEMORIJA_SERVER','memcache'),

    /**
    |--------------------------------------------------------------------------
    | Host
    |--------------------------------------------------------------------------
     * IP adresa na kojem je smještena predmemorija.
     * @since 0.5.0.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'host' => env('PREDMEMORIJA_HOST','localhost'),

    /**
    |--------------------------------------------------------------------------
    | Port
    |--------------------------------------------------------------------------
     * Port servera na kojem je smještena predmemorija.
     * @since 0.5.0.pre-alpha.M5
     *
     * @var array<string, int>
     */
    'port' => env('PREDMEMORIJA_PORT','11211'),

    /**
    |--------------------------------------------------------------------------
    | Korisničko ime
    |--------------------------------------------------------------------------
     * Korisničko ime za spajanje na server predmemorije.
     * @since 0.5.0.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'korisnicko_ime' => env('PREDMEMORIJA_KORISNICKO_IME',''),

    /**
    |--------------------------------------------------------------------------
    | Lozinka
    |--------------------------------------------------------------------------
     * Lozinka za spajanje na server predmemorije.
     * @since 0.5.0.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'lozinka' => env('PREDMEMORIJA_LOZINKA',''),

    /**
    |--------------------------------------------------------------------------
    | Trajna konekcija
    |--------------------------------------------------------------------------
     * Da li je konekcija na predmemoriju trajna.
     * @since 0.5.0.pre-alpha.M5
     *
     * @var array<string, bool>
     */
    'trajno' => true,

    /**
    |--------------------------------------------------------------------------
    | Težina
    |--------------------------------------------------------------------------
     * Težina u odnosu na ostale servere predmemorije.
     * @since 0.5.0.pre-alpha.M5
     *
     * @var array<string, int>
     */
    'tezina' => 1,

    /**
    |--------------------------------------------------------------------------
    | Odziv
    |--------------------------------------------------------------------------
     * Maksimalni odziv u sekundama na koji se čeka da server reagira.
     * @since 0.5.0.pre-alpha.M5
     *
     * @var array<string, float>
     */
    'odziv' => env('PREDMEMORIJA_ODZIV', '0.5'),

    /**
    |--------------------------------------------------------------------------
    | Interval ponovni pokušaj
    |--------------------------------------------------------------------------
     * Interval u kojem će server ponovno pokušati pronaći zapise.
     * @since 0.5.0.pre-alpha.M5
     *
     * @var array<string, int>
     */
    'interval_ponovni_pokusaj' => env('PREDMEMORIJA_INTERVAL_PONOVNI_POKUSAJ', '60'),

    /**
    |--------------------------------------------------------------------------
    | Dodatni serveri
    |--------------------------------------------------------------------------
     * Dodatni serveri za predmemoriju.
     * Primjer: # host=192.168.8.206,port=11211,korisnicko_ime=,lozinka=;host=192.168.8.50,port=11211,korisnicko_ime=,lozinka=
     * @since 0.5.0.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'dodatni_serveri' => env('PREDMEMORIJA_DODATNI_SERVERI', '')

];