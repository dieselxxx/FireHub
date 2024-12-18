<?php declare(strict_types = 1);

/**
 * Validacija
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

use Exception;
use FireHub\Jezgra\Greske\Greska;

final class Validacija {

    /**
     * ### Uklanja prazan prostor.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $vrijednost
     *
     * @return string Vrijednost
     */
    private static function Trim (string $vrijednost):string {

        return trim($vrijednost);

    }

    /**
     * ### Uklanja dovstruke navodnike.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $vrijednost
     *
     * @return string Vrijednost
     */
    private static function StripSlashes (string $vrijednost):string {

        return stripslashes($vrijednost);

    }

    /**
     * ### Pretvara posebne znakove u HTML oblik.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $vrijednost
     *
     * @return string Vrijednost
     */
    private static function HtmlSpecialChars (string $vrijednost):string {

        return htmlspecialchars($vrijednost);

    }

    /**
     * ### Validacija stringa.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $naziv
     * @param $vrijednost
     * @param int $min_znakova
     * @param int $max_znakova
     *
     * @throws Greska
     *
     * @return string Vrijednost
     */
    public static function String (string $naziv, $vrijednost, int $min_znakova = 0, int $max_znakova = 99999):string {

        $vrijednost = self::Trim($vrijednost);
        $vrijednost = self::StripSlashes($vrijednost);
        $vrijednost = self::HtmlSpecialChars($vrijednost);

        if (mb_strlen($vrijednost) < $min_znakova) {

            throw new Greska(sprintf(_('%s mora biti najmanje %d znakova! (kod: %d)'), $naziv, $min_znakova, 1), 1);

        } else if (mb_strlen($vrijednost) > $max_znakova) {

            throw new Greska(sprintf(_('%s ne smije biti veće od %d znakova! (kod: %d)'), $naziv, $max_znakova, 1), 1);

        }

        return $vrijednost;

    }

    /**
     * ### Validacija stringa.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $naziv
     * @param $vrijednost
     * @param int $min_znakova
     * @param int $max_znakova
     *
     * @throws Greska
     *
     * @return string Vrijednost
     */
    public static function StringHTML (string $naziv, $vrijednost, int $min_znakova = 0, int $max_znakova = 99999):string {

        $vrijednost = self::Trim($vrijednost);
        $vrijednost = self::StripSlashes($vrijednost);

        if (mb_strlen($vrijednost) < $min_znakova) {

            throw new Greska(sprintf(_('%s mora biti najmanje %d znakova! (kod: %d)'), $naziv, $min_znakova, 1), 1);

        } else if (mb_strlen($vrijednost) > $max_znakova) {

            throw new Greska(sprintf(_('%s ne smije biti veće od %d znakova! (kod: %d)'), $naziv, $max_znakova, 1), 1);

        }

        return $vrijednost;

    }

    /**
     * ### Validacija telefonskog broja.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $naziv
     * @param $vrijednost
     * @param int $min_znakova
     * @param int $max_znakova
     *
     * @throws Greska
     *
     * @return string Vrijednost
     */
    public static function Telefon (string $naziv, $vrijednost, int $min_znakova = 0, int $max_znakova = 99999):string {

        $vrijednost = self::Trim($vrijednost);
        $vrijednost = self::StripSlashes($vrijednost);
        $vrijednost = self::HtmlSpecialChars($vrijednost);
        $vrijednost = str_replace(' ', '', $vrijednost);

        if ($min_znakova > 0 && !preg_match("/^[0-9+-]+$/i", $vrijednost)) {

            throw new Greska(sprintf(_('%s nije u ispravnom obliku! (kod: %d)'), $naziv, 1), 1);

        } else if (strlen($vrijednost) < $min_znakova) {

            throw new Greska(sprintf(_('%s mora biti najmanje %d znakova! (kod: %d)'), $naziv, $min_znakova, 1), 1);

        } else if (strlen($vrijednost) > $max_znakova) {

            throw new Greska(sprintf(_('%s ne smije biti veće od %d znakova! (kod: %d)'), $naziv, $max_znakova, 1), 1);

        }

        return $vrijednost;

    }

    /**
     * ### Validacija broja.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $naziv
     * @param $vrijednost
     * @param int $min_znakova
     * @param int $max_znakova
     *
     * @throws Greska
     *
     * @return int Vrijednost
     */
    public static function Broj (string $naziv, $vrijednost, int $min_znakova = 0, int $max_znakova = 99999):int {

        $vrijednost = filter_var($vrijednost,FILTER_VALIDATE_INT);

        if ($vrijednost === false) {

            throw new Greska(sprintf(_('%s mora biti u obliku broja! (kod: %d)'), $naziv, 1), 1);

        } else if (strlen((string) $vrijednost) < $min_znakova) {


            throw new Greska(sprintf(_('%s mora biti najmanje %d znakova! (kod: %d)'), $naziv, $min_znakova, 1), 1);

        } else if (strlen((string) $vrijednost) > $max_znakova) {

            throw new Greska(sprintf(_('%s ne smije biti veće od %d znakova! (kod: %d)'), $naziv, $max_znakova, 1), 1);

        }

        return $vrijednost;

    }

    /**
     * ### Validacija slova.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $naziv
     * @param $vrijednost
     * @param int $min_znakova
     * @param int $max_znakova
     *
     * @throws Greska
     *
     * @return string Vrijednost
     */
    public static function Slova (string $naziv, $vrijednost, int $min_znakova = 0, int $max_znakova = 99999):string {

        $vrijednost = self::Trim($vrijednost);
        $vrijednost = self::StripSlashes($vrijednost);
        $vrijednost = self::HtmlSpecialChars($vrijednost);

        if (!preg_match("/^[a-zšđčćžA-ZŠĐČĆŽ]+$/i", $vrijednost)) {

            throw new Greska(sprintf(_('%s nije u ispravnom obliku! (kod: %d)'), $naziv, 1), 1);

        } else if (strlen($vrijednost) < $min_znakova) {

            throw new Greska(sprintf(_('%s mora biti najmanje %d znakova! (kod: %d)'), $naziv, $min_znakova, 1), 1);

        } else if (strlen($vrijednost) > $max_znakova) {

            throw new Greska(sprintf(_('%s ne smije biti veće od %d znakova! (kod: %d)'), $naziv, $max_znakova, 1), 1);

        }

        return $vrijednost;

    }

    /**
     * ### Validacija slova i brojeva.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $naziv
     * @param $vrijednost
     * @param int $min_znakova
     * @param int $max_znakova
     *
     * @throws Greska
     *
     * @return string Vrijednost
     */
    public static function SlovaBroj (string $naziv, $vrijednost, int $min_znakova = 0, int $max_znakova = 99999):string {

        $vrijednost = self::Trim($vrijednost);
        $vrijednost = self::StripSlashes($vrijednost);
        $vrijednost = self::HtmlSpecialChars($vrijednost);

        if (!preg_match("/^[a-zšđčćžA-ZŠĐČĆŽ0-9]+$/i", $vrijednost)) {

            throw new Greska(sprintf(_('%s nije u ispravnom obliku! (kod: %d)'), $naziv, 1), 1);

        } else if (strlen($vrijednost) < $min_znakova) {

            throw new Greska(sprintf(_('%s mora biti najmanje %d znakova! (kod: %d)'), $naziv, $min_znakova, 1), 1);

        } else if (strlen($vrijednost) > $max_znakova) {

            throw new Greska(sprintf(_('%s ne smije biti veće od %d znakova! (kod: %d)'), $naziv, $max_znakova, 1), 1);

        }

        return $vrijednost;

    }

    /**
     * ### Validacija decimalnog broja.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $naziv
     * @param $vrijednost
     *
     * @throws Greska
     *
     * @return float Vrijednost
     */
    public static function DecimalniBroj (string $naziv, $vrijednost):float {

        $vrijednost = filter_var($vrijednost, FILTER_VALIDATE_FLOAT);

        if ($vrijednost === false) {

            throw new Greska(sprintf(_('%s mora biti u obliku decimalnog broja! (kod: %d)'), $naziv, 1), 1);

        }

        return $vrijednost;

    }

    /**
     * ### Validacija true / false.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $naziv
     * @param $vrijednost
     *
     * @throws Greska
     *
     * @return bool Vrijednost
     */
    public static function Boolean (string $naziv, $vrijednost):bool {

        $vrijednost = filter_var($vrijednost, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($vrijednost === null) {

            throw new Greska(sprintf(_('%s mora biti u obliku true\false! (kod: %d)'), $naziv, 1), 1);

        }

        return $vrijednost;

    }

    /**
     * ### Validacija email adrese.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $naziv
     * @param $vrijednost
     * @param int $min_znakova
     * @param int $max_znakova
     *
     * @throws Greska
     *
     * @return string Vrijednost
     */
    public static function Email (string $naziv, $vrijednost, int $min_znakova = 0, int $max_znakova = 99999):string {

        $vrijednost = filter_var($vrijednost, FILTER_VALIDATE_EMAIL);

        if ($vrijednost === false) {

            throw new Greska(sprintf(_('%s mora biti u obliku email adrese! (kod: %d)'), $naziv, 1), 1);

        } else if (strlen($vrijednost) < $min_znakova) {

            throw new Greska(sprintf(_('%s mora biti najmanje %d znakova! (kod: %d)'), $naziv, $min_znakova, 1), 1);

        } else if (strlen($vrijednost) > $max_znakova) {

            throw new Greska(sprintf(_('%s ne smije biti veće od %d znakova! (kod: %d)'), $naziv, $max_znakova, 1), 1);

        }

        return $vrijednost;

    }

    /**
     * ### Validacija liste email adresa.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $naziv
     * @param $vrijednost
     * @param int $min_znakova
     * @param int $max_znakova
     *
     * @throws Greska
     *
     * @return string Vrijednost
     */
    public static function EmailLista (string $naziv, $vrijednost, int $min_znakova = 0, int $max_znakova = 99999):string {

        $vrijednost = preg_replace('/\s+/', '', $vrijednost);

        $lista_email = explode(',',$vrijednost);

        foreach($lista_email as $email) {

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

                throw new Greska(sprintf(_('%s mora biti u obliku email adrese! (kod: %d)'), $naziv, 1), 1);

            } else if (strlen($vrijednost) < $min_znakova) {

                throw new Greska(sprintf(_('%s mora biti najmanje %d znakova! (kod: %d)'), $naziv, $min_znakova, 1), 1);

            } else if (strlen($vrijednost) > $max_znakova) {

                throw new Greska(sprintf(_('%s ne smije biti veće od %d znakova! (kod: %d)'), $naziv, $max_znakova, 1), 1);

            }

        }

        return $vrijednost;

    }

    /**
     * ### Validacija IP adrese.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $naziv
     * @param $vrijednost
     *
     * @throws Exception
     *
     * @return string Vrijednost
     */
    public static function IP (string $naziv, $vrijednost):string {

        $vrijednost = filter_var($vrijednost, FILTER_VALIDATE_IP);

        if ($vrijednost === false) {

            throw new Greska(sprintf(_('%s mora biti u obliku IP adrese! (kod: %d)'), $naziv, 1), 1);

        }

        return $vrijednost;

    }

    /**
     * ### Validacija URL-a.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $naziv
     * @param $vrijednost
     * @param int $min_znakova
     * @param int $max_znakova
     *
     * @throws Greska
     *
     * @return string Vrijednost
     */
    public static function URL (string $naziv, $vrijednost, int $min_znakova = 0, int $max_znakova = 99999):string {

        $vrijednost = filter_var($vrijednost, FILTER_VALIDATE_URL);

        if ($vrijednost === false) {

            throw new Greska(sprintf(_('%s mora biti u obliku URL-a! (kod: %d)'), $naziv, 1), 1);

        } else if (strlen($vrijednost) < $min_znakova) {

            throw new Greska(sprintf(_('%s mora biti najmanje %d znakova! (kod: %d)'), $naziv, $min_znakova, 1), 1);

        } else if (strlen($vrijednost) > $max_znakova) {

            throw new Greska(sprintf(_('%s ne smije biti veće od %d znakova! (kod: %d)'), $naziv, $max_znakova, 1), 1);

        }

        return $vrijednost;

    }

    /**
     * ### Validacija potvrdnog polja.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $naziv
     * @param $vrijednost
     *
     * @throws Greska
     *
     * @return null|string Vrijednost
     */
    public static function Potvrda (string $naziv, $vrijednost):?string {

        if ($vrijednost != 'on' && $vrijednost != null) {

            throw new Greska(sprintf(_('%s ima pogrešnu vrijednost! (kod: %d)'), $naziv, 1), 1);

        }

        return $vrijednost;

    }

    /**
     * ### Validacija datuma.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $naziv
     * @param $vrijednost
     *
     * @throws Greska
     *
     * @return string Vrijednost
     */
    public static function Datum (string $naziv, $vrijednost):string {

        $vrijednost_array  = explode('.', $vrijednost);

        if (checkdate((int)$vrijednost_array[1], (int)$vrijednost_array[0], (int)$vrijednost_array[2]) === false) {

            throw new Greska(sprintf(_('%s mora biti u obliku datuma! (kod: %d)'), $naziv, 1), 1);

        }

        return $vrijednost;

    }

    /**
     * ### Validacija prilagođeno.
     * @since 0.1.2.pre-alpha.M1
     *
     * @param string $znakovi
     * @param string $naziv
     * @param $vrijednost
     * @param int $min_znakova
     * @param int $max_znakova
     *
     * @throws Greska
     *
     * @return string Vrijednost
     */
    public static function Prilagodjen (string $znakovi, string $naziv, $vrijednost, int $min_znakova = 0, int $max_znakova = 99999):string {

        $vrijednost = self::Trim($vrijednost);
        $vrijednost = self::StripSlashes($vrijednost);
        $vrijednost = self::HtmlSpecialChars($vrijednost);

        if (!preg_match($znakovi, $vrijednost)) {

            throw new Greska(sprintf(_('%s nije u ispravnom obliku! (kod: %d)'), $naziv, 1), 1);

        } else if (strlen($vrijednost) < $min_znakova) {

            throw new Greska(sprintf(_('%s mora biti najmanje %d znakova! (kod: %d)'), $naziv, $min_znakova, 1), 1);

        } else if (strlen($vrijednost) > $max_znakova) {

            throw new Greska(sprintf(_('%s ne smije biti veće od %d znakova! (kod: %d)'), $naziv, $max_znakova, 1), 1);

        }

        return $vrijednost;

    }

}