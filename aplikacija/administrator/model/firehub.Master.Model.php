<?php declare(strict_types = 1);

/**
 * Master model
 * @since 0.1.2.pre-alpha.M1
 *
 * @author Danijel Galić
 * @copyright 2022 Kapriol Web Trgovina
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Aplikacija\Model
 */

namespace FireHub\Aplikacija\Administrator\Model;

use FireHub\Jezgra\Model\Model;
use FireHub\Jezgra\Komponente\Sesija\Sesija;
use FireHub\Jezgra\Komponente\Sesija\Sesija_Interface;

/**
 * ### Master Model
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Model
 */
abstract class Master_Model extends Model {

    protected Sesija_Interface $sesija;

    /**
     * ### Konstruktor
     * @since 0.1.2.pre-alpha.M1
     */
    public function __construct (
    ) {

        // napravi sesiju
        $this->sesija = (new Sesija())->naziv('Kapriol')->napravi();

    }

}