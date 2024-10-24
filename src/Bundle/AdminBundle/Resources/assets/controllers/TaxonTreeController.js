/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';
import InfiniteTree from "infinite-tree";

export default class extends Controller {
  static values = {
    treeData: Array,
    autoOpen: Boolean,
  };
  static targets = ['tree', 'itemPrototyp'];

  async initialize() {
    this.component = await getComponent(this.element);
    this.tree = this.createInfiniteTree();

    this.component.on('render:finished', () => {
      this.tree.loadData(this.treeDataValue);
    });
  }

  createInfiniteTree() {
    return new InfiniteTree({
      el: this.treeTarget,
      data: this.treeDataValue,
      autoOpen: this.autoOpenValue,
      selectable: false,
      rowRenderer: (node, treeOptions) => this.rowRenderer(node, treeOptions),
    });
  }

  rowRenderer(node, treeOptions) {
    const { id, name, children, state } = node;
    const { depth, open, path, total } = state;
    const more = node.hasChildren();
    const nodeMargin = 24;

    let itemPrototyp = this.itemPrototypTarget.firstElementChild.cloneNode(true);
    let togglerPrototyp = itemPrototyp.querySelector('[data-infinite-tree-toggler]');

    itemPrototyp.style.marginLeft = `${(depth * nodeMargin)}px`;
    itemPrototyp.setAttribute('data-id', id);
    itemPrototyp.setAttribute('data-expanded', more && open);
    itemPrototyp.setAttribute('data-depth', depth);
    itemPrototyp.setAttribute('data-path', path);
    itemPrototyp.setAttribute('data-children', children.length);
    itemPrototyp.setAttribute('data-total', total);

    togglerPrototyp.style.width = `${nodeMargin}px`;
    if (more) {
      togglerPrototyp.classList.add(open ? 'infinite-tree-open' : 'infinite-tree-closed');
    } else {
      togglerPrototyp.classList.add('infinite-tree-leaf');
    }

    return itemPrototyp.outerHTML.replaceAll(`__TAXON_ID__`, id).replaceAll(`__TAXON_NAME__`, name);
  }
}
