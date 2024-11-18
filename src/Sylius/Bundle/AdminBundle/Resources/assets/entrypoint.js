/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import './styles/main.scss';

import './app';

import './scripts/bulk-delete';
import './scripts/check-all';
import './scripts/menu-search';
import './scripts/spotlight';
import './scripts/statistics_chart';
import './scripts/sticky-header';

import './scripts/bootstrap';

import './images/404.svg';
import './images/loader.gif';
import './images/no_data.svg';
import './images/sylius-logo.svg';
import './images/sylius-logo-dark-text.png';


document.addEventListener('click', function (event) {
  console.log('Widzi kliknięcie');
  if (event.target.matches('[data-hook]::before') || event.target.matches('[data-hook]')) {
    console.log('Udało się kliknąć');
    // Pobierz tekst wyświetlany w tooltipie
    const tooltipText = event.target.getAttribute('data-hook') + " | " + event.target.getAttribute('data-name');

    // Skopiuj tekst do schowka
    navigator.clipboard.writeText(tooltipText).then(() => {
      console.log('Skopiowano: ' + tooltipText);
    }).catch(err => {
      console.error('Nie udało się skopiować tekstu: ', err);
    });
  }
});
