/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {default as SlugController} from './SlugController';

export default class extends SlugController {
  static debounces = [ 'generateSlug' ]
  static targets = [ 'sluggable', 'slug' ]
  static values = { url: String, locale: String, parentTaxonCode: String }
  parent = document.querySelector('[data-input-id="sylius_taxon_parent"]');

  connect() {
    parent.addEventListener('change', this.onParentTaxonChange.bind(this));
  }

  disconnect() {
    parent.removeEventListener('change', this.onParentTaxonChange.bind(this));
  }

  onParentTaxonChange(event) {
    this.parentTaxonCodeValue = event.detail.value;
    this.generateSlug();
  }

  generateSlug() {
    let url = this.urlValue + '?' + new URLSearchParams({locale: this.localeValue, name: this.sluggableTarget.value, parentCode: this.parentTaxonCodeValue})

    fetch(url)
      .then(response => response.json())
      .then(data => { this.slugTarget.value = data.slug; })
    ;
  }
}
