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

    var methods = {
        init: function(options) {
            var settings = $.extend({
              'prototypePrefix': false
            }, options);

            return this.each(function() {
                show($(this), false);
                $(this).change(function() {
                    show($(this), true);
                });

                function show(element, replace) {
                    var id = element.attr('id');
                    var selectedValue = element.val();
                    var prototypePrefix = id;
                    if (false !== settings.prototypePrefix) {
                        prototypePrefix = settings.prototypePrefix;
                    }

                    var form = element.closest('div.control-group').parent();
                    var container = form.next();
                    var count = form.parents(':eq(1)').children().length - 1;
                    var prototype = $('#' + prototypePrefix + '_' + selectedValue)
                        .data('prototype')
                        .replace(/\[__name__\]/g, '[' + prototypePrefix + '][' + count + '][configuration]')
                        .replace(/__name__/g, count)
                    ;

                    if (replace) {
                        if (form.children().length > 1) {
                            form.children().last().remove();
                        }
                        container.html(prototype);
                    } else if (form.children().length <= 1) {
                        container.html(prototype);
                    }
                }
            });
        }
    };

    $.fn.handlePrototypes = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.handlePrototypes' );
        }
    };

    $(document).ready(function() {
        $('select[name^="sylius_promotion[rules]"][name$="[type]"]').livequery(function() {
            $(this).handlePrototypes({prototypePrefix: 'rules'});
        });
        $('select[name^="sylius_promotion[actions]"][name$="[type]"]').livequery(function() {
            $(this).handlePrototypes({prototypePrefix: 'actions'});
        });
    });
})(jQuery);
