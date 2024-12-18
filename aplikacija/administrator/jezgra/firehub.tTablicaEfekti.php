<?php declare(strict_types=1);

/**
 * Trait za efekte tablice u administraciji
 * @since 0.1.2.pre-alpha.M1
 *
 * @author Danijel Galić
 * @copyright 2022 Kapriol Web Trgovina
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Aplikacija\Jezgra
 */

namespace FireHub\Aplikacija\Administrator\Jezgra;

trait tTablicaEfekti {

    protected string $poredaj;
    protected string $redoslijed;

    /**
     * Obrnuti redoslijed.
     *
     * @return string
     */
    protected function RedoslijedObrnuto ():string {

        if ($this->redoslijed === 'asc') {

            return 'desc';

        }

        return 'asc';

    }

    /**
     * Ikona za redoslijed.
     *
     * @param string $poredaj
     * @param string $redoslijed
     *
     * @return string
     */
    protected function RedoslijedIkona (string $poredaj, string $redoslijed):string {

        if ($this->poredaj === $poredaj && $this->redoslijed === $redoslijed) {

            return 'aktivno';

        }

        return 'neaktivno';

    }

}