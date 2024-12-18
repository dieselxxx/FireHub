<?php declare(strict_types = 1);

/**
 * GDPR model
 * @since 0.1.2.pre-alpha.M1
 *
 * @author Danijel Galić
 * @copyright 2022 Kapriol Web Trgovina
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Aplikacija\Model
 */

namespace FireHub\Aplikacija\Kapriol\Model;

use FireHub\Jezgra\Komponente\Kolacic\Kolacic;

/**
 * ### GDPR model
 *
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Model
 */
final class Gdpr_Model extends Master_Model {

    /**
     * ### Konstruktor
     * @since 0.1.2.pre-alpha.M1
     */
    public function __construct (
        private Kolacic $kolacic
    ){

        parent::__construct();

    }

    /**
     * ### Prihvati cookie
     * @since 0.1.2.pre-alpha.M1
     *
     * @return bool
     */
    public function prihvati ():bool {

        $gdpr = $this->kolacic->naziv('gdpr')->vrijednost('ok')->napravi();

        return $gdpr->spremi();

    }

    /**
     * ### Broj cookie
     * @since 0.1.2.pre-alpha.M1
     *
     * @return string
     */
    public function html ():string {

        $gdpr = $this->kolacic->napravi();

        if (!$gdpr->procitaj('gdpr')) {

            return '
                <div id="gdpr">
                    <div>
                        <h2>Kolačići (Cookies)</h2>
                        <br>Ove Web stranice i njezini alati trećih strana (third-party tools) koriste kolačiće (cookies) uglavnom za osnovno funkcioniranje i analitiku prometa.
                        <br><br>Za više informacija pročitajte našu <a href="/kolacic/osobnipodatci">Politiku obrade osobnih podataka</a>.
                        <br><br><br><a class="gumb" href="#" data-boja="boja" onclick="$_Cookie(\'da\');">Prihvaćam</a> <a class="gumb sivi" href="#" onclick="$_Cookie(\'ne\');">Ne prihvaćam</a><br><br>
                    </div>
                </div>
            ';

        }

        return '';

    }

}