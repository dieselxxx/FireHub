$(document).ready(function () {

    /**
     * Navigacija status.
     */
    let $lokalnaPohrana = new LokalnaPohrana();
    let NavigacijaStatus = $lokalnaPohrana.Procitaj("NavigacijaStatus");

    if (NavigacijaStatus != null) {

        if (NavigacijaStatus === 'zatvoren') {

            $('body').addClass('zatvoren');

        }
    }
    $('header .navigacija_gumb').on('click', function (event) {

        if ($('body').hasClass('zatvoren')) {

            $lokalnaPohrana.Umetni('NavigacijaStatus', 'otvoren');
            $('body').removeClass('zatvoren');

        } else {

            $lokalnaPohrana.Umetni('NavigacijaStatus', 'zatvoren');
            $('body').addClass('zatvoren');

        }

    });

    /**
     * Zaglavlje profil.
     */
    $("header nav").on("click", function() {

        event.stopPropagation();

        $(this).find('ul li ul.podmeni').toggle(
            function() {
                $(this).animate({}, 500)
            }
        )

    });
    $(document).on("click", function() {

        $('header nav ul li ul.podmeni').hide(
            function() {
                $(this).animate({}, 500)
            }
        );

    });
    $('header nav ul li ul.podmeni li.treci_level').hover(

        function () {
            $(this).find('ul').show(
                function() {
                    $(this).animate({}, 500)
                }
            );
        }, function(){
            $(this).find('ul').hide(
                function() {
                    $(this).animate({}, 500)
                }
            );
        }

    );

    /**
     * Navigacija.
     */
    let url = window.location.pathname.split('/');
    if (!url[1]) {var link = '';}
    else if (!url[2]) {var link = url[1];}
    else {var link = url[1] + '/' + url[2]}
    var link = $('a[href$="' + link + '"]');
    link.closest("li").addClass("aktivan").parent("ul").parent("li").addClass("aktivan otvoren").parent("ul").parent("li").addClass("aktivan otvoren");

    $("aside nav li.podmeni > a").on("click", function() {

        $(this).removeAttr("href");
        let roditelj = $(this).parent("li");

        if (roditelj.hasClass("otvoren")) {

            roditelj.removeClass("otvoren");
            roditelj.find("li").removeClass("otvoren");
            roditelj.find("ul").slideUp(200);

        } else {

            roditelj.addClass("otvoren");
            roditelj.children("ul").slideDown(200);
            roditelj.siblings("li").children("ul").slideUp(200);
            roditelj.siblings("li").removeClass("otvoren");
            roditelj.siblings("li").find("li").removeClass("otvoren");
            roditelj.siblings("li").find("ul").slideUp(200);

        }

    });

    /**
     * Prijava.
     */
    $('form[data-oznaka="prijava"]').submit(function (odgovor) {

        odgovor.preventDefault();

        let podatci = $('form[data-oznaka="prijava"]').serializeArray();

        // dialog prozor
        let dialog = new Dialog();

        $.ajax({
            type: 'POST',
            url: '/administrator/prijava/autorizacija',
            dataType: 'json',
            data: podatci,
            beforeSend: function () {
                Dialog.dialogOtvori();
                dialog.sadrzaj(Loader_Krug);
            },
            success: function (odgovor) {
                Dialog.dialogOcisti();
                dialog.naslov('Poruka');
                dialog.sadrzaj(odgovor.Poruka);
                dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');

                if (odgovor.Validacija === 'da') {
                    //location.reload();
                    window.location.href = "/administrator/";
                }
            },
            error: function () {
                Dialog.dialogOcisti();
                dialog.naslov('Greška');
                dialog.sadrzaj('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
                dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
            },
            complete: function (odgovor) {
                //
            }
        });

        return false;

    });

});

/**
 * Spremi slike.
 */
$(function() {

    $("body").on('submit', 'form.slika', function() {

        let oznaka = $(this).data("oznaka");

        $_SpremiSlike('form[data-oznaka="' + oznaka + '"]');

        return false;

    }).on('change','form.slika input[type="file"]', function() {

        let oznaka = $(this).closest('form').data("oznaka");

        $('form[data-oznaka="' + oznaka + '"]').submit();

        return false;

    });

});
$_SpremiSlike = function ($url) {

    // dialog prozor
    let dialog = new Dialog();

    $($url).ajaxSubmit({
        beforeSend: function() {
            Dialog.dialogOtvori(false);
            dialog.naslov('Dodajem sliku');
            dialog.sadrzaj('' +
                '<div class="progres" style="display: block;">\
                    <div class="bar" style="width: 0%;"></div>\
                    <div class="postotak">0%</div>\
                </div>'
            );
        },
        uploadProgress: function(event, position, total, postotakZavrseno) {
            $('#dialog .sadrzaj .bar').width(postotakZavrseno + '%');
            $('#dialog .sadrzaj .postotak').html(postotakZavrseno + '%');
        },
        success: function(odgovor) {
            Dialog.dialogOcisti();
            dialog.naslov('Dodajem sliku');
            dialog.sadrzaj(odgovor.Poruka);
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">U redu</button>');
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.sadrzaj('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function(odgovor) {
            location.reload();
        }
    });

    return false;

};

/**
 * Odjavi se.
 */
$_Odjava = function () {

    // dialog prozor
    let dialog = new Dialog();

    $.ajax({
        type: 'POST',
        url: '/administrator/odjava',
        dataType: 'json',
        beforeSend: function () {
            Dialog.dialogOtvori(false);
            dialog.sadrzaj(Loader_Krug);
        },
        success: function (odgovor) {
            Dialog.dialogOcisti();
            dialog.naslov('Poruka');
            dialog.sadrzaj(odgovor.Poruka);
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
            location.reload();
        }
    });

};

/**
 * Dohvati artikle.
 *
 * @param {object} element
 * @param {int} $broj_stranice
 * @param {string} $poredaj
 * @param {string} $redoslijed
 * @param {string} $kategorija
 * @param {string} $podkategorija
 */
$_Artikli = function (element = '', $broj_stranice = 1, $poredaj = 'Naziv', $redoslijed = 'asc', $kategorija = '', $podkategorija = '') {

    let podatci = $('form[data-oznaka="artikli_lista"]').serializeArray();

    $.ajax({
        type: 'POST',
        url: '/administrator/artikli/lista/' + $broj_stranice + '/' + $poredaj + '/' + $redoslijed + '/' + $kategorija + '/' + $podkategorija,
        dataType: 'json',
        data: podatci,
        success: function (odgovor) {
            $('form[data-oznaka="artikli_lista"] > section table tbody').empty();
            let Artikli = odgovor.Artikli;
            $.each(Artikli, function (a, Artikal) {
                if (Artikal.Aktivan === "1") {Artikal.Aktivan = '\
                <label data-boja="boja" class="kontrolni_okvir">\
                    <input type="checkbox" disabled checked><span class="kontrolni_okvir"><span class="ukljuceno"></span></span>\
                </label>\
            ';} else {Artikal.Aktivan = '\
                <label data-boja="boja" class="kontrolni_okvir">\
                    <input type="checkbox" disabled><span class="kontrolni_okvir"><span class="ukljuceno"></span></span>\
                </label>\
            ';}
                $('form[data-oznaka="artikli_lista"] > section table tbody').append('\
                <tr>\
                    <td onclick="$_Artikl(\''+ Artikal.ID +'\')" class="uredi">'+ Artikal.ID +'</td>\
                    <td onclick="$_Artikl(\''+ Artikal.ID +'\')" class="uredi">'+ Artikal.Naziv +'</td>\
                    <td><a onclick="$_ArtiklSifre(\''+ Artikal.ID +'\')">Uredi šifre</a></td>\
                    <td><a onclick="$_ArtiklZaliha(\''+ Artikal.ID +'\')">Uredi zalihu</a></td>\
                    <td onclick="$_Artikl(\''+ Artikal.ID +'\')" class="uredi">'+ Artikal.Aktivan +'</td>\
                </tr>\
            ');
            });
            // zaglavlje
            let Zaglavlje = odgovor.Zaglavlje;
            $('form[data-oznaka="artikli_lista"] > section div.sadrzaj > table thead').empty().append(Zaglavlje);
            // navigacija
            let Navigacija = odgovor.Navigacija;
            $('form[data-oznaka="artikli_lista"] > section div.kontrole').empty().append('<ul class="navigacija">' + Navigacija.pocetak + '' + Navigacija.stranice + '' + Navigacija.kraj + '</ul>');
        },
        error: function () {
        }
    });

    return false;

};

/**
 * Uredi artikl.
 *
 * @param {int} $id
 */
$_Artikl = function ($id) {

    // dialog prozor
    let dialog = new Dialog();

    $.ajax({
        type: 'GET',
        url: '/administrator/artikli/uredi/' + $id,
        dataType: 'html',
        context: this,
        beforeSend: function () {
            Dialog.dialogOtvori(true);
            dialog.sadrzaj(Loader_Krug);
        },
        success: function (odgovor) {
            Dialog.dialogOcisti();
            dialog.naslov('Artikl: ' + $id);
            dialog.sadrzaj(odgovor);
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
            dialog.kontrole('<button type="button" class="ikona" onclick="$_ArtiklSpremi(this, \'forma\');"><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#spremi"></use></svg><span>Spremi</span></button>');
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
            $(function () {
                $('.tagovi').tagovi_input({
                    width: 'auto'
                });
                $(".input-select").chosen({
                    search_contains: true,
                    width: '100%'
                });
            });
        }
    });

    return false;

};

/**
 * Novi artikl.
 */
$_NoviArtikl = function (element) {

    // dialog prozor
    let dialog = new Dialog();

    $.ajax({
        type: 'GET',
        url: '/administrator/artikli/novi/',
        dataType: 'html',
        context: this,
        beforeSend: function () {
            Dialog.dialogOtvori(true);
            dialog.sadrzaj(Loader_Krug);
        },
        success: function (odgovor) {
            Dialog.dialogOcisti();
            dialog.naslov('Novi artikl');
            dialog.sadrzaj(odgovor);
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
            dialog.kontrole('<button type="button" class="ikona" onclick="$_ArtiklSpremi(this, \'forma\');"><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#spremi"></use></svg><span>Spremi</span></button>');
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
            $(function () {
                $('.tagovi').tagovi_input({
                    width: 'auto'
                });
                $(".input-select").chosen({
                    search_contains: true,
                    width: '100%'
                });
            });
        }
    });

    return false;

};

/**
 * Uredi zalihu artikla.
 *
 * @param {int} $id
 */
$_ArtiklZaliha = function ($id) {

    // dialog prozor
    let dialog = new Dialog();

    $.ajax({
        type: 'GET',
        url: '/administrator/artikli/uredizalihu/' + $id,
        dataType: 'html',
        context: this,
        beforeSend: function () {
            Dialog.dialogOtvori(true);
            dialog.sadrzaj(Loader_Krug);
        },
        success: function (odgovor) {
            Dialog.dialogOcisti();
            dialog.naslov('Artikl: ' + $id);
            dialog.sadrzaj(odgovor);
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
            dialog.kontrole('<button type="button" class="ikona" onclick="$_ArtiklZalihaSpremi(this, \'forma\');"><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#spremi"></use></svg><span>Spremi</span></button>');
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
        }
    });

    return false;

};

/**
 * Spremi artikl.
 */
$_ArtiklSpremi = function (element) {

    // dialog prozor
    let dialog = new Dialog();

    let artikl_forma = $('form[data-oznaka="artikl"]');

    let $id = artikl_forma.data("sifra");

    let $podatci = artikl_forma.serializeArray();

    $.ajax({
        type: 'POST',
        url: '/administrator/artikli/spremi/' + $id,
        dataType: 'json',
        data: $podatci,
        beforeSend: function () {
            $(element).closest('form').find('table tr.poruka td').empty();
        },
        success: function (odgovor) {
            if (odgovor.Validacija === "da") {

                Dialog.dialogOcisti();
                dialog.naslov('Uspješno spremljeno');
                dialog.sadrzaj('Postavke artikla su spremljene!');
                dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');

            } else {
                $(element).closest('form').find('table tr.poruka td').append(odgovor.Poruka);
            }
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
            $_Artikli();
        }
    });

    return false;

};

/**
 * Spremi zalihu artikla.
 */
$_ArtiklZalihaSpremi = function (element) {

    // dialog prozor
    let dialog = new Dialog();

    let artikl_forma = $('form[data-oznaka="artiklzaliha"]');

    let $id = artikl_forma.data("sifra");

    let $podatci = artikl_forma.serializeArray();

    $.ajax({
        type: 'POST',
        url: '/administrator/artikli/zalihaspremi/' + $id,
        dataType: 'json',
        data: $podatci,
        beforeSend: function () {
            $(element).closest('form').find('table tr.poruka td').empty();
        },
        success: function (odgovor) {
            if (odgovor.Validacija === "da") {

                Dialog.dialogOcisti();
                dialog.naslov('Uspješno spremljeno');
                dialog.sadrzaj('Postavke artikla su spremljene!');
                dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');

            } else {
                $(element).closest('form').find('table tr.poruka td').append(odgovor.Poruka);
            }
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
        }
    });

    return false;

};

/**
 * Uredi šifre artikla.
 *
 * @param {int} $id
 */
$_ArtiklSifre = function ($id) {

    // dialog prozor
    let dialog = new Dialog();

    $.ajax({
        type: 'GET',
        url: '/administrator/artikli/artiklsifre/' + $id,
        dataType: 'html',
        context: this,
        beforeSend: function () {
            Dialog.dialogOtvori(true);
            dialog.sadrzaj(Loader_Krug);
        },
        success: function (odgovor) {
            Dialog.dialogOcisti();
            dialog.naslov('Artikl: ' + $id);
            dialog.sadrzaj(odgovor);
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
            dialog.kontrole('<button type="button" class="ikona" onclick="$_ArtiklSifreNova(this, \'forma\');"><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#artikl"></use></svg><span>Nova šifra</span></button>');
            dialog.kontrole('<button type="button" class="ikona" onclick="$_ArtiklSifreSpremi(this, \'forma\');"><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#spremi"></use></svg><span>Spremi</span></button>');
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
        }
    });

    return false;

};

/**
 * Nova šifre artikla.
 */
$_ArtiklSifreNova = function (element) {

    $(element).closest('form').find('table tbody').append('\
        <tr>\
            <td>\
                <label data-boja="boja" class="unos">\
                    <input type="text" name="zaliha[]" value="" maxlength="50" autocomplete="off">\
                    <span class="naslov">\
                        <span>Šifra</span>\
                    </span>\
                    <span class="granica"></span>\
                    <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#cijena"></use></svg>\
                    <span class="upozorenje"></span>\
                </label>\
            </td>\
            <td>\
                <label data-boja="boja" class="unos">\
                    <input type="text" name="velicina[]" value="" maxlength="50" autocomplete="off">\
                    <span class="naslov">\
                        <span>Veličina</span>\
                    </span>\
                    <span class="granica"></span>\
                    <svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#cijena"></use></svg>\
                    <span class="upozorenje"></span>\
                </label>\
            </td>\
            <td>\
                <button type="button" class="ikona" onclick="$_ArtiklSifreIzbrisi(this, 0, 0)"><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#izbrisi"></use></svg><span></span></button>\
            </td>\
        </tr>\
        ');

    return false;

};

/**
 * Izbriši šifru artikla.
 *
 * @param {object} element
 * @package {int} $id
 */
$_ArtiklSifreIzbrisi = function (element, $id, $artikl) {

    if ($id == 0) {

        $(element).closest('tr').remove();

    } else {

        $.ajax({
            type: 'POST',
            url: '/administrator/artikli/artiklsifreizbrisi/' + $id,
            dataType: 'json',
            beforeSend: function () {
                $(element).closest('form').find('table tr.poruka td').empty();
            },
            success: function (odgovor) {
                if (odgovor.Validacija === "da") {
                } else {
                    $(element).closest('form').find('table tr.poruka td').append(odgovor.Poruka);
                }
            },
            error: function () {
                Dialog.dialogOcisti();
                dialog.naslov('Greška');
                dialog.sadrzaj('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
                dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
            },
            complete: function (odgovor) {
                $_ArtiklSifre($artikl);
            }
        });

    }

    return false;

};

/**
 * Spremi šifre artikla.
 */
$_ArtiklSifreSpremi = function (element) {

    // dialog prozor
    let dialog = new Dialog();

    let artikl_forma = $('form[data-oznaka="artiklsifre"]');

    let $id = artikl_forma.data("sifra");

    let $podatci = artikl_forma.serializeArray();

    $.ajax({
        type: 'POST',
        url: '/administrator/artikli/artiklsifrespremi/' + $id,
        dataType: 'json',
        data: $podatci,
        beforeSend: function () {
            $(element).closest('form').find('table tr.poruka td').empty();
        },
        success: function (odgovor) {
            if (odgovor.Validacija === "da") {

                Dialog.dialogOcisti();
                dialog.naslov('Uspješno spremljeno');
                dialog.sadrzaj('Postavke artikla su spremljene!');
                dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');

            } else {
                $(element).closest('form').find('table tr.poruka td').append(odgovor.Poruka);
            }
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
        }
    });

    return false;

};

/**
 * Dohvati obavijesti.
 *
 * @param {object} element
 * @param {int} $broj_stranice
 * @param {string} $poredaj
 * @param {string} $redoslijed
 */
$_Obavijesti = function (element = '', $broj_stranice = 1, $poredaj = 'Obavijest', $redoslijed = 'asc') {

    let podatci = $('form[data-oznaka="obavijesti_lista"]').serializeArray();

    $.ajax({
        type: 'POST',
        url: '/administrator/obavijesti/lista/' + $broj_stranice + '/' + $poredaj + '/' + $redoslijed,
        dataType: 'json',
        data: podatci,
        success: function (odgovor) {
            $('form[data-oznaka="obavijesti_lista"] > section table tbody').empty();
            let Obavijesti = odgovor.Obavijesti;
            $.each(Obavijesti, function (a, Obavijest) {
                if (Obavijest.Aktivan === "1") {Obavijest.Aktivan = '\
                    <label data-boja="boja" class="kontrolni_okvir">\
                        <input type="checkbox" disabled checked><span class="kontrolni_okvir"><span class="ukljuceno"></span></span>\
                    </label>\
                ';} else {Obavijest.Aktivan = '\
                    <label data-boja="boja" class="kontrolni_okvir">\
                        <input type="checkbox" disabled><span class="kontrolni_okvir"><span class="ukljuceno"></span></span>\
                    </label>\
                ';}
                $('form[data-oznaka="obavijesti_lista"] > section table tbody').append('\
                    <tr onclick="$_Obavijest(\''+ Obavijest.ID +'\')">\
                        <td class="uredi">'+ Obavijest.ID +'</td>\
                        <td class="uredi">'+ Obavijest.Obavijest +'</td>\
                        <td class="uredi"><img src="/slika/baner/'+ Obavijest.Obavijest +'" /></td>\
                        <td class="uredi">'+ Obavijest.Redoslijed +'</td>\
                    </tr>\
                ');
            });
            // zaglavlje
            let Zaglavlje = odgovor.Zaglavlje;
            $('form[data-oznaka="obavijesti_lista"] > section div.sadrzaj > table thead').empty().append(Zaglavlje);
            // navigacija
            let Navigacija = odgovor.Navigacija;
            $('form[data-oznaka="obavijesti_lista"] > section div.kontrole').empty().append('<ul class="navigacija">' + Navigacija.pocetak + '' + Navigacija.stranice + '' + Navigacija.kraj + '</ul>');
        },
        error: function () {
        }
    });

    return false;

};

/**
 * Uredi obavijest.
 *
 * @param {int} $id
 */
$_Obavijest = function ($id) {

    // dialog prozor
    let dialog = new Dialog();

    $.ajax({
        type: 'GET',
        url: '/administrator/obavijesti/uredi/' + $id,
        dataType: 'html',
        context: this,
        beforeSend: function () {
            Dialog.dialogOtvori(true);
            dialog.sadrzaj(Loader_Krug);
        },
        success: function (odgovor) {
            Dialog.dialogOcisti();
            dialog.naslov('Obavijest: ' + $id);
            dialog.sadrzaj(odgovor);
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
            dialog.kontrole('<button type="button" class="ikona" onclick="$_ObavijestSpremi(this, \'forma\');"><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#spremi"></use></svg><span>Spremi</span></button>');
            dialog.kontrole('<button type="button" class="ikona" onclick="$_ObavijestIzbrisi(this,  \'forma\');"><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#izbrisi"></use></svg><span>Izbrisi</span></button>');
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
            $(function () {
                $('.tagovi').tagovi_input({
                    width: 'auto'
                });
                $(".input-select").chosen({
                    search_contains: true,
                    width: '100%'
                });
            });
        }
    });

    return false;

};

/**
 * Spremi obavijest.
 */
$_ObavijestSpremi = function (element) {

    // dialog prozor
    let dialog = new Dialog();

    let obavijest_forma = $('form[data-oznaka="obavijest"]');

    let $id = obavijest_forma.data("sifra");

    let $podatci = obavijest_forma.serializeArray();

    $.ajax({
        type: 'POST',
        url: '/administrator/obavijesti/spremi/' + $id,
        dataType: 'json',
        data: $podatci,
        beforeSend: function () {
            $(element).closest('form').find('table tr.poruka td').empty();
        },
        success: function (odgovor) {
            if (odgovor.Validacija === "da") {

                Dialog.dialogOcisti();
                dialog.naslov('Uspješno spremljeno');
                dialog.sadrzaj('Postavke obavijesti su spremljene!');
                dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');

            } else {
                $(element).closest('form').find('table tr.poruka td').append(odgovor.Poruka);
            }
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
            $_Obavijesti();
        }
    });

    return false;

};

/**
 * Izbriši obavijest.
 */
$_ObavijestIzbrisi = function (element) {

    // dialog prozor
    let dialog = new Dialog();

    let obavijest_forma = $('form[data-oznaka="obavijest"]');

    let $id = obavijest_forma.data("sifra");

    $.ajax({
        type: 'POST',
        url: '/administrator/obavijesti/izbrisi/' + $id,
        dataType: 'json',
        beforeSend: function () {
            $(element).closest('form').find('table tr.poruka td').empty();
        },
        success: function (odgovor) {
            if (odgovor.Validacija === "da") {

                Dialog.dialogOcisti();
                dialog.naslov('Uspješno izbrisano');
                dialog.sadrzaj('Obavijest je izbrisana!');
                dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');

            } else {
                $(element).closest('form').find('table tr.poruka td').append(odgovor.Poruka);
            }
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.sadrzaj('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
            $_Obavijesti();
        }
    });

    return false;

};

/**
 * Dohvati obavijesti.
 *
 * @param {object} element
 * @param {int} $broj_stranice
 * @param {string} $poredaj
 * @param {string} $redoslijed
 */
$_ObavijestiDno = function (element = '', $broj_stranice = 1, $poredaj = 'Obavijest', $redoslijed = 'asc') {

    let podatci = $('form[data-oznaka="obavijestidno_lista"]').serializeArray();

    $.ajax({
        type: 'POST',
        url: '/administrator/obavijestidno/lista/' + $broj_stranice + '/' + $poredaj + '/' + $redoslijed,
        dataType: 'json',
        data: podatci,
        success: function (odgovor) {
            $('form[data-oznaka="obavijestidno_lista"] > section table tbody').empty();
            let ObavijestiDno = odgovor.ObavijestiDno;
            $.each(ObavijestiDno, function (a, ObavijestDno) {
                if (ObavijestDno.Aktivan === "1") {ObavijestDno.Aktivan = '\
                    <label data-boja="boja" class="kontrolni_okvir">\
                        <input type="checkbox" disabled checked><span class="kontrolni_okvir"><span class="ukljuceno"></span></span>\
                    </label>\
                ';} else {ObavijestDno.Aktivan = '\
                    <label data-boja="boja" class="kontrolni_okvir">\
                        <input type="checkbox" disabled><span class="kontrolni_okvir"><span class="ukljuceno"></span></span>\
                    </label>\
                ';}
                $('form[data-oznaka="obavijestidno_lista"] > section table tbody').append('\
                    <tr onclick="$_ObavijestDno(\''+ ObavijestDno.ID +'\')">\
                        <td class="uredi">'+ ObavijestDno.ID +'</td>\
                        <td class="uredi">'+ ObavijestDno.Obavijest +'</td>\
                        <td class="uredi"><img src="/slika/banerdno/'+ ObavijestDno.Obavijest +'" /></td>\
                        <td class="uredi">'+ ObavijestDno.Redoslijed +'</td>\
                    </tr>\
                ');
            });
            // zaglavlje
            let Zaglavlje = odgovor.Zaglavlje;
            $('form[data-oznaka="obavijestidno_lista"] > section div.sadrzaj > table thead').empty().append(Zaglavlje);
            // navigacija
            let Navigacija = odgovor.Navigacija;
            $('form[data-oznaka="obavijestidno_lista"] > section div.kontrole').empty().append('<ul class="navigacija">' + Navigacija.pocetak + '' + Navigacija.stranice + '' + Navigacija.kraj + '</ul>');
        },
        error: function () {
        }
    });

    return false;

};

/**
 * Uredi obavijest.
 *
 * @param {int} $id
 */
$_ObavijestDno = function ($id) {

    // dialog prozor
    let dialog = new Dialog();

    $.ajax({
        type: 'GET',
        url: '/administrator/obavijestidno/uredi/' + $id,
        dataType: 'html',
        context: this,
        beforeSend: function () {
            Dialog.dialogOtvori(true);
            dialog.sadrzaj(Loader_Krug);
        },
        success: function (odgovor) {
            Dialog.dialogOcisti();
            dialog.naslov('Obavijest: ' + $id);
            dialog.sadrzaj(odgovor);
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
            dialog.kontrole('<button type="button" class="ikona" onclick="$_ObavijestDnoSpremi(this, \'forma\');"><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#spremi"></use></svg><span>Spremi</span></button>');
            dialog.kontrole('<button type="button" class="ikona" onclick="$_ObavijestDnoIzbrisi(this,  \'forma\');"><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#izbrisi"></use></svg><span>Izbrisi</span></button>');
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
            $(function () {
                $('.tagovi').tagovi_input({
                    width: 'auto'
                });
                $(".input-select").chosen({
                    search_contains: true,
                    width: '100%'
                });
            });
        }
    });

    return false;

};

/**
 * Spremi obavijest.
 */
$_ObavijestDnoSpremi = function (element) {

    // dialog prozor
    let dialog = new Dialog();

    let obavijestdno_forma = $('form[data-oznaka="obavijestdno"]');

    let $id = obavijestdno_forma.data("sifra");

    let $podatci = obavijestdno_forma.serializeArray();

    $.ajax({
        type: 'POST',
        url: '/administrator/obavijestidno/spremi/' + $id,
        dataType: 'json',
        data: $podatci,
        beforeSend: function () {
            $(element).closest('form').find('table tr.poruka td').empty();
        },
        success: function (odgovor) {
            if (odgovor.Validacija === "da") {

                Dialog.dialogOcisti();
                dialog.naslov('Uspješno spremljeno');
                dialog.sadrzaj('Postavke obavijesti su spremljene!');
                dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');

            } else {
                $(element).closest('form').find('table tr.poruka td').append(odgovor.Poruka);
            }
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
            $_ObavijestiDno();
        }
    });

    return false;

};

/**
 * Izbriši obavijest.
 */
$_ObavijestDnoIzbrisi = function (element) {

    // dialog prozor
    let dialog = new Dialog();

    let obavijestdno_forma = $('form[data-oznaka="obavijestdno"]');

    let $id = obavijestdno_forma.data("sifra");

    $.ajax({
        type: 'POST',
        url: '/administrator/obavijestidno/izbrisi/' + $id,
        dataType: 'json',
        beforeSend: function () {
            $(element).closest('form').find('table tr.poruka td').empty();
        },
        success: function (odgovor) {
            if (odgovor.Validacija === "da") {

                Dialog.dialogOcisti();
                dialog.naslov('Uspješno izbrisano');
                dialog.sadrzaj('Obavijest je izbrisana!');
                dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');

            } else {
                $(element).closest('form').find('table tr.poruka td').append(odgovor.Poruka);
            }
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.sadrzaj('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
            $_ObavijestiDno();
        }
    });

    return false;

};

/**
 * Dohvati kategorije.
 *
 * @param {object} element
 * @param {int} $broj_stranice
 * @param {string} $poredaj
 * @param {string} $redoslijed
 */
$_Kategorije = function (element = '', $broj_stranice = 1, $poredaj = 'Kategorija', $redoslijed = 'asc') {

    let podatci = $('form[data-oznaka="kategorije_lista"]').serializeArray();

    $.ajax({
        type: 'POST',
        url: '/administrator/kategorije/lista/' + $broj_stranice + '/' + $poredaj + '/' + $redoslijed,
        dataType: 'json',
        data: podatci,
        success: function (odgovor) {
            $('form[data-oznaka="kategorije_lista"] > section table tbody').empty();
            let Kategorije = odgovor.Kategorije;
            $.each(Kategorije, function (a, Kategorija) {
                if (Kategorija.CalcVelicina === "1") {Kategorija.CalcVelicina = '\
                    <label data-boja="boja" class="kontrolni_okvir">\
                        <input type="checkbox" disabled checked><span class="kontrolni_okvir"><span class="ukljuceno"></span></span>\
                    </label>\
                ';} else {Kategorija.CalcVelicina = '\
                    <label data-boja="boja" class="kontrolni_okvir">\
                        <input type="checkbox" disabled><span class="kontrolni_okvir"><span class="ukljuceno"></span></span>\
                    </label>\
                ';}
                $('form[data-oznaka="kategorije_lista"] > section table tbody').append('\
                    <tr onclick="$_Kategorija(\''+ Kategorija.ID +'\')">\
                        <td class="uredi">'+ Kategorija.ID +'</td>\
                        <td class="uredi">'+ Kategorija.Kategorija +'</td>\
                        <td class="uredi">'+ Kategorija.CalcVelicina +'</td>\
                        <td><a href="/administrator/artikli/'+ Kategorija.ID +'">Artikli</a></td>\
                    </tr>\
                ');
            });
            // zaglavlje
            let Zaglavlje = odgovor.Zaglavlje;
            $('form[data-oznaka="kategorije_lista"] > section div.sadrzaj > table thead').empty().append(Zaglavlje);
            // navigacija
            let Navigacija = odgovor.Navigacija;
            $('form[data-oznaka="kategorije_lista"] > section div.kontrole').empty().append('<ul class="navigacija">' + Navigacija.pocetak + '' + Navigacija.stranice + '' + Navigacija.kraj + '</ul>');
        },
        error: function () {
        }
    });

    return false;

};

/**
 * Uredi kategoriju.
 *
 * @param {int} $id
 */
$_Kategorija = function ($id) {

    // dialog prozor
    let dialog = new Dialog();

    $.ajax({
        type: 'GET',
        url: '/administrator/kategorije/uredi/' + $id,
        dataType: 'html',
        context: this,
        beforeSend: function () {
            Dialog.dialogOtvori(true);
            dialog.sadrzaj(Loader_Krug);
        },
        success: function (odgovor) {
            Dialog.dialogOcisti();
            dialog.naslov('Kategorija: ' + $id);
            dialog.sadrzaj(odgovor);
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
            dialog.kontrole('<button type="button" class="ikona" onclick="$_KategorijaIzbrisi(this, \'forma\');"><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#izbrisi"></use></svg><span>Izbriši</span></button>');
            dialog.kontrole('<button type="button" class="ikona" onclick="$_KategorijaSpremi(this, \'forma\');"><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#spremi"></use></svg><span>Spremi</span></button>');
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        }
    });

    return false;

};

/**
 * Spremi kategoriju.
 */
$_KategorijaSpremi = function (element) {

    // dialog prozor
    let dialog = new Dialog();

    let kategorija_forma = $('form[data-oznaka="kategorija"]');

    let $id = kategorija_forma.data("sifra");

    let $podatci = kategorija_forma.serializeArray();

    $.ajax({
        type: 'POST',
        url: '/administrator/kategorije/spremi/' + $id,
        dataType: 'json',
        data: $podatci,
        beforeSend: function () {
            $(element).closest('form').find('table tr.poruka td').empty();
        },
        success: function (odgovor) {
            if (odgovor.Validacija === "da") {

                Dialog.dialogOcisti();
                dialog.naslov('Uspješno spremljeno');
                dialog.sadrzaj('Postavke kategorije su spremljene!');
                dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');

            } else {
                $(element).closest('form').find('table tr.poruka td').append(odgovor.Poruka);
            }
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
            $_Kategorije();
        }
    });

    return false;

};

/**
 * Nova kategorija.
 */
$_KategorijaNova = function (element) {

    // dialog prozor
    let dialog = new Dialog();

    $.ajax({
        type: 'GET',
        url: '/administrator/kategorije/nova/',
        dataType: 'html',
        context: this,
        beforeSend: function () {
            Dialog.dialogOtvori(true);
            dialog.sadrzaj(Loader_Krug);
        },
        success: function (odgovor) {
            Dialog.dialogOcisti();
            dialog.naslov('Nova kategorija');
            dialog.sadrzaj(odgovor);
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
            dialog.kontrole('<button type="button" class="ikona" onclick="$_KategorijaSpremi(this, \'forma\');"><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#spremi"></use></svg><span>Spremi</span></button>');
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
            $_Kategorije();
        }
    });

    return false;

};

/**
 * Izbrisi kategoriju.
 */
$_KategorijaIzbrisi = function (element) {

    // dialog prozor
    let dialog = new Dialog();

    let kategorija_forma = $('form[data-oznaka="kategorija"]');

    let $id = kategorija_forma.data("sifra");

    let $podatci = kategorija_forma.serializeArray();

    $.ajax({
        type: 'POST',
        url: '/administrator/kategorije/izbrisi/' + $id,
        dataType: 'json',
        data: $podatci,
        beforeSend: function () {
            $(element).closest('form').find('table tr.poruka td').empty();
        },
        success: function (odgovor) {
            if (odgovor.Validacija === "da") {

                Dialog.dialogOcisti();
                dialog.naslov('Uspješno izbrisano');
                dialog.sadrzaj('Kategorija je izbrisana!');
                dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');

            } else {
                $(element).closest('form').find('table tr.poruka td').append(odgovor.Poruka);
            }
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.sadrzaj('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
            $_Kategorije();
        }
    });

    return false;

};

/**
 * Dohvati podkategorije.
 *
 * @param {object} element
 * @param {int} $broj_stranice
 * @param {string} $poredaj
 * @param {string} $redoslijed
 */
$_PodKategorije = function (element = '', $broj_stranice = 1, $poredaj = 'PodKategorija', $redoslijed = 'asc') {

    let podatci = $('form[data-oznaka="podkategorije_lista"]').serializeArray();

    $.ajax({
        type: 'POST',
        url: '/administrator/podkategorije/lista/' + $broj_stranice + '/' + $poredaj + '/' + $redoslijed,
        dataType: 'json',
        data: podatci,
        success: function (odgovor) {
            $('form[data-oznaka="podkategorije_lista"] > section table tbody').empty();
            let PodKategorije = odgovor.PodKategorije;
            $.each(PodKategorije, function (a, PodKategorija) {
                if (PodKategorija.CalcVelicina === "1") {PodKategorija.CalcVelicina = '\
                    <label data-boja="boja" class="kontrolni_okvir">\
                        <input type="checkbox" disabled checked><span class="kontrolni_okvir"><span class="ukljuceno"></span></span>\
                    </label>\
                ';} else {PodKategorija.CalcVelicina = '\
                    <label data-boja="boja" class="kontrolni_okvir">\
                        <input type="checkbox" disabled><span class="kontrolni_okvir"><span class="ukljuceno"></span></span>\
                    </label>\
                ';}
                $('form[data-oznaka="podkategorije_lista"] > section table tbody').append('\
                    <tr onclick="$_PodKategorija(\''+ PodKategorija.ID +'\')">\
                        <td class="uredi">'+ PodKategorija.ID +'</td>\
                        <td class="uredi">'+ PodKategorija.Podkategorija +'</td>\
                        <td class="uredi">'+ PodKategorija.Kategorija +'</td>\
                        <td><a href="/administrator/artikli//'+ PodKategorija.ID +'">Artikli</a></td>\
                    </tr>\
                ');
            });
            // zaglavlje
            let Zaglavlje = odgovor.Zaglavlje;
            $('form[data-oznaka="podkategorije_lista"] > section div.sadrzaj > table thead').empty().append(Zaglavlje);
            // navigacija
            let Navigacija = odgovor.Navigacija;
            $('form[data-oznaka="podkategorije_lista"] > section div.kontrole').empty().append('<ul class="navigacija">' + Navigacija.pocetak + '' + Navigacija.stranice + '' + Navigacija.kraj + '</ul>');
        },
        error: function () {
        }
    });

    return false;

};

/**
 * Uredi podkategoriju.
 *
 * @param {int} $id
 */
$_PodKategorija = function ($id) {

    // dialog prozor
    let dialog = new Dialog();

    $.ajax({
        type: 'GET',
        url: '/administrator/podkategorije/uredi/' + $id,
        dataType: 'html',
        context: this,
        beforeSend: function () {
            Dialog.dialogOtvori(true);
            dialog.sadrzaj(Loader_Krug);
        },
        success: function (odgovor) {
            Dialog.dialogOcisti();
            dialog.naslov('PodKategorija: ' + $id);
            dialog.sadrzaj(odgovor);
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
            dialog.kontrole('<button type="button" class="ikona" onclick="$_PodKategorijaIzbrisi(this, \'forma\');"><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#izbrisi"></use></svg><span>Izbriši</span></button>');
            dialog.kontrole('<button type="button" class="ikona" onclick="$_PodKategorijaSpremi(this, \'forma\');"><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#spremi"></use></svg><span>Spremi</span></button>');
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        }
    });

    return false;

};

/**
 * Spremi podkategoriju.
 */
$_PodKategorijaSpremi = function (element) {

    // dialog prozor
    let dialog = new Dialog();

    let podkategorija_forma = $('form[data-oznaka="podkategorija"]');

    let $id = podkategorija_forma.data("sifra");

    let $podatci = podkategorija_forma.serializeArray();

    $.ajax({
        type: 'POST',
        url: '/administrator/podkategorije/spremi/' + $id,
        dataType: 'json',
        data: $podatci,
        beforeSend: function () {
            $(element).closest('form').find('table tr.poruka td').empty();
        },
        success: function (odgovor) {
            if (odgovor.Validacija === "da") {

                Dialog.dialogOcisti();
                dialog.naslov('Uspješno spremljeno');
                dialog.sadrzaj('Postavke podkategorije su spremljene!');
                dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');

            } else {
                $(element).closest('form').find('table tr.poruka td').append(odgovor.Poruka);
            }
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
            $_PodKategorije();
        }
    });

    return false;

};

/**
 * Nova podkategorija.
 */
$_PodKategorijaNova = function (element) {

    // dialog prozor
    let dialog = new Dialog();

    $.ajax({
        type: 'GET',
        url: '/administrator/podkategorije/nova/',
        dataType: 'html',
        context: this,
        beforeSend: function () {
            Dialog.dialogOtvori(true);
            dialog.sadrzaj(Loader_Krug);
        },
        success: function (odgovor) {
            Dialog.dialogOcisti();
            dialog.naslov('Nova podkategorija');
            dialog.sadrzaj(odgovor);
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
            dialog.kontrole('<button type="button" class="ikona" onclick="$_PodKategorijaSpremi(this, \'forma\');"><svg><use xlink:href="/kapriol/resursi/grafika/simboli/simbol.ikone.svg#spremi"></use></svg><span>Spremi</span></button>');
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
            $_PodKategorije();
        }
    });

    return false;

};

/**
 * Izbrisi podkategoriju.
 */
$_PodKategorijaIzbrisi = function (element) {

    // dialog prozor
    let dialog = new Dialog();

    let podkategorija_forma = $('form[data-oznaka="podkategorija"]');

    let $id = podkategorija_forma.data("sifra");

    let $podatci = podkategorija_forma.serializeArray();

    $.ajax({
        type: 'POST',
        url: '/administrator/podkategorije/izbrisi/' + $id,
        dataType: 'json',
        data: $podatci,
        beforeSend: function () {
            $(element).closest('form').find('table tr.poruka td').empty();
        },
        success: function (odgovor) {
            if (odgovor.Validacija === "da") {

                Dialog.dialogOcisti();
                dialog.naslov('Uspješno izbrisano');
                dialog.sadrzaj('Podkategorija je izbrisana!');
                dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');

            } else {
                $(element).closest('form').find('table tr.poruka td').append(odgovor.Poruka);
            }
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.sadrzaj('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
            $_PodKategorije();
        }
    });

    return false;

};

/**
 * Spremi sliku artikla.
 */
$(function() {

    $("body").on('submit', 'form[data-oznaka="artikl"]', function() {

        let oznaka = $(this).data("oznaka");

        $_ArtiklSpremiSliku('form[data-oznaka="' + oznaka + '"]');

        return false;

    }).on('change','form[data-oznaka="artikl"] input[type="file"]', function() {

        let oznaka = $(this).closest('form').data("oznaka");

        $('form[data-oznaka="' + oznaka + '"]').submit();

        return false;

    });

});
$_ArtiklSpremiSliku = function ($url) {

    // dialog prozor
    let dialog = new Dialog();

    $($url).ajaxSubmit({
        beforeSend: function() {
            Dialog.dialogOtvori(false);
            dialog.naslov('Dodajem sliku');
            dialog.sadrzaj('' +
                '<div class="progres" style="display: block;">\
                    <div class="bar" style="width: 0%;"></div>\
                    <div class="postotak">0%</div>\
                </div>'
            );
        },
        uploadProgress: function(event, position, total, postotakZavrseno) {
            $('#dialog .sadrzaj .bar').width(postotakZavrseno + '%');
            $('#dialog .sadrzaj .postotak').html(postotakZavrseno + '%');
        },
        success: function(odgovor) {
            Dialog.dialogOcisti();
            dialog.naslov('Dodajem sliku');
            dialog.sadrzaj(odgovor.Poruka);
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">U redu</button>');
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function(odgovor) {
            $_Artikl($id);
        }
    });

    return false;

};
$_ArtiklIzbrisiSliku = function ($slika) {

    let artikl_forma = $('form[data-oznaka="artikl"]');

    let $id = artikl_forma.data("sifra");

    let $podatci = artikl_forma.serializeArray();

    $.ajax({
        type: 'POST',
        url: '/administrator/artikli/izbrisisliku/' + $slika,
        dataType: 'json',
        data: $podatci,
        beforeSend: function () {
            //
        },
        success: function (odgovor) {
            if (odgovor.Validacija === "da") {

            } else {
                //$(element).closest('form').find('table tr.poruka td').append(odgovor.Poruka);
            }
        },
        error: function () {
        },
        complete: function (odgovor) {
            $_Artikl($id);
        }
    });

    return false;

};

$_ArtiklIzbrisiCijenu = function ($slika) {

    let artikl_forma = $('form[data-oznaka="artikl"]');

    let $id = artikl_forma.data("sifra");

    let $podatci = artikl_forma.serializeArray();

    $.ajax({
        type: 'POST',
        url: '/administrator/artikli/izbrisicijenu/' + $slika,
        dataType: 'json',
        data: $podatci,
        beforeSend: function () {
            //
        },
        success: function (odgovor) {
            if (odgovor.Validacija === "da") {

            } else {
                //$(element).closest('form').find('table tr.poruka td').append(odgovor.Poruka);
            }
        },
        error: function () {
        },
        complete: function (odgovor) {
            $_Artikl($id);
        }
    });

    return false;

};

/**
 * Spremi sliku kateogrije.
 */
$(function() {

    $("body").on('submit', 'form[data-oznaka="kategorija"]', function() {

        let oznaka = $(this).data("oznaka");

        $_KategorijaSpremiSliku('form[data-oznaka="' + oznaka + '"]');

        return false;

    }).on('change','form[data-oznaka="kategorija"] input[type="file"]', function() {

        let oznaka = $(this).closest('form').data("oznaka");

        $('form[data-oznaka="' + oznaka + '"]').submit();

        return false;

    });

});
$_KategorijaSpremiSliku = function ($url) {

    // dialog prozor
    let dialog = new Dialog();

    $($url).ajaxSubmit({
        beforeSend: function() {
            Dialog.dialogOtvori(false);
            dialog.naslov('Dodajem sliku');
            dialog.sadrzaj('' +
                '<div class="progres" style="display: block;">\
                    <div class="bar" style="width: 0%;"></div>\
                    <div class="postotak">0%</div>\
                </div>'
            );
        },
        uploadProgress: function(event, position, total, postotakZavrseno) {
            $('#dialog .sadrzaj .bar').width(postotakZavrseno + '%');
            $('#dialog .sadrzaj .postotak').html(postotakZavrseno + '%');
        },
        success: function(odgovor) {
            Dialog.dialogOcisti();
            dialog.naslov('Dodajem sliku');
            dialog.sadrzaj(odgovor.Poruka);
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">U redu</button>');
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function(odgovor) {
            $_Kategorija($id);
        }
    });

    return false;

};

/**
 * Spremi sliku podkateogrije.
 */
$(function() {

    $("body").on('submit', 'form[data-oznaka="podkategorija"]', function() {

        let oznaka = $(this).data("oznaka");

        $_PodKategorijaSpremiSliku('form[data-oznaka="' + oznaka + '"]');

        return false;

    }).on('change','form[data-oznaka="podkategorija"] input[type="file"]', function() {

        let oznaka = $(this).closest('form').data("oznaka");

        $('form[data-oznaka="' + oznaka + '"]').submit();

        return false;

    });

});
$_PodKategorijaSpremiSliku = function ($url) {

    // dialog prozor
    let dialog = new Dialog();

    $($url).ajaxSubmit({
        beforeSend: function() {
            Dialog.dialogOtvori(false);
            dialog.naslov('Dodajem sliku');
            dialog.sadrzaj('' +
                '<div class="progres" style="display: block;">\
                    <div class="bar" style="width: 0%;"></div>\
                    <div class="postotak">0%</div>\
                </div>'
            );
        },
        uploadProgress: function(event, position, total, postotakZavrseno) {
            $('#dialog .sadrzaj .bar').width(postotakZavrseno + '%');
            $('#dialog .sadrzaj .postotak').html(postotakZavrseno + '%');
        },
        success: function(odgovor) {
            Dialog.dialogOcisti();
            dialog.naslov('Dodajem sliku');
            dialog.sadrzaj(odgovor.Poruka);
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">U redu</button>');
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function(odgovor) {
            $_PodKategorija($id);
        }
    });

    return false;

};

/**
 * Spremi reklamu.
 */
$_ReklamaSpremi = function (element) {

    // dialog prozor
    let dialog = new Dialog();

    let reklama_forma = $(element).closest('form');

    let $id = reklama_forma.data("id");

    let $podatci = reklama_forma.serializeArray();

    $.ajax({
        type: 'POST',
        url: '/administrator/reklame/spremi/' + $id,
        dataType: 'json',
        data: $podatci,
        beforeSend: function () {
            $(element).closest('form').find('table tr.poruka td').empty();
        },
        success: function (odgovor) {
            if (odgovor.Validacija === "da") {
                Dialog.dialogOcisti();
                Dialog.dialogOtvori();
                dialog.naslov('Uspješno spremljeno');
                dialog.sadrzaj('Postavke reklame su spremljene!');
                dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');

            } else {
                $(element).closest('form').find('table tr.poruka td').append(odgovor.Poruka);
            }
        },
        error: function () {
            Dialog.dialogOcisti();
            dialog.naslov('Greška');
            dialog.naslov('Dogodila se greška prilikom učitavanja podataka, molimo kontaktirajte administratora');
            dialog.kontrole('<button data-boja="boja" onclick="Dialog.dialogZatvori()">Zatvori</button>');
        },
        complete: function (odgovor) {
        }
    });

    return false;

};