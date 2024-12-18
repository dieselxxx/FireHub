<?php declare(strict_types = 1);

/**
 * Server
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

final class Server {

    private static $agent;
    private static $datoteka;
    private static $gateway;
    private static $host;
    private static $https;
    private static $javna_ip;
    private static $modificiran;
    private static $port;
    private static $preporucio;
    private static $program;
    private static $protokol;
    private static $provjera;
    private static $putaja;
    private static $root;
    private static $server;
    private static $uri;
    private static $utf8uri;
    private static $xhost;
    private static $http_autorizacija;
    private static $provjeri_metodu;

    /**
     * Prikupi podataka posjetielja.
     */
    public static function Agent ():string {

        if (isset($_SERVER['HTTP_USER_AGENT'])) {

            self::$agent = $_SERVER['HTTP_USER_AGENT'];

        }

        if (!empty(self::$agent)) {

            return self::$agent;

        } else {

            return "";

        }

    }

    /**
     * Vraća naziv trenutne datoteke.
     */
    public static function Datoteka ():?string {

        self::$datoteka = $_SERVER['SCRIPT_NAME'];

        if (!empty(self::$datoteka)) {

            $datoteka = strtolower(self::$datoteka);

            return $datoteka;

        } else {

            return null;

        }

    }

    /**
     * Vraća naziv domene.
     */
    public static function Domena ():?string {

        $port = ((!self::HTTPS() && self::Port() == 80) || (self::HTTPS() && self::Port() == 443)) ? '' : ':'.self::Port();

        $domena = (self::xHost()) ? self::xHost() : (self::Host() ? self::Host() : null);
        $domena = isset($domena) ? $domena : self::NazivServera() . $port;

        return $domena;

    }

    /**
     * Provjerava dali je trenutni pretraživač IE ili ne.
     */
    public static function IE ():bool {

        if (
            preg_match('~MSIE|Internet Explorer~i', self::Agent()) || (strpos(self::Agent(), 'Trident/7.0') !== false
                && strpos(self::Agent(), 'rv:11.0') !== false)
        ) {

            return true;

        }

        return false;

    }

    /**
     * Preuzmi gateway interface.
     */
    public static function Gateway ():?string {

        self::$gateway = $_SERVER['GATEWAY_INTERFACE'];

        if (!empty(self::$gateway)) {

            return self::$gateway;

        } else {

            return null;

        }

    }

    /**
     * Preuzmi naziv host-a.
     */
    public static function Host ():?string {

        self::$host = $_SERVER['HTTP_HOST'];

        if (!empty(self::$host)) {

            $host = strtolower(self::$host);

            return $host;

        } else {

            return null;

        }

    }

    /**
     * Provjeri dali je HTTP ili HTTPS.
     */
    public static function HTTPS ():bool {

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {

            return true;

        } else {

            return false;

        }

    }

    /**
     * Preuzmi IP adresu.
     */
    public static function IP ():?string {

        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) { // iza CloudFlare mreže

            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];

        }

        if (array_key_exists("HTTP_CLIENT_IP",$_SERVER)) {$klijent = @$_SERVER['HTTP_CLIENT_IP'];} else {$klijent = '';};
        if (array_key_exists("HTTP_X_FORWARDED_FOR",$_SERVER)) {$preusmjereno = @$_SERVER['HTTP_X_FORWARDED_FOR'];} else {$preusmjereno = '';};
        $server = $_SERVER['REMOTE_ADDR'];

        if ($klijent)  {

            $klijent = Validacija::IP('IP adresa klijenta', $klijent);
            $ip = $klijent;
        }
        else if ($preusmjereno && $preusmjereno <> "unknown") {

            $preusmjereno = Validacija::IP('IP adresa klijenta', $preusmjereno);
            $ip = $preusmjereno;

        }
        else {

            $ip = $server;

        }

        self::$javna_ip = $ip;

        if (!empty(self::$javna_ip)) {

            return self::$javna_ip;

        } else {

            return null;

        }

    }

    /**
     * Provjeri kada je datoteka zadnji put modificirana.
     */
    public static function Modificiran ():?string {

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {

            self::$modificiran = $_SERVER['HTTP_IF_MODIFIED_SINCE'];

            return self::$modificiran;

        } else {

            return null;

        }

    }

    /**
     * Preuzmi broj porta.
     */
    public static function Port ():?int {

        self::$port = $_SERVER['SERVER_PORT'];

        if (!empty(self::$port)) {

            return (int)self::$port;

        } else {

            return null;

        }

    }

    /**
     * Prikupi stranicu sa koje je posjetitelj stigao.
     */
    public static function Preporucio ():?string {

        self::$preporucio = $_SERVER['HTTP_REFERER'];

        if (!empty(self::$preporucio)) {

            return self::$preporucio;

        } else {

            return null;

        }

    }

    /**
     * Vraća naziv programa.
     */
    public static function Program ():?string {

        self::$program = $_SERVER['SERVER_SOFTWARE'];

        if (!empty(self::$program)) {

            return self::$program;

        } else {

            return null;

        }

    }

    /**
     * Provjeri verziju protokola.
     */
    public static function Protokol ():?string {

        self::$protokol = $_SERVER['SERVER_PROTOCOL'];

        if (!empty(self::$protokol)) {

            $protokol = strtolower(self::$protokol);

            $protokol = substr($protokol, 0, strpos($protokol, '/')).((self::HTTPS()) ? 's' : '' );

            return $protokol;

        } else {

            return null;

        }

    }

    /**
     * Provjeri dali je promjenjena datoteka.
     */
    public static function Provjera ():?string {

        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {

            self::$provjera = $_SERVER['HTTP_IF_NONE_MATCH'];

            return self::$provjera;

        } else {

            return null;

        }

    }

    /**
     * Preuzmi punu putanju datoteke.
     */
    public static function Putanja ():?string {

        self::$putaja = $_SERVER['SCRIPT_FILENAME'];

        if (!empty(self::$putaja)) {

            return self::$putaja;

        } else {

            return null;

        }

    }

    /**
     * Vraća naziv početne mape.
     */
    public static function Root ():?string {

        self::$root = $_SERVER['DOCUMENT_ROOT'];

        if (!empty(self::$root)) {

            return self::$root;

        } else {

            return null;

        }

    }

    /**
     * Preuzmi verziju protokola.
     */
    public static function NazivServera ():?string {

        self::$server = $_SERVER['SERVER_NAME'];

        if (!empty(self::$server)) {

            return self::$server;

        } else {

            return null;

        }

    }

    /**
     * Vraća puni naziv URI.
     */
    public static function URI ():?string {

        self::$uri = $_SERVER['REQUEST_URI'];

        if (!empty(self::$uri)) {

            $uri = strtolower(self::$uri);

            return $uri;

        } else {

            return null;

        }

    }

    /**
     * Vraća puni naziv URI, uz posebne znakove.
     */
    public static function UTF8URI ():?string {

        self::$utf8uri = $_SERVER['UNENCODED_URL'];

        if (!empty(self::$utf8uri)) {

            $utf8uri = strtolower(self::$utf8uri);

            return $utf8uri;

        } else {

            return null;

        }

    }

    /**
     * Vraća absolutni URL.
     */
    public static function URL ():?string {

        return self::Protokol().'://'.self::Domena();

    }

    /**
     * Preuzmi HTTP ukoliko je domena iza proxy-a ili load banance-ra
     */
    public static function xHost ():?string {

        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {

            self::$xhost = $_SERVER['HTTP_X_FORWARDED_HOST'];

        }

        if (!empty(self::$xhost)) {

            $xhost = strtolower(self::$xhost);

            return $xhost;

        } else {

            return null;

        }

    }

    /**
     * Preuzmi HTTP autorizaciju
     */
    public static function HTTP_Autorizacija ():?string {

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {

            self::$http_autorizacija = $_SERVER['HTTP_AUTHORIZATION'];

        }

        if (!empty(self::$http_autorizacija)) {

            return self::$http_autorizacija;

        } else {

            return null;

        }

    }

    /**
     * Provjeri HTTP metodu
     */
    public static function ProvjeriMetodu ():string {

        self::$provjeri_metodu = $_SERVER['REQUEST_METHOD'];

        return self::$provjeri_metodu;

    }

}