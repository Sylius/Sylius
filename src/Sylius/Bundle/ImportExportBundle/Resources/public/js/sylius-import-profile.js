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
        $('#sylius_import').handlePrototypes({
            'prototypePrefix': 'sylius_import_profile_exporter_exporters'
        });
    });
})( jQuery );
