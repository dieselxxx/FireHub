<?php declare(strict_types = 1);

/**
 * Artikli model
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
use FireHub\Aplikacija\Administrator\Jezgra\tTablicaEfekti;

/**
 * ### Artikli
 * @since 0.1.2.pre-alpha.M1
 *
 * @package Aplikacija\Model
 */
final class Artikli_Model extends Master_Model {

    use tTablicaEfekti;

    private int $broj_stranice;
    protected string $poredaj;
    protected string $redoslijed;
    private string $pretraga;
    public int $limit_zapisa_po_stranici = 10;
    private string|int $kategorija;
    private string|int $podkategorija;

    /**
     * ### Konstruktor
     * @since 0.1.2.pre-alpha.M1
     */
    public function __construct (
        private BazaPodataka $bazaPodataka
    ) {

        parent::__construct();

    }

    public function listaGratis ():array {

        $rezultat = $this->bazaPodataka
                ->sirovi("
                SELECT
                    ID, Naziv
                FROM artikli
                ORDER BY Naziv
            ")
                ->napravi();

        return $rezultat->niz() ?: [];

    }

    /**
     * Lista kategorija.
     *
     * @param int $broj_stranice
     * @param string $poredaj
     * @param string $redoslijed
     * @param string|int $kategorija
     * @param string|int $podkategorija
     *
     * @throws Greska
     *
     * @return array
     */
    public function lista (int $broj_stranice = 1, string $poredaj = 'Naziv', string $redoslijed = 'asc', string|int $kategorija = '', string|int $podkategorija = ''):array {

        if (isset($_REQUEST['pretraga']) && $_REQUEST['pretraga'] <> '') {

            $pretraga = $_REQUEST['pretraga'];

        } else {

            $pretraga = '';

        }

        $pretraga = Validacija::String(_('Pretraga'), $pretraga, 0, 50);
        $broj_stranice = Validacija::Broj(_('Broj stranice'), $broj_stranice, 1, 10);

        $this->broj_stranice = $broj_stranice;

        $this->pretraga = $pretraga;

        $this->poredaj = $poredaj;
        $this->poredaj = Validacija::Slova(_('Poredaj rezutate'), $this->poredaj, 1);

        $this->redoslijed = $redoslijed;
        $this->redoslijed = Validacija::Slova(_('Redoslijed rezultata'), $this->redoslijed, 3, 4);
        if ($this->redoslijed <> 'asc' && $this->redoslijed <> 'desc') {throw new Greska(_('Redoslijed rezultata ima pogrešnu vrijednost'));}

        $this->kategorija = $kategorija;
        $this->podkategorija = $podkategorija;

        // potraži artikle
        $pomak = ($broj_stranice - 1) * $this->limit_zapisa_po_stranici;

        if ($kategorija !== '') {

            $rezultat = $this->bazaPodataka
                ->sirovi("
                SELECT
                    ID, Naziv, Aktivan
                FROM artikli
                WHERE ID <> 0
                AND KategorijaID = '$kategorija'
                {$this->trazi()}
                ORDER BY ".ucwords($poredaj)." $redoslijed
                LIMIT $pomak, $this->limit_zapisa_po_stranici
            ")
                ->napravi();

        } else if ($podkategorija !== '') {

            $rezultat = $this->bazaPodataka
                ->sirovi("
                SELECT
                    ID, Naziv, Aktivan
                FROM artikli
                WHERE ID <> 0
                AND PodKategorijaID = '$podkategorija'
                {$this->trazi()}
                ORDER BY ".ucwords($poredaj)." $redoslijed
                LIMIT $pomak, $this->limit_zapisa_po_stranici
            ")
                ->napravi();

        } else {

            $rezultat = $this->bazaPodataka
                ->sirovi("
                SELECT
                    ID, Naziv, Aktivan
                FROM artikli
                WHERE ID <> 0
                {$this->trazi()}
                ORDER BY ".ucwords($poredaj)." $redoslijed
                LIMIT $pomak, $this->limit_zapisa_po_stranici
            ")
                ->napravi();

        }

        return $rezultat->niz() ?: [];

    }

    /**
     * ### Traži artikl
     * @since 0.1.2.pre-alpha.M1
     *
     * @return string Upit za traženje.
     */
    private function trazi ():string {

        if (isset($_POST['pretraga'])) {

            $trazi = explode(' ', (string)$_POST['pretraga']);

            $trazi_array = '';
            foreach ($trazi as $stavka) {

                $trazi_array .= "
                    AND (
                        Naziv LIKE '%{$stavka}%'
                    )
                ";

            }

            return $trazi_array;

        }

        return '';

    }

    /**
     * Zaglavlje artikala.
     *
     * @return string
     */
    public function IspisiZaglavlje () {

        return '
            <tr>
                <th width="15%" onclick="$_Artikli(this,1,\'ID\',\''.$this->RedoslijedObrnuto().'\')">
                    <div>
                        <span>'._('ID artikla').'</span>
                        <div class="poredaj">
                            <svg class="gore '.$this->RedoslijedIkona('ID', 'desc').'"><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_gore"></use></svg>
                            <svg class="dole '.$this->RedoslijedIkona('ID', 'asc').'"><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_dole"></use></svg>
                        </div>
                    </div>
                </th>
                <th width="65%" onclick="$_Artikli(this,1,\'Naziv\',\''.$this->RedoslijedObrnuto().'\')">
                    <div>
                        <span>'._('Naziv artikla').'</span>
                        <div class="poredaj">
                            <svg class="gore '.$this->RedoslijedIkona('Naziv', 'desc').'"><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_gore"></use></svg>
                            <svg class="dole '.$this->RedoslijedIkona('Naziv', 'asc').'"><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_dole"></use></svg>
                        </div>
                    </div>
                </th>
                <th width="10%">
                    <div>
                        <span>'._('Šifre').'</span>
                    </div>
                </th>
                <th width="10%">
                    <div>
                        <span>'._('Zaliha').'</span>
                    </div>
                </th>
                <th width="10%" onclick="$_Artikli(this,1,\'Aktivan\',\''.$this->RedoslijedObrnuto().'\')">
                    <div>
                        <span>'._('Aktivno').'</span>
                        <div class="poredaj">
                            <svg class="gore '.$this->RedoslijedIkona('Aktivan', 'desc').'"><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_gore"></use></svg>
                            <svg class="dole '.$this->RedoslijedIkona('Aktivan', 'asc').'"><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_dole"></use></svg>
                        </div>
                    </div>
                </th>
            </tr>
        ';

    }

    /**
     * Navigacija.
     *
     * @return array
     */
    public function IspisiNavigaciju ():array {

        $pocetak_link_stranice = "";
        $link_stranice = "";
        $kraj_link_stranice = "";

        $ukupno_stranica = ceil($this->brojZapisa() / $this->limit_zapisa_po_stranici);
        if (($this->broj_stranice - 2) < 1) {$x = 1;} else {$x = ($this->broj_stranice - 2);}
        if (($this->broj_stranice + 2) >= $ukupno_stranica) {$y = $ukupno_stranica;} else {$y = ($this->broj_stranice + 2);}

        $artikli = '$_Artikli';

        if ($this->broj_stranice >= 2) {

            $prosla_stranica = $this->broj_stranice - 1;

            $pocetak_link_stranice .= "<li><a class='gumb mali ikona' onclick='{$artikli}(this,1,\"{$this->poredaj}\",\"{$this->redoslijed}\",\"{$this->kategorija}\",\"{$this->podkategorija}\")'><svg data-boja='boja'><use xlink:href=\"/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_lijevo_duplo\" /></svg></a></li>";
            $pocetak_link_stranice .= "<li><a class='gumb mali ikona' onclick='{$artikli}(this,$prosla_stranica,\"{$this->poredaj}\",\"{$this->redoslijed}\",\"{$this->kategorija}\",\"{$this->podkategorija}\")'><svg data-boja='boja'><use xlink:href=\"/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_lijevo\" /></svg></a></li>";

        }

        for ($i = $x; $i <= $y; $i++) {

            if ($i == $this->broj_stranice) {

                $link_stranice .= "<li><a class='gumb mali' data-boja='boja' onclick='{$artikli}(this,$i,\"{$this->poredaj}\",\"{$this->redoslijed}\",\"{$this->kategorija}\",\"{$this->podkategorija}\")'>{$i}</a></li>";

            }  else {

                $link_stranice .= "<li><a class='gumb mali' onclick='{$artikli}(this,$i,\"{$this->poredaj}\",\"{$this->redoslijed}\",\"{$this->kategorija}\",\"{$this->podkategorija}\")'>{$i}</a></li>";

            }

        }

        if ($this->broj_stranice < $ukupno_stranica) {

            $sljedeca_stranica = $this->broj_stranice + 1;

            $kraj_link_stranice .= "<li><a class='gumb mali ikona' onclick='{$artikli}(this,$sljedeca_stranica,\"{$this->poredaj}\",\"{$this->redoslijed}\",\"{$this->kategorija}\",\"{$this->podkategorija}\")'><svg data-boja='boja'><use xlink:href=\"/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno\" /></svg></a></li>";
            $kraj_link_stranice .= "<li><a class='gumb mali ikona' onclick='{$artikli}(this,$ukupno_stranica,\"{$this->poredaj}\",\"{$this->redoslijed}\",\"{$this->kategorija}\",\"{$this->podkategorija}\")'><svg data-boja='boja'><use xlink:href=\"/kapriol/resursi/grafika/simboli/simbol.ikone.svg#strelica_desno_duplo\" /></svg></a></li>";

        }

        return array('pocetak' => $pocetak_link_stranice, 'stranice' => $link_stranice, 'kraj' => $kraj_link_stranice);

    }

    /**
     * Broj zapisa.
     *
     * @return int
     */
    private function brojZapisa():int {

        if ($this->kategorija !== '') {

            $rezultat = $this->bazaPodataka
                ->sirovi("
                SELECT
                    ID
                FROM artikli
                WHERE ID <> 0
                AND KategorijaID = '$this->kategorija'
                {$this->trazi()}
            ")
                ->napravi();

        } else if ($this->podkategorija !== '') {

            $rezultat = $this->bazaPodataka
                ->sirovi("
                SELECT
                    ID
                FROM artikli
                WHERE ID <> 0
                AND PodKategorijaID = '$this->podkategorija'
                {$this->trazi()}
            ")
                ->napravi();

        } else {

            $rezultat = $this->bazaPodataka
                ->sirovi("
                SELECT
                    ID
                FROM artikli
                WHERE ID <> 0
                {$this->trazi()}
            ")
                ->napravi();

        }

        return $rezultat->broj_zapisa();

    }

}