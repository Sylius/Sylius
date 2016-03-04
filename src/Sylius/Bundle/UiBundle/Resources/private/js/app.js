(function($) {
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
    $('select').dropdown();

    $('.form button').on('click', function() {
      return $(this).closest('form').addClass('loading');
    });
    $('.message .close').on('click', function() {
      return $(this).closest('.message').transition('fade');
    });

    $('[data-requires-confirmation').requireConfirmation();
  });
})(jQuery);
