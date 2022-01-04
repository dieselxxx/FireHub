<?php declare(strict_types = 1);

/**
 * Datoteka za HTML sadržaj
 * @since 0.4.4.pre-alpha.M4
 *
 * @author Danijel Galić
 * @copyright 2022 FireHub Web Application Framework
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Sustav\Sadrzaj
 */

namespace FireHub\Jezgra\Sadrzaj\Vrste;

use FireHub\Jezgra\Sadrzaj\Vrsta_Interface;

/**
 * ### Klasa za HTML sadržaj
 * @since 0.4.4.pre-alpha.M4
 *
 * @package Sustav\Sadrzaj
 */
final class HTML implements Vrsta_Interface {

    /**
     * {@inheritDoc}
     *
     * @param string $datoteka [optional] <p>
     * Datoteka za učitavanja.
     * </p>
     */
    public function __construct (
        private array $podatci,
        private string $datoteka = ''
    ) {

    }

    /**
     * @inheritDoc
     */
    public function ispisi ():string {

        var_dump($this->datoteka);

        return '<br><b>'.round(memory_get_peak_usage()/1048576, 2) . ' mb</b>';

    }

}