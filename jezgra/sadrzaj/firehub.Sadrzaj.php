<?php declare(strict_types = 1);

/**
 * Datoteka za ispis sadržaja
 * @since 0.4.4.pre-alpha.M4
 *
 * @author Danijel Galić
 * @copyright 2022 FireHub Web Application Framework
 * @license GNU General Public License version 3 - [https://opensource.org/licenses/GPL-3.0](https://opensource.org/licenses/GPL-3.0)
 *
 * @version 1.0
 * @package Sustav\Sadrzaj
 */

namespace FireHub\Jezgra\Sadrzaj;

use FireHub\Jezgra\Kontejner\Greske\Kontejner_Greska;
use FireHub\Jezgra\Sadrzaj\Enumeratori\Vrsta;
use FireHub\Jezgra\Sadrzaj\Vrste\HTML;
use FireHub\Jezgra\Sadrzaj\Vrste\JSON;
use FireHub\Jezgra\Komponente\Log\Enumeratori\Level;
use FireHub\Jezgra\Sadrzaj\Greske\Sadrzaj_Greska;
use Throwable;

/**
 * ### Klasa za ispis sadržaja
 * @since 0.4.4.pre-alpha.M4
 *
 * @package Sustav\Sadrzaj
 */
final class Sadrzaj {

    /**
     * ### Vrsta formata sadržaja
     * @var Vrsta
     */
    private Vrsta $format = Vrsta::HTML;

    /**
     * ### Datoteka sa sadržajem
     * @var string
     */
    private string $datoteka = '';

    /**
     * ### Lista podataka koje treba prenijeti u sadržaj
     * @var array<string, string|int>
     */
    private array $podatci = [];

    /**
     * ### Format sadržaja koji se ispisuje na stranicu
     * @since 0.4.4.pre-alpha.M4
     *
     * @param Vrsta $naziv <p>
     * Format sadržaja za prikaz.
     * </p>
     *
     * @return $this Instanca Sadrzaj-a.
     */
    public function format (Vrsta $naziv):self {

        $this->format = $naziv;

        return $this;

    }

    /**
     * ### Datoteka sa sadržajem
     * @since 0.4.4.pre-alpha.M4
     *
     * @param string $naziv <p>
     * Naziv datoteke sa sadržajem.
     * Putanja datoteke počima u mapi sadržaja aplikacije.
     * </p>
     *
     * @return $this Instanca Sadrzaj-a.
     */
    public function datoteka (string $naziv):self {

        $this->datoteka = $naziv;

        return $this;

    }

    /**
     * ### Podatci koje treba prenijeti u sadržaj
     * @since 0.4.4.pre-alpha.M4
     *
     * @param array<string, string|int> $podatci <p>
     * Lista podataka koje treba prenijeti u sadržaj.
     * </p>
     *
     * @return $this Instanca Sadrzaj-a.
     */
    public function podatci (array $podatci):self {

        $this->podatci = $podatci;

        return $this;

    }

    /**
     * ### Ispiši sadržaj
     * @since 0.4.4.pre-alpha.M4
     *
     * @throws Sadrzaj_Greska Ukoliko stranica ima nezovoljen format ili se ne može učitati datoteka.
     * @throws Kontejner_Greska Ukoliko se ne može spremiti instanca Log-a.
     *
     * @return string Sadržaj.
     */
    public function ispisi ():string {

        try {

            // potvrdi validnost formata
            if (!Vrsta::tryFrom($this->format->value)) {

                zapisnik(Level::KRITICNO, sprintf(_('Stranica: %s ima nedozvoljen format %s!'), $this->datoteka, $this->format->value));
                throw new Sadrzaj_Greska(_('Ne mogu pokrenuti sustav, obratite se administratoru.'));

            }

            // ispiši sadržaj u ovisnosti o odabranoj vrsti sadržaja
            return match ($this->format) {
                Vrsta::HTML => (new HTML($this->podatci, $this->datoteka))->ispisi(),
                Vrsta::JSON => (new JSON($this->podatci))->ispisi()
            };

        } catch (Throwable) {

            zapisnik(Level::KRITICNO, sprintf(_('Ne mogu učitati sadržaj za datoteku: %s u formatu: %s!'), $this->datoteka, $this->format->value));
            throw new Sadrzaj_Greska(_('Ne mogu pokrenuti sustav, obratite se administratoru.'));

        }

    }

}