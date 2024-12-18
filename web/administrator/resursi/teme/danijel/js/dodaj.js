/**
 * Animacije.
 */
Loader_Krug = '\
    <svg class="loader" version="1.1" xmlns="http://www.w3.org/2000/svg" width="65px" height="65px" viewBox="0 0 66 66">\
        <circle class="loader_putanja" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>\
    </svg>';

Potvrda_Uspjesno = '\
    <svg class="potvrda" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">\
        <circle class="putanja krug" fill="none" stroke="#4caf50" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>\
        <polyline class="putanja odabir" fill="none" stroke="#4caf50" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "/>\
    </svg>\
    ';

Potvrda_Neuspjesno = '\
    <svg class="potvrda" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">\
        <circle class="putanja krug" fill="none" stroke="#f44336" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>\
        <line class="putanja linija" fill="none" stroke="#f44336" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3"/>\
        <line class="putanja linija" fill="none" stroke="#f44336" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2"/>\
    </svg>\
    ';

/**
 * Loader na učitavanju stranice.
 *
 */
$(window).on('load', function () {

    $('main > div#loader_sadrzaj').css('visibility', 'hidden');
    $('main > *:not(div#loader_sadrzaj)').css('visibility', 'visible');

});
$_Ucitaj = function () {

    $('header nav').css('height', 'auto');
    $('header nav ul li').removeClass('glavni_meni_otvoren');
    $('main > *:not(div#loader_sadrzaj)').css('visibility', 'hidden');
    $('main > div#loader_sadrzaj').append(Loader_Krug).css('visibility', 'visible');
    $('main > svg').css('visibility', 'visible');
    $("html, body").animate({ scrollTop: 0 }, 300);

};

/**
 * Tagovi.
 */
$(function () {
    $('.tagovi').tagovi_input({
        width: 'auto'
    });
});

/**
 * Input odabir.
 */
$(function () {
    $(".input-select").chosen({
        search_contains: true,
        width: '100%'
    });
});

/**
 * Odgodi input tipkovnice.
 */
(function ($) {
    $.fn.OdgodiInput = function(callback, ms){

        let timer = 0;

        $(this).keyup(function () {

            clearTimeout (timer);
            timer = setTimeout(callback, ms);

        });

        return $(this);

    };
})(jQuery);

$(document).ready(function () {

    /**
     * Tablica model.
     */
    $('section.modul > div.opcije a.puni_zaslon').on("click", function () {
        $(this).closest('form').toggleClass('puni_zaslon');
        $(this).closest('body').toggleClass('puni_zaslon');
        $(this).find('svg').toggle();
    });

    /**
     * Datum i vrijeme.
     */
    jQuery.datetimepicker.setLocale('hr');
    jQuery('.datum_vrijeme').datetimepicker({
        timepicker:true,
        format:'d.m.Y H:i'
    });
    jQuery('.datum').datetimepicker({
        timepicker:false,
        format:'d.m.Y'
    });
    jQuery('.vrijeme').datetimepicker({
        timepicker:true,
        format:'H:i'
    });

});

/**
 * Dialog.
 */
class Dialog {

    /**
     * Konstruktor.
     */
    constructor () {

    }

    /**
     * Otvori dialog.
     *
     * @param $podigni (true = podigni dialog na vrh, false = ostaje na mjestu)
     */
    static dialogOtvori ($podigni = true) {

        $('#dialog .naslov h4, #dialog .sadrzaj, #dialog .kontrole').empty();
        if ($podigni) {
            $("html, body").animate({ scrollTop: 0 }, 300);
            $('#dialog_okolo').fadeIn(300).css('position', 'absolute');
        } else {
            $('#dialog_okolo').fadeIn(300).css('position', 'fixed');
        }
        $('#dialog').slideDown(300).css('display', 'grid');

    }

    /**
     * Zatvori dialog.
     */
    static dialogZatvori () {

        $('#dialog').slideUp(300);
        $('#dialog_okolo').fadeOut(300);
        $('#dialog .naslov h4, #dialog .sadrzaj, #dialog .kontrole').empty();

    }

    /**
     * Očisti dialog.
     */
    static dialogOcisti () {

        $('#dialog .naslov h4, #dialog .sadrzaj, #dialog .kontrole').empty();

    }

    /**
     * Naslov.
     *
     * @param $tekst
     */
    naslov ($tekst) {

        this.tekst = $tekst;

        $('#dialog .naslov h4').append(this.tekst);

    }

    /**
     * Dodaj sadržaj.
     *
     * @param $tekst
     */
    sadrzaj ($tekst) {

        this.tekst = $tekst;

        $('#dialog .sadrzaj').append(this.tekst);

    }

    /**
     * Kontrole.
     *
     * @param $tekst
     */
    kontrole ($tekst) {

        this.tekst = $tekst;

        $('#dialog .kontrole').append(this.tekst);

    }

}

/**
 * Lokana pohrana.
 */
class LokalnaPohrana {

    /**
     * Umetni podatak u lokalnu pohranu.
     *
     * @param $podatak
     * @param $vrijednost
     */
    Umetni ($podatak, $vrijednost) {

        this.podatak = $podatak;
        this.vrijednost = $vrijednost;

        localStorage.setItem(this.podatak, this.vrijednost);

    }

    /**
     * Pročitaj podatak iz lokalne pohrane.
     *
     * @param $podatak
     */
    Procitaj ($podatak) {

        this.procitaj = localStorage.getItem($podatak);

        return this.procitaj;

    }

}

