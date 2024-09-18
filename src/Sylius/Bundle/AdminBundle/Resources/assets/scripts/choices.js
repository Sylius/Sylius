/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import Choices from 'choices.js';

(function () {
  const initValues = (initUrl, currentValues, choiceLabelField, choiceValueField, callback) => {
    const defaultChoicesParam = currentValues.split(',').map((item) => `${choiceValueField}[]=${item}`).join('&');
    fetch(`${initUrl}?${defaultChoicesParam}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    })
      .then((response) => response.json())
      .then((data) => {
        callback(data.map((item) => ({ label: item[choiceLabelField], value: item[choiceValueField], selected: true })));
      })
      .catch((error) => console.error('Error:', error));
  };

  // eslint-disable-next-line no-undef
  const items = document.querySelectorAll('[data-choices]');

  if (items.length) {
    items.forEach((el) => {
      const {
        inputId,
        initUrl,
        searchUrl,
        noChoicesText,
        choiceLabelField,
        choiceValueField,
      } = el.dataset;
      const isMultiple = el.getAttribute('multiple') != null;
      // eslint-disable-next-line no-undef
      const input = document.getElementById(inputId);

      // eslint-disable-next-line no-new
      const choices = new Choices(el, {
        noChoicesText,
      });

      if (initUrl != null || input.value != null) {
        initValues(initUrl, input.value, choiceLabelField, choiceValueField, (data) => choices.setChoices(data));
      }

      el.addEventListener('search', (event) => {
        let excludes;
        if (isMultiple) {
          const currentChoices = choices.getValue() == null ? [] : choices.getValue();
          excludes = currentChoices.map((item) => `excludes[]=${item.value}`).join('&');
        } else {
          excludes = `excludes[]=${choices.getValue(true) == null ? '' : choices.getValue(true)}`;
        }
        fetch(`${searchUrl}?phrase=${event.detail.value}&${excludes}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
          },
        })
          .then((response) => response.json())
          .then((data) => {
            choices.setChoices(data.map((item) => ({ label: item[choiceLabelField], value: item[choiceValueField] })), 'value', 'label', true);
          })
          .catch((error) => console.error('Error:', error));
      });

      el.addEventListener('addItem', () => {
        if (!isMultiple) {
          input.value = choices.getValue(true) == null ? '' : choices.getValue(true);
        } else {
          input.value = choices.getValue().map((item) => item.value).join(',');
        }

        choices.setChoices([], 'value', 'label', true);
      });

      el.addEventListener('removeItem', () => {
        if (!isMultiple) {
          input.value = choices.getValue(true) == null ? '' : choices.getValue(true);
        } else {
          input.value = choices.getValue().map((item) => item.value).join(',');
        }

        choices.setChoices([], 'value', 'label', true);
      });
    });
  }
}());
