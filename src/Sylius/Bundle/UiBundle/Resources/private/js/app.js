(function($) {
  $.fn.extend({
    toggleElement: function() {
      return this.each(function() {
        $(this).on('change', function(event) {
          event.preventDefault();

          var toggleElement = $(this);
          var targetElement = $('#' + toggleElement.data('toggles'));

          if (toggleElement.is(':checked')) {
            targetElement.show();
          } else {
            targetElement.hide();
          }
        });

        return $(this).trigger('change');
      });
    }
  });

  $.fn.extend({
    requireConfirmation: function() {
      return this.each(function() {
        return $(this).on('click', function(event) {
          event.preventDefault();

          var actionButton = $(this);

          if (actionButton.is('a')) {
            $('#confirmation-button').attr('href', actionButton.attr('href'));
          }
          if (actionButton.is('button')) {
            $('#confirmation-button').on('click', function(event) {
              event.preventDefault();

              return actionButton.closest('form').submit();
            });
          }

          return $('#confirmation-modal').modal('show');
        });
      });
    }
  });

  $(document).ready(function() {
    $('#sidebar')
        .first()
        .sidebar('attach events', '#sidebar-toggle', 'show')
    ;

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
    $('.cart.button')
        .popup({
            popup: $('.cart.popup'),
            on: 'click',
        })
    ;

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
  });
})(jQuery);
