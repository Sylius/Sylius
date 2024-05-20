import InfiniteTree from 'infinite-tree';

(function () {
    const treeElement = document.querySelector('[data-infinite-tree]');
    const filterInput = document.querySelector('[data-infinite-tree-filter]');
    const filterClear = document.querySelector('[data-infinite-tree-clear]');
    const checkAll = document.querySelector('[data-infinite-tree-check-all]');
    const uncheckAll = document.querySelector('[data-infinite-tree-uncheck-all]');

    if (!treeElement) return;

    const treeArray = JSON.parse(treeElement.dataset.infiniteTree);

    const rowRenderer = (node, treeOptions) => {
        const { id, name, loadOnDemand = false, children, state } = node;
        const { depth, open, path, total, selected = false, filtered } = state;
        const childrenLength = Object.keys(children).length;
        const more = node.hasChildren();
        const nodeMargin = 24;

        if (filtered === false) return '';

        let togglerClass = '';
        if (more) {
            togglerClass = open ? 'infinite-tree-open' : 'infinite-tree-closed';
        } else if (loadOnDemand) {
            togglerClass = 'infinite-tree-closed';
        }

        const toggler = `<span class="${treeOptions.togglerClass} ${togglerClass}" style="width: ${nodeMargin}px"></span>`;
        const checkbox = `<span class="infinite-tree-check" style="width: ${nodeMargin}px;"><input class="form-check-input" type="checkbox" ${node.state.indeterminate ? 'data-indeterminate' : ''} ${node.state.checked ? 'checked' : ''}></span>`;
        const treeNode = `<div class="infinite-tree-node" style="margin-left: ${(depth * nodeMargin)}px">${toggler}${checkbox}<span class="infinite-tree-title">${name}</span></div>`;

        return `<div
          data-id="${id}"
          data-expanded="${more && open}"
          data-depth="${depth}"
          data-path="${path}"
          data-selected="${selected}"
          data-children="${childrenLength}"
          data-total="${total}"
          class="infinite-tree-item ${selected ? 'infinite-tree-selected' : ''}"
          droppable="${treeOptions.droppable}">${treeNode}
        </div>`;
    };

    const tree = new InfiniteTree({
        el: treeElement,
        data: treeArray,
        autoOpen: true,
        droppable: false,
        selectable: false,
        nodeIdAttr: 'data-id',
        rowRenderer,
    });

    const updateIndeterminateState = (tr) => {
        const checkboxes = tr.contentElement.querySelectorAll('input[type="checkbox"]');
        for (const checkbox of checkboxes) {
            checkbox.indeterminate = checkbox.hasAttribute('data-indeterminate');
        }
    };

    const handleTreeClick = (event) => {
        const currentNode = tree.getNodeFromPoint(event.clientX, event.clientY);
        if (currentNode && event.target.classList.contains('form-check-input')) {
            event.stopPropagation();
            tree.checkNode(currentNode);
        }
    };

    const handleFilterInput = (event) => {
        tree.filter(event.target.value, {
            caseSensitive: false,
            exactMatch: false,
            includeAncestors: true,
            includeDescendants: true,
        });
    };

    const clearFilter = () => {
        filterInput.value = '';
        tree.unfilter();
    };

    const handleFilterClear = (event) => {
        event.preventDefault();
        clearFilter();
    };

    const handleEscapeKey = (event) => {
        if (event.key === 'Escape' && document.activeElement === filterInput) {
            clearFilter();
        }
    };

    const toggleAllNodes = (e, isChecked) => {
        e.preventDefault();
        tree.nodes.forEach((node) => {
            tree.checkNode(node, isChecked);
        });
    };

    function registerEventListeners() {
        tree.on('click', handleTreeClick);
        tree.on('contentDidUpdate', () => updateIndeterminateState(tree));
        tree.on('clusterDidChange', () => updateIndeterminateState(tree));

        if (filterInput && filterClear) {
            filterInput.addEventListener('input', handleFilterInput);
            filterClear.addEventListener('click', handleFilterClear);
            document.addEventListener('keydown', handleEscapeKey);
        }

        if (checkAll && uncheckAll) {
            checkAll.addEventListener('click', (e) => toggleAllNodes(e, true));
            uncheckAll.addEventListener('click', (e) => toggleAllNodes(e, false));
        }
    }

    registerEventListeners();

    updateIndeterminateState(tree);
}());
