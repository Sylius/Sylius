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

import './scripts/bootstrap';
import './scripts/spotlight';

const imagesContext = require.context('./images', true, /\.(jpg|jpeg|png|svg|gif|webp)$/);
imagesContext.keys().forEach(imagesContext);
