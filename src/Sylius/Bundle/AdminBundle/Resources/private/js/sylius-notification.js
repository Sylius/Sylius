/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import $ from 'jquery';

const HUB_REQUEST_TIME = 'hub_request_time';
const LAST_SYLIUS_VERSION = 'last_sylius_version';
const SYLIUS_VERSION_DISMISSED = 'sylius_version_dismissed';
const MILISECONDS_MULTIPLIER = 1000;

const store = function store(key, value) {
  localStorage.setItem(key, value);
};

const retrieve = function retrieve(key) {
  return localStorage.getItem(key);
};

const milisecondsSinceLastRequest = function milisecondsSinceLastRequest() {
  return new Date().getTime() - parseInt(retrieve(HUB_REQUEST_TIME), 10);
};

const getDismissedSyliusVersion = function getDismissedSyliusVersion() {
  return retrieve(SYLIUS_VERSION_DISMISSED);
};

$.fn.extend({
  notification() {
    const notificationMenu = $('#sylius-version-notification');
    const askFrequency = notificationMenu.attr('data-frequency') * MILISECONDS_MULTIPLIER;

    const getCurrentSyliusVersion = function getCurrentSyliusVersion() {
      return notificationMenu.data('current-version');
    };

    const getLatestSyliusVersion = function getLatestSyliusVersion() {
      if (retrieve(HUB_REQUEST_TIME) !== undefined && milisecondsSinceLastRequest() < askFrequency) {
        return retrieve(LAST_SYLIUS_VERSION);
      }

      $.ajax({
        type: 'GET',
        url: notificationMenu.attr('data-url'),
        accept: 'application/json',
        success(response) {
          if (undefined !== response && undefined !== response.version && response.version !== retrieve(LAST_SYLIUS_VERSION)) {
            store(LAST_SYLIUS_VERSION, response.version.toString());
          }
        },
        complete() {
          store(HUB_REQUEST_TIME, new Date().getTime().toString());
        },
      });

      return retrieve(LAST_SYLIUS_VERSION);
    };

    const isLatest = function isLatest() {
      return getCurrentSyliusVersion() === getLatestSyliusVersion();
    };

    const isDismissed = function isDismissed() {
      return getLatestSyliusVersion() === getDismissedSyliusVersion();
    };

    const showNotification = function showNotification() {
      $('#notifications').css('display', 'block');
      $('#no-notifications').css('display', 'none');
      notificationMenu.find('.bell.icon').removeClass('outline').addClass('yellow');
    };

    const hideNotification = function hideNotification() {
      $('#notifications').css('display', 'none');
      $('#no-notifications').css('display', 'block');
      notificationMenu.find('.bell.icon').removeClass('yellow').addClass('outline');
    };

    const updateNotification = function updateNotification() {
      if (isLatest() || isDismissed()) {
        hideNotification();

        return;
      }

      showNotification();
    };

    $(notificationMenu).find('i[data-dismiss]').on('click', () => {
      store(SYLIUS_VERSION_DISMISSED, getLatestSyliusVersion());

      updateNotification();
    });

    updateNotification();
  },
});
