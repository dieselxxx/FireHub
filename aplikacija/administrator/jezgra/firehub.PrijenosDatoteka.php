<?php declare(strict_types=1);

/**
 * Prijenos datoteka
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

use FireHub\Jezgra\Greske\Greska;

final class PrijenosDatoteka {

    private $putanja;
    private $ime_input;
    private $ime;
    private $vrsta;
    private $velicina;
    private $ekstenzija;
    private $datoteka;
    private $novo_ime;

    /**
     * Konstruktor
     */
    public function __construct (string $ime_input = "datoteka") {

        $this->ime_input = $ime_input;

        $this->novo_ime = strtolower($this->_Ime());

    }

    /**
     * Putanja za spremanje datoteke
     */
    public function Putanja (string $putanja):string {

        $postoji = realpath($putanja);

        if (!$postoji) {

            throw new Greska(_('Putanja za spremanje slike ne postoji!'));

        }

        $this->putanja = $putanja;

        return $this->putanja;

    }

    /**
     * Dozvoljene vrste datoteka
     */
    public function DozvoljeneVrste (array $vrste):void {

        if (!in_array($this->Vrsta(), $vrste)) {

            throw new Greska(_('Vrsta datoteke nije dozvoljena!'));

        }

    }

    /**
     * Dozvoljena veličina datoteka
     */
    public function DozvoljenaVelicina (int $velicina = 100):void {

        $velicina_u_kb = $velicina * 1000;

        if ($this->Velicina() > $velicina_u_kb) {

            throw new Greska(sprintf(_('Datoteka je prevelika, maksimalna veličina datoteke je %d kb !'), $velicina));

        }

    }

    /**
     * Novo ime datoteke kao random string
     */
    public function NovoIme (string $prefiks = '', bool $random = true):void {

        if ($random) {
            $novo_ime = bin2hex(random_bytes(20));
        } else {
            $novo_ime = '';
        }

        if ($prefiks <> '') {

            $this->novo_ime = $prefiks . $novo_ime . '.' . $this->Ekstenzija();

        } else {

            $this->novo_ime = $novo_ime . '.' . $this->Ekstenzija();

        }

    }

    /**
     * Čita prvo ime datoteke prije izmijena
     */
    public function PrvoImeDatoteke ():string {

        return pathinfo($this->_Ime(), PATHINFO_FILENAME);

    }

    /**
     * Čita ime datoteke
     */
    public function ImeDatoteke ():string {

        return $this->novo_ime;

    }

    /**
     * Čita primvremeno ime datoteke
     */
    public function TempImeDatoteke ():string {

        if (isset($_FILES[$this->ime_input]["tmp_name"])) {

            return $_FILES[$this->ime_input]["tmp_name"];

        }

        return '';

    }

    /**
     * Čita ektenziju datoteke
     */
    public function EkstenzijaDatoteke ():string {

        return $this->ekstenzija;

    }

    /**
     * Dozvoljena veličina datoteka
     */
    public function SlikaDimenzije (int $sirina = 100, int $visina = 100) {

        $datoteka = $this->putanja . $this->novo_ime;

        list($orginalna_sirina, $orginalna_visina) = getimagesize($datoteka);

        $tn = imagecreatetruecolor($sirina, $visina);

        // vrste datoteka
        if ($this->Vrsta() == 'image/jpeg') { // jpeg

            $slika = imagecreatefromjpeg($datoteka);

        } else if ($this->Vrsta() == 'image/gif') { // gif

            $slika = imagecreatefromgif($datoteka);

        } else if ($this->Vrsta() == 'image/png') { // png

            $slika = imagecreatefrompng($datoteka);

        } else if ($this->Vrsta() == 'image/webp') { // png

            $slika = imagecreatefromwebp($datoteka);

        } else {

            throw new Greska(_('Slika nije u podržanom formatu!'));

        }

        imagecolortransparent($tn, imagecolorallocate($tn, 0, 0, 0));
        imagealphablending($tn, false);
        imagesavealpha($tn, true);

        imagecopyresampled($tn, $slika, 0, 0, 0, 0, $sirina, $visina, $orginalna_sirina, $orginalna_visina);

        if ($this->Vrsta() == 'image/jpeg') { // jpeg

            imagejpeg($tn, $datoteka, 100);
            imagedestroy($tn);

        } else if ($this->Vrsta() == 'image/gif') { // gif

            imagegif($tn, $datoteka);
            imagedestroy($tn);

        } else if ($this->Vrsta() == 'image/png') { // png

            imagepng($tn, $datoteka,9);
            imagedestroy($tn);

        } else if ($this->Vrsta() == 'image/webp') { // webp

            imagewebp($tn, $datoteka,100);
            imagedestroy($tn);

        } else {

            throw new Greska(_('Greška prilikom spremanja slike!'));

        }

    }

    /**
     * Pročitaj ime datoteke
     */
    private function _Ime ():string {

        if (isset($_FILES[$this->ime_input]["name"])) {

            $this->ime = $_FILES[$this->ime_input]["name"];

        }

        if (!$this->ime) {

            throw new Greska(_('Molimo dodajte datoteku!'));

        }

        return $this->ime;

    }

    /**
     * Pročitaj vrstu datoteke
     */
    public function Vrsta ():string {

        if (isset($_FILES[$this->ime_input]["type"])) {

            $this->vrsta = $_FILES[$this->ime_input]["type"];

        }

        if (!$this->vrsta) {

            throw new Greska(_('Dogodila se greška prilikom učitavanja vrste datoteke!'));

        }

        return $this->vrsta;

    }

    /**
     * Pročitaj veličinu datoteke.
     */
    public function Velicina ():int {

        if (isset($_FILES[$this->ime_input]["size"])) {

            $this->velicina = $_FILES[$this->ime_input]["size"];

        }

        if (!$this->velicina) {

            throw new Greska(_('Dogodila se greška prilikom učitavanja veličine datoteke!'));

        }

        return $this->velicina;

    }

    /**
     * Pročitaj ekstenziju datoteke
     */
    public function Ekstenzija () {

        $this->ekstenzija = pathinfo($this->_Ime(), PATHINFO_EXTENSION);

        return $this->ekstenzija;

    }

    /**
     * Datoteka
     */
    private function _Datoteka ():string {

        if (isset($_FILES[$this->ime_input]["tmp_name"])) {

            $this->datoteka = $_FILES[$this->ime_input]["tmp_name"];

        }

        if (!$this->datoteka) {

            throw new Greska(_('Molimo dodajte datoteku!'));

        }

        return $this->datoteka;

    }

    /**
     * Prijenos datoteke
     */
    public function PrijenosDatoteke ():void {

        move_uploaded_file($this->_Datoteka(), $this->putanja . $this->novo_ime);

    }

}