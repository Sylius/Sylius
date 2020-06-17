import $ from 'jquery';

$.fn.extend({
  searchable(searchInputSelector) {
    const searchInput = $(searchInputSelector);
    const menu = $(this);

    searchInput.on('input', function(e) {
      const phrase = searchInput.val();
      const regex = new RegExp(phrase.replace(' ', '.*'), 'i');

      const foundItems = menu.find('.item')
        .hide()
        // either filter by header if it has one or by text
        .filter(function() {
          const item = $(this);
          const header = item.find('.header');

          if (header.length !== 0) {
            return regex.test(header.first().text())
          }

          return regex.test($(this).text());
        })
        .show()
      ;

      // show children if any
      foundItems.find('.item').show();

      // show parents if any
      foundItems.parentsUntil(menu, '.item').show();
    });
  }
});
