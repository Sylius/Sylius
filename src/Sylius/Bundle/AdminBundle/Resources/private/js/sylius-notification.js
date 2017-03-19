/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function ($) {
    'use strict';

    $.fn.extend({
        notification: function () {
            var HUB_REQUEST_TIME = 'hub_request_time';
            var LAST_HUB_SYLIUS_VERSION = 'last_sylius_version';
            var SYLIUS_VERSION_DISMISSED = 'sylius_version_dismissed';
            var MILISECONDS_MULTIPLIER = 1000;

            var notificationMenu = $('#sylius-version-notification');
            var askFrequency = notificationMenu.attr('data-frequency') * MILISECONDS_MULTIPLIER;

            initializeWidget();

            if (retrieve(HUB_REQUEST_TIME) == undefined || milisecondsSinceLastRequest() > askFrequency) {
                askVersion();
            }

            $(notificationMenu).find('i[data-dismiss]').on('click', function () {
                store(SYLIUS_VERSION_DISMISSED, retrieve(LAST_HUB_SYLIUS_VERSION));

                notify(false);
            });

            function askVersion() {
                $.ajax({
                    type: "GET",
                    url: notificationMenu.attr('data-url'),
                    accept: "application/json",
                    success: function (data) {
                        if (undefined != data && data.version != retrieve(LAST_HUB_SYLIUS_VERSION)) {
                            store(LAST_HUB_SYLIUS_VERSION, data.version.toString());

                            notify(true);
                        }
                    },
                    complete: function () {
                        store(HUB_REQUEST_TIME, new Date().getTime().toString());
                    }
                });
            }

            function initializeWidget() {
                if (undefined == retrieve(LAST_HUB_SYLIUS_VERSION)) {
                    store(LAST_HUB_SYLIUS_VERSION, '0');
                }
                if (undefined == retrieve(SYLIUS_VERSION_DISMISSED)) {
                    store(SYLIUS_VERSION_DISMISSED, '0');
                }

                if (retrieve(LAST_HUB_SYLIUS_VERSION) == retrieve(SYLIUS_VERSION_DISMISSED)) {
                    notify(false);
                } else {
                    notify(true);
                }
            }

            function notify(bool) {
                var notificationMenu = $('#sylius-version-notification');

                if (true === bool) {
                    $('#notifications').css('display', 'block');
                    $('#no-notifications').css('display', 'none');
                    notificationMenu.find('.bell.icon').removeClass('outline').addClass('yellow');
                } else {
                    $('#notifications').css('display', 'none');
                    $('#no-notifications').css('display', 'block');
                    notificationMenu.find('.bell.icon').removeClass('yellow').addClass('outline');
                }
            }

            function milisecondsSinceLastRequest() {
                return new Date().getTime() - parseInt(retrieve(HUB_REQUEST_TIME));
            }

            function store(key, value) {
                localStorage.setItem(key, value);
            }
            function retrieve(key) {
                return localStorage.getItem(key);
            }
        }
    });
})(jQuery);
