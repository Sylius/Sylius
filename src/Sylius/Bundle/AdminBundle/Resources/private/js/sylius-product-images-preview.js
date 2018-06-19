/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import $ from 'jquery';

const displayUploadedImage = function displayUploadedImage(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();

    reader.onload = (event) => {
      const image = $(input).parent().siblings('.image');

      if (image.length > 0) {
        image.attr('src', event.target.result);
      } else {
        const img = $('<img class="ui small bordered image"/>');
        img.attr('src', event.target.result);
        $(input).parent().before(img);
      }
    };

    reader.readAsDataURL(input.files[0]);
  }
};

$.fn.extend({
  previewUploadedImage(root) {
    $(`${root} input[type="file"]`).each((idx, el) => {
      $(el).change((event) => {
        displayUploadedImage(event.currentTarget);
      });
    });

    $(`${root} [data-form-collection="add"]`).on('click', (evt) => {
      const self = $(evt.currentTarget);

      setTimeout(() => {
        self.parent().find('.column:last-child input[type="file"]').on('change', (event) => {
          displayUploadedImage(event.currentTarget);
        });
      }, 500);
    });
  },
});
