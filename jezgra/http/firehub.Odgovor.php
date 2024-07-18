<?php declare(strict_types = 1);

/**
 * Datoteka za HTTP odgovor
 *
 * Ova datoteka prikuplja i obrađuje sve informacije o HTTP odgovoru.
 * @since 0.2.6.pre-alpha.M2
 *
 * @author Danijel Galić
 * @copyright 2022 FireHub Web Application Framework
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Sustav\HTTP
 */

namespace FireHub\Jezgra\HTTP;

use FireHub\Jezgra\Odgovor as Odgovor_Interface;
use FireHub\Jezgra\Komponente\Datoteka\Datoteka;
use FireHub\Jezgra\HTTP\Enumeratori\StatusKod as HTTP_StatusKod;
use FireHub\Jezgra\HTTP\Enumeratori\Vrsta as HTTP_Vrsta;
use FireHub\Jezgra\HTTP\Enumeratori\Predmemorija as HTTP_Predmemorija;
use FireHub\Jezgra\Komponente\Log\Log;
use FireHub\Jezgra\Komponente\Log\Servisi\AutoPosalji;
use FireHub\Jezgra\Kontejner\Greske\Kontejner_Greska;
use FireHub\Jezgra\Komponente\Datoteka\Greske\Datoteka_Greska;
use FireHub\Jezgra\Sadrzaj\Sadrzaj;
use FireHub\Jezgra\Komponente\Slika\Servisi\Slika_Servis;
use Throwable;

/**
 * ### HTTP odgovor
 *
 * Klasa namjenjana za upravljenje i obrađivanje svih
 * HTTP odgovora od sustava i aplikacije.
 * @since 0.2.6.pre-alpha.M2
 *
 * @package Sustav\HTTP
 */
final class Odgovor implements Odgovor_Interface {

    /**
     * ### Konstruktor
     * @since 0.2.6.pre-alpha.M2
     *
     * @param Datoteka $datoteka <p>
     * Trenutna radna datoteka.
     * </p>
     * @param HTTP_StatusKod $kod <p>
     * Status kod HTTP odgovora.
     * </p>
     * @param HTTP_Vrsta $vrsta <p>
     * Media tip vrsta HTTP odgovora.
     * </p>
     * @param string $karakteri <p>
     * Standard enkodiranje karaktera.
     * </p>
     * @param string $jezik <p>
     * Skraćeni naziv jezika.
     * </p>
     * @param HTTP_Predmemorija[] $predmemorija <p>
     * Lista naredbi za predmemoriju.
     * https://www.iana.org/assignments/language-subtag-registry/language-subtag-registry.
     * </p>
     * @param int $predmemorija_vrijeme <p>
     * Maksimalno vrijeme trajanja predmemorije.
     * </p>
     * @param Sadrzaj|Slika_Servis $sadrzaj <p>
     * Sadržaj HTTP odgovora.
     * </p>
     *
     * @throws Datoteka_Greska Ukoliko se ne može pročitati naziv datoteke.
     * @throws Kontejner_Greska Ukoliko se može spremiti instanca Log-a.
     */
    public function __construct (
        public readonly Datoteka $datoteka,
        public readonly HTTP_StatusKod $kod,
        public readonly HTTP_Vrsta $vrsta,
        public readonly string $karakteri,
        public readonly string $jezik ,
        public readonly array $predmemorija,
        public readonly int $predmemorija_vrijeme,
        private Sadrzaj|Slika_Servis $sadrzaj
    ) {

        // postavi HTTP zaglavlja
        $this
            ->postaviStatus()
            ->postaviVrstu()
            ->postaviJezik()
            ->postaviPredmemoriju()
            ->poweredBy();

    }

    /**
     * {@inheritDoc}
     *
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Log-a.
     */
    public function sadrzaj ():string {

        ob_start();

        try {

            return $this->sadrzaj->ispisi();

        } catch (Throwable $objekt) {

            (new Log)->servis(AutoPosalji::class)->greska($objekt)->napravi()->posalji();

            return '';

        }

    }

    /**
     * ### Status HTTP odgovora
     *
     * Status kodova HTTP odgovora odlučuju da li je
     * HTTP zahtjev uspješno obrađen.
     * @since 0.2.6.pre-alpha.M2
     *
     * @return $this Trenutni objekt.
     */
    private function postaviStatus ():self {

        header('HTTP/1.1 ' . $this->kod->value . ' ' . $this->kod->statusNaziv());

        return $this;

    }

    /**
     * ### Vrsta HTTP odgovora
     *
     * Naznačuje media tip resursa.
     * @since 0.2.6.pre-alpha.M2
     *
     * @return $this Trenutni objekt.
     */
    private function postaviVrstu ():self {

        header('Content-Type: ' . $this->vrsta->value . '; charset=' . $this->karakteri);
        header('X-Content-Type-Options: nosniff');

        return $this;

    }

    /**
     * ### Dodaj potrebni HTTP jezik
     *
     * Namijenjena publika za sadržaj.
     * @since 0.2.6.pre-alpha.M2
     *
     * @return $this Trenutni objekt.
     */
    private function postaviJezik ():self {

        header('Content-language: ' . $this->jezik);

        return $this;

    }

    /**
     * ### Dodaj potrebnu HTTP predmemoriju
     * @since 0.2.6.pre-alpha.M2
     *
     * @throws Datoteka_Greska Ukoliko se ne može pročitati naziv datoteke.
     * @throws Kontejner_Greska Ukoliko se može spremiti instanca Log-a.
     *
     * @return $this Trenutni objekt.
     */
    private function postaviPredmemoriju ():self {

        $datoteka = ($this->datoteka)->napravi();

        // izvlačenje naziva enuma predmemorije
        $lista_predmemorije = array_map(
            function($predmemorija):string {
                return $predmemorija->value;
            },
            $this->predmemorija
        );

        header('Cache-Control: ' . implode(',', $lista_predmemorije) . ', max-age=' . $this->predmemorija_vrijeme);
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $datoteka->zadnjeIzmijenjen()) . ' GMT'); // info o vremenu izmjene datoteke
        header('Etag: ' . $datoteka->eTag()); // md5 hash za provjeru izmjene datoteke

        if ($datoteka->eTagZaglavlje() === $datoteka->eTag() || strtotime($datoteka->izmijenjenOd()) === $datoteka->zadnjeIzmijenjen()) {

            header('HTTP/1.1 ' . HTTP_StatusKod::HTTP_NOT_MODIFIED->value . ' ' . HTTP_StatusKod::HTTP_NOT_MODIFIED->statusNaziv());

        }

        return $this;

    }

    /**
     * ### Zaglavlje X-Powered-By
     *
     * Označava sustav na kojem radi trenutni aplikacija.
     * @since 0.2.6.pre-alpha.M2
     *
     * @return $this Trenutni objekt.
     */
    private function poweredBy ():self {

        return $this;

    }

}
