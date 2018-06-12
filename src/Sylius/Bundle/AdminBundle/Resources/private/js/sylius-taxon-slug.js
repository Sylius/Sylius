/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import $ from 'jquery';

const updateSlug = function updateSlug(element) {
  const slugInput = element.parents('.content').find('[name*="[slug]"]');
  const loadableParent = slugInput.parents('.field.loadable');

  if (slugInput.attr('readonly') === 'readonly') {
    return;
  }

  loadableParent.addClass('loading');

  let data;
  if (slugInput.attr('data-parent') != '' && slugInput.attr('data-parent') != undefined) {
    data = {
      name: element.val(),
      locale: element.closest('[data-locale]').data('locale'),
      parentId: slugInput.attr('data-parent'),
    };
  } else if ($('#sylius_taxon_parent').length > 0 && $('#sylius_taxon_parent').is(':visible') && $('#sylius_taxon_parent').val() != '') {
    data = {
      name: element.val(),
      locale: element.closest('[data-locale]').data('locale'),
      parentId: $('#sylius_taxon_parent').val(),
    };
  } else {
    data = {
      name: element.val(),
      locale: element.closest('[data-locale]').data('locale'),
    };
  }

  $.ajax({
    type: 'GET',
    url: slugInput.attr('data-url'),
    data,
    dataType: 'json',
    accept: 'application/json',
    success(response) {
      slugInput.val(response.slug);
      if (slugInput.parents('.field').hasClass('error')) {
        slugInput.parents('.field').removeClass('error');
        slugInput.parents('.field').find('.sylius-validation-error').remove();
      }
      loadableParent.removeClass('loading');
    },
  });
};

const toggleSlugModification = function toggleSlugModification(button, slugInput) {
  if (slugInput.attr('readonly')) {
    slugInput.removeAttr('readonly');
    button.html('<i class="unlock icon"></i>');
  } else {
    slugInput.attr('readonly', 'readonly');
    button.html('<i class="lock icon"></i>');
  }
};

$.fn.extend({
  taxonSlugGenerator() {
    let timeout;

    $('[name*="sylius_taxon[translations]"][name*="[name]"]').on('input', (event) => {
      clearTimeout(timeout);
      const element = $(event.currentTarget);

      timeout = setTimeout(() => {
        updateSlug(element);
      }, 1000);
    });

    $('.toggle-taxon-slug-modification').on('click', (event) => {
      event.preventDefault();
      toggleSlugModification($(event.currentTarget), $(event.currentTarget).siblings('input'));
    });
  },
});
