<?php declare(strict_types = 1);

/**
 * Datoteka za konfiguraciju parametara za baza podataka
 * @since 0.5.1.pre-alpha.M5
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
    | Server baze podataka
    |--------------------------------------------------------------------------
     * Zadana baza podataka koju aplikacija koristi.
     * @since 0.5.1.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'server' => env('BAZA_PODATAKA_SERVER','MSSQL'),

    /**
    |--------------------------------------------------------------------------
    | Host
    |--------------------------------------------------------------------------
     * IP adresa na kojem je smještena baza podataka.
     * @since 0.5.1.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'host' => env('BAZA_PODATAKA_HOST','localhost'),

    /**
    |--------------------------------------------------------------------------
    | Port
    |--------------------------------------------------------------------------
     * Port servera na kojem je smještena baza podataka.
     * @since 0.5.1.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'port' => env('BAZA_PODATAKA_PORT','1143'),

    /**
    |--------------------------------------------------------------------------
    | Instanca
    |--------------------------------------------------------------------------
     * Instanca baze podataka.
     * @since 0.5.1.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'instanca' => env('BAZA_PODATAKA_INSTANCA','MSSQLSERVER'),

    /**
    |--------------------------------------------------------------------------
    | Baza
    |--------------------------------------------------------------------------
     * Baza baze podataka.
     * @since 0.5.1.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'baza' => env('BAZA_PODATAKA_BAZA','FireHub'),

    /**
    |--------------------------------------------------------------------------
    | Shema
    |--------------------------------------------------------------------------
     * Shema baze podataka.
     * @since 0.5.1.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'shema' => env('BAZA_PODATAKA_SHEMA','dbo'),

    /**
    |--------------------------------------------------------------------------
    | Korisničko ime
    |--------------------------------------------------------------------------
     * Korisničko ime za spajanje na bazu podataka.
     * @since 0.5.1.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'korisnicko_ime' => env('BAZA_PODATAKA_KORISNICKO_IME',''),

    /**
    |--------------------------------------------------------------------------
    | Lozinka
    |--------------------------------------------------------------------------
     * Lozinka za spajanje na bazu podataka.
     * @since 0.5.1.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'lozinka' => env('BAZA_PODATAKA_LOZINKA',''),

    /**
    |--------------------------------------------------------------------------
    | Odziv
    |--------------------------------------------------------------------------
     * Maksimalni odziv servera u sekundama prilikom upit.
     * @since 0.5.1.pre-alpha.M5
     *
     * @var array<string, int>
     */
    'odziv' => 500,

    /**
    |--------------------------------------------------------------------------
    | Stream u dijelovima
    |--------------------------------------------------------------------------
     * Slanje svih podataka pri izvršavanju u upita ili u dijelovima.
     * @since 0.5.1.pre-alpha.M5
     *
     * @var array<string, bool>
     */
    'posalji_stream_pri_izvrsavanju' => true,

    /**
    |--------------------------------------------------------------------------
    | Kursor upita
    |--------------------------------------------------------------------------
     * Način redoslijeda odabiranja redaka.
     * @since 0.5.1.pre-alpha.M5
     *
     * @var array<string, \FireHub\Jezgra\Komponente\BazaPodataka\Kursor_Interface>
     */
    'kursor' => \FireHub\Jezgra\Komponente\BazaPodataka\Servisi\MSSQL\Enumeratori\Kursor::SQLSRV_CURSOR_FORWARD

];