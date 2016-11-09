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

    $.fn.api.settings.api = {
        'get taxons': '/admin/api/taxons',
        'move taxon': '/admin/taxons/{id}/move?_format=json'
    };
})(jQuery);
