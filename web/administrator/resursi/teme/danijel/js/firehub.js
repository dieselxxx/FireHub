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