/**
 * Notifikacije.
 */
document.addEventListener('DOMContentLoaded', () => {

    const applicationServerKey =
        'BMBlr6YznhYMX3NgcWIDRxZXs0sh7tCv7_YCsWcww0ZCv9WGg-tRCXfMEHTiBPCksSqeve1twlbmVAZFv7GSuj0';
    let isPushEnabled = false;

    const pushButton = document.querySelector('#push-subscription-button');
    if (!pushButton) {
        return;
    }

    pushButton.addEventListener('click', function() {
        if (isPushEnabled) {
            push_unsubscribe();
        } else {
            push_subscribe();
        }
    });

    if (!('serviceWorker' in navigator)) {
        console.warn('Service workers are not supported by this browser');
        changePushButtonState('incompatible');
        return;
    }

    if (!('PushManager' in window)) {
        console.warn('Push notifications are not supported by this browser');
        changePushButtonState('incompatible');
        return;
    }

    if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
        console.warn('Notifications are not supported by this browser');
        changePushButtonState('incompatible');
        return;
    }

    if (Notification.permission === 'denied') {
        console.warn('Notifications are denied by the user');
        changePushButtonState('incompatible');
        return;
    }

    navigator.serviceWorker.register('/imovina/js/gtgwebshop_SW.js', { scope: "/" }).then(
        () => {
            console.log('[SW] Service worker has been registered');
            push_updateSubscription();
        },
        e => {
            console.error('[SW] Service worker registration failed', e);
            changePushButtonState('incompatible');
        }
    );

    function changePushButtonState(state) {
        switch (state) {
            case 'enabled':
                pushButton.disabled = false;
                pushButton.textContent = 'Disable Push notifications';
                isPushEnabled = true;
                break;
            case 'disabled':
                pushButton.disabled = false;
                pushButton.textContent = 'Enable Push notifications';
                isPushEnabled = false;
                break;
            case 'computing':
                pushButton.disabled = true;
                pushButton.textContent = 'Loading...';
                break;
            case 'incompatible':
                pushButton.disabled = true;
                pushButton.textContent = 'Push notifications are not compatible with this browser';
                break;
            default:
                console.error('Unhandled push button state', state);
                break;
        }
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    function checkNotificationPermission() {
        return new Promise((resolve, reject) => {
            if (Notification.permission === 'denied') {
                return reject(new Error('Push messages are blocked.'));
            }

            if (Notification.permission === 'granted') {
                return resolve();
            }

            if (Notification.permission === 'default') {
                return Notification.requestPermission().then(result => {
                    if (result !== 'granted') {
                        reject(new Error('Bad permission result'));
                    } else {
                        resolve();
                    }
                });
            }

            return reject(new Error('Unknown permission'));
        });
    }

    function push_subscribe() {
        changePushButtonState('computing');

        return checkNotificationPermission()
            .then(() => navigator.serviceWorker.ready)
            .then(serviceWorkerRegistration =>
                serviceWorkerRegistration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(applicationServerKey),
                })
            )
            .then(subscription => {
                return push_sendSubscriptionToServer(subscription, 'POST');
            })
            .then(subscription => subscription && changePushButtonState('enabled')) // update your UI
            .catch(e => {
                if (Notification.permission === 'denied') {
                    console.warn('Notifications are denied by the user.');
                    changePushButtonState('incompatible');
                } else {
                    console.error('Impossible to subscribe to push notifications', e);
                    changePushButtonState('disabled');
                }
            });
    }

    function push_updateSubscription() {
        navigator.serviceWorker.ready
            .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
            .then(subscription => {
                changePushButtonState('disabled');

                if (!subscription) {
                    return;
                }

                return push_sendSubscriptionToServer(subscription, 'PUT');
            })
            .then(subscription => subscription && changePushButtonState('enabled'))
            .catch(e => {
                console.error('Error when updating the subscription', e);
            });
    }

    function push_unsubscribe() {
        changePushButtonState('computing');

        navigator.serviceWorker.ready
            .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
            .then(subscription => {
                if (!subscription) {
                    changePushButtonState('disabled');
                    return;
                }

                return push_sendSubscriptionToServer(subscription, 'DELETE');
            })
            .then(subscription => subscription.unsubscribe())
            .then(() => changePushButtonState('disabled'))
            .catch(e => {
                console.error('Error when unsubscribing the user', e);
                changePushButtonState('disabled');
            });
    }

    function push_sendSubscriptionToServer(subscription, method) {
        const key = subscription.getKey('p256dh');
        const token = subscription.getKey('auth');
        const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];

        return fetch('push_subscription.php', {
            method,
            body: JSON.stringify({
                endpoint: subscription.endpoint,
                publicKey: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
                authToken: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
                contentEncoding,
            }),
        }).then(() => subscription);
    }

    const sendPushButton = document.querySelector('#send-push-button');
    if (!sendPushButton) {
        return;
    }

    sendPushButton.addEventListener('click', () =>
        navigator.serviceWorker.ready
            .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
            .then(subscription => {
                if (!subscription) {
                    alert('Please enable push notifications');
                    return;
                }

                const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];
                const jsonSubscription = subscription.toJSON();
                fetch('/testing', {
                    method: 'POST',
                    body: JSON.stringify(Object.assign(jsonSubscription, { contentEncoding })),
                });
            })
    );

});