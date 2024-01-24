/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {ApplicationController, useDebounce} from 'stimulus-use'

export default class extends ApplicationController {
  static debounces = ['generateSlug']
  static targets = [ 'sluggable', 'slug' ]
  static values = { url: String, locale: String }

  connect() {
    useDebounce(this);
  }

  generateSlug() {
    let url = this.urlValue + '?' + new URLSearchParams({locale: this.localeValue, name: this.sluggableTarget.value})

    fetch(url)
      .then(response => response.json())
      .then(data => { this.slugTarget.value = data.slug; })
    ;
  }
}
