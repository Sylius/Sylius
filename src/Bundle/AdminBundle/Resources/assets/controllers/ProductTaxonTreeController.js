/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import { Controller } from '@hotwired/stimulus';
import InfiniteTree from "infinite-tree";

export default class extends Controller {
  static values = {
    treeData: Array,
    autoOpen: Boolean,
  };
  static targets = ['tree', 'filter', 'productTaxons'];

  connect() {
    this.tree = this.createInfiniteTree();

    this.tree.on('contentDidUpdate', () => this.updateIndeterminateState());
    this.tree.on('clusterDidChange', () => this.updateIndeterminateState());
    this.tree.on('checkNode', (node) => this.updateProductTaxons(node));

    this.checkInitialNodes();
  }

  createInfiniteTree() {
    return new InfiniteTree({
      el: this.treeTarget,
      data: this.treeDataValue,
      autoOpen: this.autoOpenValue,
      selectable: false,
      rowRenderer: this.rowRenderer,
    });
  }

  rowRenderer(node, treeOptions) {
    const { id, name, children, state } = node;
    const { depth, open, path, total, filtered, checked, indeterminate } = state;
    const more = node.hasChildren();
    const nodeMargin = 24;

    if (filtered === false)  {
      return '';
    }

    let togglerClass = '';
    if (more) {
      togglerClass = open ? 'infinite-tree-open' : 'infinite-tree-closed';
    }

    const toggler = `<span class="${treeOptions.togglerClass} ${togglerClass}" style="width: ${nodeMargin}px"></span>`;
    const checkbox = `<span class="infinite-tree-check" style="width: ${nodeMargin}px;"><input class="form-check-input" type="checkbox" data-action="product-taxon-tree#clickNode" ${indeterminate && !checked ? 'data-indeterminate' : ''} ${checked ? 'checked' : ''}></span>`;
    const treeNode = `<div class="infinite-tree-node" style="margin-left: ${(depth * nodeMargin)}px">${toggler}${checkbox}<span class="infinite-tree-title">${name}</span></div>`;

    return `<div
          data-id="${id}"
          data-expanded="${more && open}"
          data-depth="${depth}"
          data-path="${path}"
          data-children="${children.length}"
          data-total="${total}"
          class="infinite-tree-item"
        >
          ${treeNode}
        </div>`;
  }

  clickNode(event) {
    const id = event.target.closest('.infinite-tree-item').dataset.id;
    this.checkNode(this.tree.getNodeById(id));
  }

  checkNode(node, checked) {
    if (true === checked) {
      node.state.checked = true;
    } else if (false === checked) {
      node.state.checked = false;
    } else {
      node.state.checked = node.state.checked === undefined ? true : !node.state.checked;
    }

    const topParentNode = this.updateParentNodes(node);
    this.tree.updateNode(topParentNode);
    this.tree.emit('checkNode', node);
  }

  updateParentNodes(childNode) {
    let parentNode = childNode;

    while (parentNode.parent && parentNode.parent.state.depth >= 0) {
      parentNode = parentNode.parent;
      let checkedCount = 0;
      let indeterminate = false;

      parentNode.children.forEach((childNode) => {
        indeterminate = indeterminate || !!childNode.state.indeterminate;
        if (childNode.state.checked) {
          checkedCount++;
        }
      });

      if (checkedCount > 0 || indeterminate) {
        parentNode.state.indeterminate = true;
      } else if (checkedCount === 0) {
        parentNode.state.indeterminate = false;
      }
    }

    return parentNode;
  }

  updateIndeterminateState() {
    const checkboxes = this.tree.contentElement.querySelectorAll('input[type="checkbox"]');
    for (const checkbox of checkboxes) {
      checkbox.indeterminate = checkbox.hasAttribute('data-indeterminate');
    }
  }

  updateProductTaxons(node) {
    let values = this.productTaxonsTarget.value.split(',').filter(Boolean);

    if (false === node.state.checked) {
      values.splice(values.indexOf(node.id), 1);
    } else if (!values.includes(node.id)) {
      values.push(node.id);
    }

    this.productTaxonsTarget.value = values.join();
  }

  checkInitialNodes() {
    this.productTaxonsTarget.value.split(',').filter(Boolean).forEach((productTaxonCode) => {
      this.checkNode(this.tree.getNodeById(productTaxonCode));
    });
  }

  filter(event) {
    this.tree.filter(event.target.value, {
      caseSensitive: false,
      exactMatch: false,
      includeAncestors: true,
      includeDescendants: true,
    });
  }

  clearFilter() {
    this.filterTarget.value = '';
    this.tree.unfilter();
  }

  checkAll(event) {
    event.preventDefault();
    this.toggleAllNodes(true);
  }

  uncheckAll(event) {
    event.preventDefault();
    this.toggleAllNodes(false);
  }

  toggleAllNodes(isChecked) {
    this.tree.nodes.forEach((node) => {
      if (!this.tree.filtered || node.state.filtered) {
        this.checkNode(node, isChecked);
      }
    });
  }
}
