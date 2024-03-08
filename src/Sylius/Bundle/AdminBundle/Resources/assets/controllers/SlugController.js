/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {ApplicationController, useDebounce} from 'stimulus-use'
import slugify from 'slugify';

export default class extends ApplicationController {
  static debounces = ['generateSlug']
  static targets = [ 'sluggable', 'slug' ]
  static values = { locale: String }

  connect() {
    useDebounce(this);
  }

  generateSlug() {
    this.element.setAttribute('busy', '');
    this.slugTarget.value = slugify(this.sluggableTarget.value, { locale: this.localeValue, lower: true });
    this.element.removeAttribute('busy');
  }
}
