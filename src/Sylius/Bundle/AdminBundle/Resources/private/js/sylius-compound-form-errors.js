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
        addTabErrors: function () {
            var element = $(this);

            $('.ui.segment > .ui.tab').each(function () {
                var errors = $(this).find('.sylius-validation-error');

                if(0 !== errors.length) {
                    var tabName = $(this).attr('data-tab');
                    var tabWithErrors = $(element).find('a.item[data-tab="' + tabName + '"]');

                    var label = tabWithErrors.html();
                    var newLabel = label + '<span class="ui small horizontal circular label" style="background-color: #DB2828">' + errors.length + '</span>';

                    tabWithErrors.html(newLabel);
                }
            });
        },
        addAccordionErrors: function () {
            var element = $(this);
            var accordionElements = element.find('.ui.content');

            $(accordionElements).each(function () {
                var errors = $(this).find('.sylius-validation-error');

                if(0 !== errors.length) {
                    var ribWithErrors = $(this).closest('[data-locale]').find('.title');

                    ribWithErrors.css('color', '#DB2828');
                }
            });
        }
    })
})( jQuery );
