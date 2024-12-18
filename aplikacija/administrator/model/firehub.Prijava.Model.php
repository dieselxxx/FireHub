<?php declare(strict_types = 1);

/**
 * Prijava model
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

use FireHub\Jezgra\Komponente\BazaPodataka\BazaPodataka;
use FireHub\Aplikacija\Kapriol\Jezgra\Validacija;
use FireHub\Jezgra\Greske\Greska;
use FireHub\Jezgra\Kontejner\Greske\Kontejner_Greska;

/**
 * ### Prijava model
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Model
 */
final class Prijava_Model extends Master_Model {

    private string $korisnicko_ime;
    private string $lozinka;

    /**
     * ### Konstruktor
     * @since 0.1.2.pre-alpha.M1
     *
     * @throws Greska
     */
    public function __construct (
        private BazaPodataka $bazaPodataka
    ) {

        parent::__construct();

        // ako nisu poslani svi podatci za prijavu
        if (!isset($_POST["korisnicko_ime"]) || !isset($_POST["lozinka"])) {

            throw new Greska('Nema dovoljno podataka za prijavu!');

        }

        $this->korisnicko_ime = $_POST["korisnicko_ime"];
        $this->lozinka = $_POST["lozinka"];

        $this->korisnicko_ime = Validacija::Prilagodjen('/^[a-z0-9]+$/i', _('Vaše korisničko ime'), $this->korisnicko_ime, 5, 20);
        $this->lozinka = Validacija::String(_('Lozinka'), $this->lozinka);

        // dohvati korisnika
        if ($this->prijava()) {

            $this->sesija->zapisi('korisnik', 'prijavljen');

        }

    }

    /**
     * ### Dohvati ID korisnika
     * @since 0.1.2.pre-alpha.M1
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca objekta.
     * @throws Greska
     *
     * @return bool
     */
    private function prijava ():bool {

        $korisnik = $this->bazaPodataka->tabela('artikliview')
            ->sirovi("
                SELECT
                    ID
                FROM korisnik
                WHERE Username = '$this->korisnicko_ime'
                AND Password = '$this->lozinka'
                AND Nivo = 'admin' AND Aktivan = 1
                LIMIT 1
            ")
            ->napravi();

        if ($korisnik->broj_zapisa() !== 1) {

            throw new Greska('Korisničko ime ili lozinka nisu ispravni!');

        }

        return true;

    }

}