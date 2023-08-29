/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import $ from 'jquery';

const updateSlug = function updateSlug(element) {
  const slugInput = element.parents('.content').find('[name*="[slug]"]');
  if (slugInput.attr('readonly') === 'readonly') {
    return;
  }

  const loadableParent = slugInput.parents('.field.loadable');
  const parentTaxonInput = $('#sylius_taxon_parent');

  loadableParent.addClass('loading');

  let data;
  if (parentTaxonInput.length > 0 && parentTaxonInput.val() !== '') {
    data = {
      name: element.val(),
      locale: element.closest('[data-locale]').data('locale'),
      parentCode: parentTaxonInput.val(),
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
    },
    error() {
      slugInput.val('');
    },
    complete() {
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
    $('#sylius_taxon_parent').parent().on('change', () => {
      const nameInput = $('[data-locale]').find('.content.active [name*="sylius_taxon[translations]"][name*="[name]"]');
      if (nameInput.val() === '') {
        return;
      }

      updateSlug($(nameInput));
    });

    $('.toggle-taxon-slug-modification').on('click', (event) => {
      event.preventDefault();
      toggleSlugModification($(event.currentTarget), $(event.currentTarget).siblings('input'));
    });
  },
});
