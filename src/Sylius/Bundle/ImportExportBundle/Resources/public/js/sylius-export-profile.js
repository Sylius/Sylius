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
        $('#sylius_export_profile_reader').handlePrototypes({
            'prototypePrefix': 'sylius_export_profile_reader_reader',
            'prototypeElementPrefix': '',
        });
        $('#sylius_export_profile_writer').handlePrototypes({
            'prototypePrefix': 'sylius_export_profile_writer_writer',
            'prototypeElementPrefix': '',
        });
    });
})( jQuery );
