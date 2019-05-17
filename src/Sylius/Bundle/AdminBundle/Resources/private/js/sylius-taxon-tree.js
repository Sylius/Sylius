class SyliusTaxonomyTree {
  constructor() {
    this.attr = {
      tree: 'data-sylius-js-tree',
      parent: 'data-sylius-js-tree-parent',
      trigger: 'data-sylius-js-tree-trigger',
      storageName: 'sylius:taxonomy:hidden',
    };

    this.tree = document.querySelector(`[${this.attr.tree}]`);
    if (!this.tree) return;
    this.hiddenItems = this.getMapFromStorage();
    this.renderMap();
    this.tree.classList.remove('hidden');

    this.tree.querySelectorAll(`[${this.attr.trigger}]`).forEach((trigger) => {
      trigger.addEventListener('click', this.handleTrigger.bind(this, trigger));
    });
  }

  handleTrigger(trigger, e) {
    e.preventDefault();

    const id = trigger.getAttribute(this.attr.trigger) || null;
    const parent = this.tree.querySelector(`[${this.attr.parent}="${id}"]`) || this.tree;
    const toRemove = this.hiddenItems.indexOf(id) === -1;

    this.hiddenItems = !id && this.hiddenItems.length ? [] : this.toggle(toRemove, this.getIDs(parent, toRemove));
    this.saveMapToStorage(this.hiddenItems);
    this.renderMap();
  }

  /**
   * Adding or removing the given array of items ID from the hiddenItems array
   * @param {boolean} action - true: add, false: remove from array
   * @param {Array} ids - array of items ID
   * @return {Array}
   */
  toggle(action, ids) {
    const newMap = [...this.hiddenItems];

    ids.forEach((item) => {
      const index = newMap.indexOf(item);
      if (action && index === -1) newMap.push(item);
      if (!action && index !== -1) newMap.splice(index, 1);
    });

    return newMap;
  }

  /**
   * Return ID of given Node element (if has one) and optional IDs of children
   * @param {Node} parent
   * @param {boolean} withChildren
   * @return {Array}
   */
  getIDs(parent, withChildren = true) {
    const arr = parent.hasAttribute(this.attr.parent) ? [parent] : [];
    const children = withChildren ? [].slice.call(parent.querySelectorAll(`[${this.attr.parent}]`)) : [];
    return [...arr, ...children].map((child, i) => child.getAttribute(this.attr.parent));
  }

  /**
   * Hides elements if their ID is in the hiddenItems array
   */
  renderMap() {
    this.tree.querySelectorAll(`[${this.attr.parent}]`).forEach(parent => {
      const id = parent.getAttribute(this.attr.parent);
      const state = this.hiddenItems.indexOf(id) !== -1;
      parent.classList.toggle('hide', state);
    });
  }

  /**
   * Get items from local storage
   * @return {Array}
   */
  getMapFromStorage() {
    const items = localStorage.getItem(this.attr.storageName);
    return items ? JSON.parse(items) : [];
  }

  /**
   * Save items to local storage
   * @param {Array} items
   */
  saveMapToStorage(items) {
    window.localStorage.setItem(this.attr.storageName, JSON.stringify(items));
  }
}

export default SyliusTaxonomyTree;
