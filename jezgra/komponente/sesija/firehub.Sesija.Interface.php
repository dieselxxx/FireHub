<?php declare(strict_types = 1);

/**
 * Datoteka interface-a za sesija servise
 * @since 0.5.3.pre-alpha.M5
 *
 * @author Danijel Galić
 * @copyright 2022 FireHub Web Application Framework
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Sustav\Jezgra
 */

namespace FireHub\Jezgra\Komponente\Sesija;

use FireHub\Jezgra\Komponente\Servis_Interface;

/**
 * ### Interface-a za sesija servise
 * @since 0.5.3.pre-alpha.M5
 *
 * @package Sustav\Jezgra
 */
interface Sesija_Interface extends Servis_Interface {

    /**
     * ### Status sesije
     * @since 0.5.3.pre-alpha.M5
     *
     * @return bool Da li je aktivna sesija.
     */
    public function status ():bool;

    /**
     * ### Zapiši vrijednost u sesiju
     * @since 0.5.3.pre-alpha.M5
     *
     * @param string $kljuc <p>
     * Naziv zapisa kojeg zapisujemo u sesiju.
     * </p>
     * @param mixed $vrijednost <p>
     * Vrijednost koju zapisujemo u sesiju.
     * </p>
     *
     * @return bool Da li je spremljen zapis sesije.
     */
    public function zapisi (string $kljuc, mixed $vrijednost):bool;

    /**
     * ### Dodaj zapis u niz
     * @since 0.6.1.alpha.M1
     *
     * @param string $niz <p>
     * Naziv niza u koji dodajemo zapis.
     * </p>
     * @param string $kljuc <p>
     * Naziv zapisa kojeg zapisujemo u sesiju.
     * </p>
     * @param mixed $vrijednost <p>
     * Vrijednost koju zapisujemo u sesiju.
     * </p>
     *
     * @return bool Da li je dodan zapis sesije u niz.
     */
    public function dodaj (string $niz, string $kljuc, mixed $vrijednost):bool;

    /**
     * ### Izbriši zapis iz niza
     * @since 0.6.1.alpha.M1
     *
     * @param string $niz <p>
     * Naziv niza iz kojeg brišemo zapis.
     * </p>
     * @param string $kljuc <p>
     * Naziv zapisa kojeg brišemo iz niza.
     * </p>
     *
     * @return bool Da li je izbrisan zapis sesije iz niza.
     */
    public function izbrisiNiz (string $niz, string $kljuc):bool;

    /**
     * ### Pročitaj vrijednost iz sesije
     * @since 0.5.3.pre-alpha.M5
     *
     * @param string $kljuc <p>
     * Naziv zapisa kojeg zapisujemo u sesiju.
     * </p>
     *
     * @return bool Vrijednost ključa sesije.
     */
    public function procitaj (string $kljuc):mixed;

    /**
     * ### Uredi vrijednost u sesiju
     * @since 0.5.3.pre-alpha.M5
     *
     * @param string $kljuc <p>
     * Naziv zapisa kojeg zapisujemo u sesiju.
     * </p>
     * @param mixed $vrijednost <p>
     * Vrijednost koju zapisujemo u sesiju.
     * </p>
     *
     * @return bool Da li je izmijenjen zapis sesije.
     */
    public function uredi (string $kljuc, mixed $vrijednost):bool;

    /**
     * ### Izbriši vrijednost iz sesije
     * @since 0.5.3.pre-alpha.M5
     *
     * @param string $kljuc <p>
     * Naziv zapisa kojeg brišemo iz sesije.
     * </p>
     *
     * @return bool Da li je izbrisan zapis sesije.
     */
    public function izbrisi (string $kljuc):bool;

    /**
     * ### Uništi sve podatke vezane za sesiju
     * @since 0.5.3.pre-alpha.M5
     *
     * @return bool Da li je uništena sesija.
     */
    public function unisti ():bool;

}