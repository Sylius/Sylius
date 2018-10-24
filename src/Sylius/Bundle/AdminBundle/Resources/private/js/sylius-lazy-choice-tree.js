/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import 'semantic-ui-css/components/api';
import 'semantic-ui-css/components/checkbox';
import $ from 'jquery';

const createRootContainer = function createRootContainer() {
  return $('<div class="ui list"></div>');
};

const createLeafContainerElement = function createLeafContainerElement() {
  return $('<div class="list"></div>');
};

const createLeafIconElement = function createLeafIconElement() {
  return $('<i class="folder icon"></i>');
};

const createLeafTitleElement = function createLeafTitleElement() {
  return $('<div class="header"></div>');
};

const createLeafTitleSpan = function createLeafTitleSpan(displayName) {
  return $(`<span style="margin-right: 5px; cursor: pointer;">${displayName}</span>`);
};

const createLeafContentElement = function createLeafContentElement() {
  return $('<div class="content"></div>');
};

$.fn.extend({
  choiceTree(type, multiple, defaultLevel) {
    const tree = this;
    const loader = tree.find('.dimmer');
    const loadedLeafs = [];
    const $input = tree.find('input[type="hidden"]');

    const createCheckboxElement = function createCheckboxElement(name, code, multi) {
      const chosenNodes = $input.val().split(',');
      let checked = '';
      if (chosenNodes.some(chosenCode => chosenCode === code)) {
        checked = 'checked="checked"';
      }
      if (multi) {
        return $(`<div class="ui checkbox" data-value="${code}"><input ${checked} type="checkbox" name="${type}"></div>`);
      }

      return $(`<div class="ui radio checkbox" data-value="${code}"><input ${checked} type="radio" name="${type}"></div>`);
    };

    const isLeafLoaded = function isLeafLoaded(code) {
      return loadedLeafs.some(leafCode => leafCode === code);
    };

    let createLeafFunc;

    const loadLeafAction = function loadLeafAction(parentCode, expandButton, content, icon, leafContainerElement) {
      icon.toggleClass('open');

      if (!isLeafLoaded(parentCode)) {
        expandButton.api({
          on: 'now',
          url: tree.data('tree-leafs-url') || tree.data('taxon-leafs-url'),
          method: 'GET',
          cache: false,
          data: {
            parentCode,
          },
          beforeSend(settings) {
            loader.addClass('active');

            return settings;
          },
          onSuccess(response) {
            response.forEach((leafNode) => {
              leafContainerElement.append((
                createLeafFunc(leafNode.name, leafNode.code, leafNode.hasChildren, multiple, leafNode.level)
              ));
            });
            content.append(leafContainerElement);
            loader.removeClass('active');
            loadedLeafs.push(parentCode);
          },
        });
      }

      leafContainerElement.toggle();
    };

    const bindExpandLeafAction = function bindExpandLeafAction(parentCode, expandButton, content, icon, level) {
      const leafContainerElement = createLeafContainerElement();
      if (defaultLevel > level) {
        loadLeafAction(parentCode, expandButton, content, icon, leafContainerElement);
      }

      expandButton.click(() => {
        loadLeafAction(parentCode, expandButton, content, icon, leafContainerElement);
      });
    };

    const bindCheckboxAction = function bindCheckboxAction(checkboxElement) {
      checkboxElement.checkbox({
        onChecked() {
          const value = checkboxElement.data('value');
          const checkedValues = $input.val().split(',').filter(Boolean);
          checkedValues.push(value);
          $input.val(checkedValues.join());
        },
        onUnchecked() {
          const value = checkboxElement.data('value');
          const checkedValues = $input.val().split(',').filter(Boolean);
          const i = checkedValues.indexOf(value);
          if (i !== -1) {
            checkedValues.splice(i, 1);
          }
          $input.val(checkedValues.join());
        },
      });
    };

    const createLeaf = function createLeaf(name, code, hasChildren, multipleChoice, level) {
      const displayNameElement = createLeafTitleSpan(name);
      const titleElement = createLeafTitleElement();
      const iconElement = createLeafIconElement();
      const checkboxElement = createCheckboxElement(name, code, multipleChoice);

      bindCheckboxAction(checkboxElement);

      const leafElement = $('<div class="item"></div>');
      const leafContentElement = createLeafContentElement();

      leafElement.append(iconElement);
      titleElement.append(displayNameElement);
      titleElement.append(checkboxElement);
      leafContentElement.append(titleElement);

      if (!hasChildren) {
        iconElement.addClass('outline');
      }
      if (hasChildren) {
        bindExpandLeafAction(code, displayNameElement, leafContentElement, iconElement, level);
      }
      leafElement.append(leafContentElement);

      return leafElement;
    };
    createLeafFunc = createLeaf;

    tree.api({
      on: 'now',
      method: 'GET',
      url: tree.data('tree-root-nodes-url') || tree.data('taxon-root-nodes-url'),
      cache: false,
      beforeSend(settings) {
        loader.addClass('active');

        return settings;
      },
      onSuccess(response) {
        const rootContainer = createRootContainer();
        response.forEach((rootNode) => {
          rootContainer.append((
            createLeaf(rootNode.name, rootNode.code, rootNode.hasChildren, multiple, rootNode.level)
          ));
        });
        tree.append(rootContainer);
        loader.removeClass('active');
      },
    });
  },
});
