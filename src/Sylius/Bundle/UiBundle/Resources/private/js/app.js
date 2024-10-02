/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import $ from 'jquery';

import './sylius-check-all';

$(document).ready(() => {
  $('[data-js-bulk-checkboxes]').checkAll();
});
