ArtikalPlusMinus = function (element, $vrsta) {

    let staraVrijednost = $(element).parent().find('input[name="vrijednost"]').val();

    let pakiranje = $(element).parent().find('input[name="vrijednost"]').data("pakiranje");

    let maxpakiranje = $(element).parent().find('input[name="vrijednost"]').data("maxpakiranje");

    let novaVrijednost;

    if ($vrsta === 'plus') {

        if (staraVrijednost >= maxpakiranje) {

            novaVrijednost = maxpakiranje;

        } else if (staraVrijednost > 0) {

            novaVrijednost = parseFloat(staraVrijednost) + pakiranje;

        } else {

            novaVrijednost = pakiranje;

        }

    } else if ($vrsta === 'minus') {

        if (staraVrijednost > pakiranje) {

            novaVrijednost = parseFloat(staraVrijednost) - pakiranje;

        } else {

            novaVrijednost = pakiranje;

        }

    }

    $(element).parent().find('input[name="vrijednost"]').val(novaVrijednost);

};

$_Cookie = function ($odgovor) {

    if ($odgovor === 'da') {

        $.ajax({
            type: 'POST',
            url: '/kolacic/gdpr',
            complete: function (odgovor) {
                $("#gdpr").remove();
            }
        });

    } else {

        window.history.back();

    }

};

$(document).ready(function () {

    $('header > a.trazi').click(function(event) {
        event.preventDefault();
        $('header > form[data-oznaka="trazi_artikal"]').slideToggle("fast");
    });

    $('select[data-oznaka="redoslijed"]').on('change', function () {

        document.location.href = $(this).val();

    });
    $('select[data-oznaka="podkategorija"]').on('change', function () {

        document.location.href = $(this).val();

    });

    $('form[data-oznaka="trazi_artikal"]').submit(function (odgovor) {

        let vrijednost = $('form[data-oznaka="trazi_artikal"] input[name="trazi"]').val();

        vrijednost = vrijednost.replace('/', ' ');

        window.location.href = '/rezultat/sve/sve/sve velicine/' + vrijednost + '/naziv/asc';

        return false;

    });

    var swiper = new Swiper(".rotator", {
        spaceBetween: 30,
        centeredSlides: true,
        autoplay: {
            delay: 8000,
            disableOnInteraction: false
        },
        pagination: {
            el: ".swiper-pagination",
            type: "progressbar",
            clickable: true
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev"
        }
    });

    $('input[name="r1"]').on('change', function() {

        let isChecked = $('input[name="r1"]').is(':checked');

        if (isChecked) {
            $('main > div#sadrzaj.narudzba > form.podatci > section.tvrtka').show();
        } else {
            $('main > div#sadrzaj.narudzba > form.podatci > section.tvrtka').hide();
        }

    });

    $('#sadrzaj.narudzba form').submit(function (odgovor) {

        let podatci = $('.narudzba form').serializeArray();

        $.ajax({
            type: 'POST',
            url: '/kosarica/naruci/',
            dataType: 'html',
            data: podatci,
            success: function (odgovor) {},
            error: function () {},
            complete: function (odgovor) {
                window.location.href = '/kosarica/ga4purchase';
            }
        });

        return false;

    });

});