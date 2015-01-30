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

    $(document).ready(function() {
        $('#sylius_report_renderer').handlePrototypes({
            'prototypePrefix': 'sylius_report_renderer_renderers',
            'prototypeElementPrefix': '',
        });
        $('#sylius_report_dataFetcher').handlePrototypes({
            'prototypePrefix': 'sylius_report_dataFetcher_dataFetchers',
            'prototypeElementPrefix': '',
        });
    });
})( jQuery );