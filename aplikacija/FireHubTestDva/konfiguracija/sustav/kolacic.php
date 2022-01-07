<?php declare(strict_types = 1);

/**
 * Datoteka za konfiguraciju kolačića
 * @since 0.5.2.pre-alpha.M5
 *
 * @author Danijel Galić
 * @copyright 2022 FireHub Web Application Framework
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Sustav\Konfiguracija
 */

return [

    /**
     * --------------------------------------------------------------------------
     * Vrijeme
     * --------------------------------------------------------------------------
     * Maksimalno vrijeme kolačića.
     * @since 0.5.2.pre-alpha.M5
     *
     * @var array<string, int>
     */
    'vrijeme' => 86400
    ,

    /**
     * --------------------------------------------------------------------------
     * Putanja
     * --------------------------------------------------------------------------
     * Putanja za koju vrijedi kolačić.
     * @since 0.5.2.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'putanja' => '/',

    /**
     * --------------------------------------------------------------------------
     * Domena
     * --------------------------------------------------------------------------
     * Domena za koju vrijedi kolačić.
     * @since 0.5.2.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'domena' => '',

    /**
     * --------------------------------------------------------------------------
     * SSL
     * --------------------------------------------------------------------------
     * Da li kolačić zahtjeva SSL enkriptiranu vezu.
     * @since 0.5.2.pre-alpha.M5
     *
     * @var array<string, bool>
     */
    'ssl' => false,

    /**
     * --------------------------------------------------------------------------
     * HTTP
     * --------------------------------------------------------------------------
     * Da li je kolačić dostupan samo u HTTP protokolu.
     * @since 0.5.2.pre-alpha.M5
     *
     * @var array<string, bool>
     */
    'http' => true,

    /**
     * --------------------------------------------------------------------------
     * Ista stranica
     * --------------------------------------------------------------------------
     * Restrikcija kolačića.
     * @since 0.5.2.pre-alpha.M5
     *
     * @var array<string, string>
     */
    'ista_stranica' => 'Lax'

];