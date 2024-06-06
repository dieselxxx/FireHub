<?php declare(strict_types = 1);

/**
 * Domena
 * @since 0.1.2.pre-alpha.M1
 *
 * @author Danijel Galić
 * @copyright 2022 Kapriol Web Trgovina
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Aplikacija\Kontroler
 */

namespace FireHub\Aplikacija\Kapriol\Jezgra;

final class Domena {

    public static function Ba ():bool {

        if (Server::Domena() !== 'www.kapriol-point.hr') {

            return true;

        }

        return false;

    }

    public static function Hr ():bool {

        if (Server::Domena() === 'www.kapriol.hr' || Server::Domena() === 'www.kapriol-point.hr' || Server::Domena() === 'kapriol-point.hr' || Server::Domena() === 'test.kapriol-point.hr') {


            return true;

        }

        return false;

    }

    public static function emailNarudzbe ():array {

        if (self::Hr()) {

            return array(
                array("adresa" => 'imotski@kapriol-point.com', "ime" => 'Kapriol Imotski'),
                array("adresa" => 'kapriol@kapriol-point.com', "ime" => 'Kapriol')
            );
        }

        return array(
            array("adresa" => 'mostar@kapriol-point.com', "ime" => 'Kapriol Mostar'),
            array("adresa" => 'kapriol@kapriol-point.com', "ime" => 'Kapriol')
        );

    }

    public static function sqlTablica ():string {

        if (self::Hr()) {

            return 'Hr';

        }

        return 'Ba';

    }

    public static function sqlCijena ():string {

        if (self::Hr()) {

            return 'CijenaKn';

        }

        return 'Cijena';

    }

    public static function sqlCijenaAkcija ():string {

        if (self::Hr()) {

            return 'CijenaAkcijaKn';

        }

        return 'CijenaAkcija';

    }

    public static function sqlOutlet ():string {

        if (self::Hr()) {

            return 'OutletHr';

        }

        return 'Outlet';

    }

    public static function valuta ():string {

        if (self::Hr()) {

            return '€';

        }

        return 'KM';

    }

    public static function valutaISO ():string {

        if (self::Hr()) {

            return 'EUR';

        }

        return 'BAM';

    }

    public static function telefon ():string {

        if (self::Hr()) {

            return '+385 21 486 385';

        }

        return '+387 36 349 223';

    }

    public static function adresa ():string {

        if (self::Hr()) {

            return 'Glavina Donja 336, 21260 Imotski';

        }

        return 'Dubrovačka bb (SC Piramida), 88000 Mostar';

    }

    public static function poslovnice ():string {

        if (self::Hr()) {

            return 'poslovnice_hr.html';

        }

        return 'poslovnice.html';

    }

    public static function podnozjeDostava ():string {

        if (self::Hr()) {

            return '
                <ul>
                    <li>
                        <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno_duplo2"></use></svg>
                        <span>Besplatna dostava za narudzbe preko 149 €.</span>
                    </li>
                    <li>
                        <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno_duplo2"></use></svg>
                        <span>Dostava za narudzbe manje od 149 € iznosi 6 €.</span>
                    </li>
                    <li>
                        <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno_duplo2"></use></svg>
                        <span>Dostava brzom poštom u roku 24-48 h.</span>
                    </li>
                    <li>
                        <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno_duplo2"></use></svg>
                        <span>Mogućnost plaćanja pouzećem, općom uplatnicom, internet bankarstvom.</span>
                    </li>
                </ul>
            ';

        }

        return '
                <ul>
                    <li>
                        <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno_duplo2"></use></svg>
                        <span>Besplatna dostava za narudzbe preko 149 KM.</span>
                    </li>
                    <li>
                        <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno_duplo2"></use></svg>
                        <span>Dostava za narudzbe manje od 149 KM iznosi 9 KM.</span>
                    </li>
                    <li>
                        <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno_duplo2"></use></svg>
                        <span>Dostava brzom poštom u roku 24-48 h.</span>
                    </li>
                    <li>
                        <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno_duplo2"></use></svg>
                        <span>Mogućnost plaćanja pouzećem, općom uplatnicom, internet bankarstvom.</span>
                    </li>
                </ul>
            ';

    }

    public static function OIBPDV ():string {

        if (self::Hr()) {

            return 'OIB';

        }

        return 'PDV';

    }

    public static function dostavaLimit ():int|float {

        if (self::Hr()) {

            return 149;

        }

        return 149;

    }

    public static function dostavaIznos ():int|float {

        if (self::Hr()) {

            return 6;

        }

        return 9;

    }

    public static function facebook ():string {

        if (self::Hr()) {

            return 'https://www.facebook.com/kapriol.hr';

        }

        return 'https://www.facebook.com/kapriol.ba';

    }

    public static function instagram ():string {

        if (self::Hr()) {

            return 'https://www.instagram.com/kapriol.hr/';

        }

        return 'https://www.instagram.com/kapriol.sa/';

    }

    public static function mobitel ():string {

        if (self::Hr()) {

            return '385996039376';

        }

        return '38763363270';

    }

    public static function opciUvjeti ():string {

        if (self::Hr()) {

            return '
                <li>
                    <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno_duplo2"></use></svg>
                    <span><a href="/opciuvjeti">Opći uvjeti</a></span>
                </li>
                <li>
                    <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno_duplo2"></use></svg>
                    <span><a target="_blank" href="/kapriol/resursi/datoteke/Obrazac_za_jednostrani_raskid_ugovora.pdf">Obrazac za jednostrani raskid ugovora</a></span>
                </li>
            ';

        }

        return '';

    }

    public static function opis ():string {

        if (self::Hr()) {

            return 'KAPRIOL POINT d.o.o. Imotski, zastupnik i distributer branda Kapriol na tržištu Hrvatske. Glavina Donja 336, 21260 | Imotski | Tel.: +385 21 486 385 | kapriol(at)kapriol-point.com';

        }

        return 'KAPRIOL POINT d.o.o. Mostar, zastupnik i distributer branda Kapriol na tržištu Bosne i Hercegovine. Kneza Višesalava bb (SPC Mljekara, Lamela A) | Mostar | Tel.: +387 36 349 223 | kapriol(at)kapriol-point.com';

    }

    public static function GA ():string {

        if (self::Hr()) {

            return 'G-NJ1TDD52J1';

        }

        return 'G-TJ5HRB99K9';

    }

    public static function linkerRetargeting ():string {

        if (self::Hr()) {

            return '<img src="https://linker.hr/m.php?campaign=416" style="display:none">';

        } else {

            return '';

        }

    }

    public static function linkerKonverzija ():string {

        if (self::Hr()) {

            return '<img src="https://linker.hr/c.php?id=3794" style="display:none">';

        } else {

            return '';

        }

    }

    public static function euroEmail (float|int $total_cijena):string {

        if (self::Hr()) {

            return '1 EUR = 7,53450 KN - Ukupno HRK '.number_format((float)$total_cijena * 7.5345, 2, ',', '.').'';

        }

        return '';

    }

    public static function blackFriday ():bool {

        if (date("m-d") >= '11-25' && date("m-d") <= '11-25') {

            return true;

        }

        return false;

    }

    public static function blackFridayPopust ():float {

        return 0.15;

    }

    public static function prodajniPredstavnik ():string {

        if (self::Hr()) {

            return '';

        }

        return '
        <li>
            <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#korisnik"></use></svg>
            <ul>
                <li>Selma Šalaka, prodajni predstavik</li>
                <li>Mob: 063/282-370</li>
                <li>Email: selma@kapriol-point.com</li>
            </ul>
        </li>
        ';

    }

}