<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Element\Product\ShowPage;

use FriendsOfBehat\PageObjectExtension\Element\Element;

class MediaElement extends Element implements MediaElementInterface
{
    public function isImageDisplayed(): bool
    {
        $imageElement = $this->getDocument()->find('css', '[data-test-media] img');
        if ($imageElement === null) {
            return false;
        }
        $imageUrl = $imageElement->getAttribute('src');
        $originalUrl = $this->getDriver()->getCurrentUrl();

        $this->getDriver()->visit($imageUrl);
        $pageText = $this->getDocument()->getText();
        $this->getDriver()->visit($originalUrl);

        return false === stripos($pageText, '404 Not Found');
    }
}
