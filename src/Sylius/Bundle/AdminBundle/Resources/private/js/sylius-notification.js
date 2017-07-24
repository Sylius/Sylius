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
            var LAST_SYLIUS_VERSION = 'last_sylius_version';
            var SYLIUS_VERSION_DISMISSED = 'sylius_version_dismissed';
            var MILISECONDS_MULTIPLIER = 1000;

            var notificationMenu = $('#sylius-version-notification');
            var askFrequency = notificationMenu.attr('data-frequency') * MILISECONDS_MULTIPLIER;

            $(notificationMenu).find('i[data-dismiss]').on('click', function () {
                store(SYLIUS_VERSION_DISMISSED, getLatestSyliusVersion());

                updateNotification();
            });

            updateNotification();

            function updateNotification() {
                if (isLatest() || isDismissed()) {
                    hideNotification();

                    return;
                }

                showNotification();
            }

            function showNotification()
            {
                var notificationMenu = $('#sylius-version-notification');

                $('#notifications').css('display', 'block');
                $('#no-notifications').css('display', 'none');
                notificationMenu.find('.bell.icon').removeClass('outline').addClass('yellow');
            }

            function hideNotification()
            {
                var notificationMenu = $('#sylius-version-notification');

                $('#notifications').css('display', 'none');
                $('#no-notifications').css('display', 'block');
                notificationMenu.find('.bell.icon').removeClass('yellow').addClass('outline');
            }

            function getCurrentSyliusVersion()
            {
                return notificationMenu.data('current-version');
            }

            function getLatestSyliusVersion()
            {
                if (retrieve(HUB_REQUEST_TIME) !== undefined && milisecondsSinceLastRequest() < askFrequency) {
                    return retrieve(LAST_SYLIUS_VERSION);
                }

                $.ajax({
                    type: "GET",
                    url: notificationMenu.attr('data-url'),
                    accept: "application/json",
                    success: function (data) {
                        if (undefined !== data && data.version !== retrieve(LAST_SYLIUS_VERSION)) {
                            store(LAST_SYLIUS_VERSION, data.version.toString());
                        }
                    },
                    complete: function () {
                        store(HUB_REQUEST_TIME, new Date().getTime().toString());
                    }
                });

                return retrieve(LAST_SYLIUS_VERSION);
            }

            function getDismissedSyliusVersion()
            {
                return retrieve(SYLIUS_VERSION_DISMISSED);
            }

            function isLatest()
            {
                return getCurrentSyliusVersion() === getLatestSyliusVersion();
            }

            function isDismissed()
            {
                return getLatestSyliusVersion() === getDismissedSyliusVersion();
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
