/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import $ from 'jquery';

/**
 * Collection Form plugin
 *
 * @param element
 * @constructor
 */
class CollectionForm {
  constructor(element) {
    this.addItem = this.addItem.bind(this);
    this.updateItem = this.updateItem.bind(this);
    this.deleteItem = this.constructor.deleteItem;
    this.updatePrototype = this.updatePrototype.bind(this);

    this.$element = $(element);
    this.$list = this.$element.find('[data-form-collection="list"]:first');
    this.count = this.$list.children().length;
    this.lastChoice = null;
    this.$element.on('click', '[data-form-collection="add"]:last', this.addItem);
    this.$element.on('click', '[data-form-collection="delete"]', this.deleteItem);
    this.$element.on('change', '[data-form-collection="update"]', this.updateItem);
    $(document).on('change', '[data-form-prototype="update"]', this.updatePrototype);
    $(document).on('collection-form-add', (event, addedElement) => {
      $(addedElement).find('[data-form-type="collection"]').CollectionForm();
      $(document).trigger('dom-node-inserted', [$(addedElement)]);
    });
  }

  /**
   * Add a item to the collection.
   * @param event
   */
  addItem(event) {
    event.preventDefault();

    let prototype = this.$element.data('prototype');
    let prototypeName = new RegExp(this.$element.data('prototype-name'), 'g');

    prototype = prototype.replace(prototypeName, this.count);

    this.$list.append(prototype);
    this.count = this.count + 1;

    $(document).trigger('collection-form-add', [this.$list.children().last()]);
  }

  /**
   * Update item from the collection
   */
  updateItem(event) {
    event.preventDefault();
    const $element = $(event.currentTarget);
    const url = $element.data('form-url');
    const value = $element.val();
    const $container = $element.closest('[data-form-collection="item"]');
    const index = $container.data('form-collection-index');
    const position = $container.data('form-collection-index');

    if (url) {
      $container.load(url, { id: value, position });
    } else {
      let $prototype = this.$element.find(`[data-form-prototype="${value}"]`);
      let prototypeName = new RegExp($prototype.data('subprototype-name'), 'g');

      let prototype = $prototype.val().replace(prototypeName, index);

      $container.replaceWith(prototype);
    }
    $(document).trigger('collection-form-update', [$(event.currentTarget)]);
  }

  /**
   * Delete item from the collection
   * @param event
   */
  static deleteItem(event) {
    event.preventDefault();

    $(event.currentTarget)
      .closest('[data-form-collection="item"]')
      .remove();

    $(document).trigger('collection-form-delete', [$(event.currentTarget)]);
  }

  /**
   * Update the prototype
   * @param event
   */
  updatePrototype(event) {
    const $target = $(event.currentTarget);
    let prototypeName = $target.val();

    if ($target.data('form-prototype-prefix') !== undefined) {
      prototypeName = $target.data('form-prototype-prefix') + prototypeName;
    }

    if (this.lastChoice !== null && this.lastChoice !== prototypeName) {
      this.$list.html('');
    }

    this.lastChoice = prototypeName;

    this.$element.data('prototype', this.$element.find(`[data-form-prototype="${prototypeName}"]`).val());
  }
}

/*
 * Plugin definition
 */

$.fn.CollectionForm = function CollectionFormPlugin(option) {
  this.each((idx, el) => {
    const $element = $(el);
    const data = $element.data('collectionForm');
    const options = typeof option === 'object' && option;

    if (!data) {
      $element.data('collectionForm', new CollectionForm(el, options));
    }
  });
};

$.fn.CollectionForm.Constructor = CollectionForm;
