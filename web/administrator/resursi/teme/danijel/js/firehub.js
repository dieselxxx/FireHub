$(document).ready(function () {

    /**
     * Navigacija status.
     *
     * @since 0.0.74 GTGwebShop alpha
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

});