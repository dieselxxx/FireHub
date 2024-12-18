<?php declare(strict_types = 1);

/**
 * Datoteka posrednika za prijavu
 * @since 0.1.2.pre-alpha.M1
 *
 * @author Danijel Galić
 * @copyright 2022 FireHub Web Application Framework
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Sustav\Posrednici
 */

namespace FireHub\Aplikacija\Administrator\Posrednici;

use FireHub\Jezgra\Posrednici\Posrednik;

/**
 * ### Posrednik za prijavu
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Sustav\Posrednici
 */
final class Prijava implements Posrednik {

    /**
     * @inheritDoc
     */
    public function obradi ():bool {

        return true;

    }

}