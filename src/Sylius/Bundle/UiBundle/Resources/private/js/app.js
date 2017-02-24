/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function($) {
  $(document).ready(function() {
    $('#sidebar').addClass('visible');
    $('#sidebar').first().sidebar('attach events', '#sidebar-toggle', 'toggle');
    $('#sidebar').first().sidebar('setting', { dimPage: false });

    $('.ui.checkbox').checkbox();
    $('.ui.accordion').accordion();
    $('.ui.menu .dropdown').dropdown({action: 'hide'});
    $('.ui.inline.dropdown').dropdown();
    $('.link.ui.dropdown').dropdown({action: 'hide'});
    $('.button.ui.dropdown').dropdown({action: 'hide'});
    $('.ui.fluid.search.selection.ui.dropdown').dropdown();
    $('.menu .item').tab();
    $('.card .image').dimmer({on: 'hover'});
    $('.ui.rating').rating('disable');

    $('form.loadable button').on('click', function() {
      return $(this).closest('form').addClass('loading');
    });
    $('.loadable.button').on('click', function() {
      return $(this).addClass('loading');
    });
    $('.message .close').on('click', function() {
      return $(this).closest('.message').transition('fade');
    });

    $('[data-requires-confirmation]').requireConfirmation();
    $('[data-toggles]').toggleElement();

    $('.special.cards .image').dimmer({
      on: 'hover'
    });

    $('[data-form-type="collection"]').CollectionForm();

  });
})(jQuery);
