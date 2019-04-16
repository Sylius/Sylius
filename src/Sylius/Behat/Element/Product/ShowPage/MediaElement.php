<?php

declare(strict_types=1);

namespace Sylius\Behat\Element\Product\ShowPage;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Element\Element;

final class MediaElement extends Element implements MediaElementInterface
{
    public function isImageDisplayed(): bool
    {
        $imageElement = $this->getDocument()->find('css','#media a img');

            if ($imageElement === null) {
                return false;
            }
            $imageUrl = $imageElement->getAttribute('src');
            $this->getDriver()->visit($imageUrl);
            $pageText = $this->getDocument()->getText();
            $this->getDriver()->back();

            return false === stripos($pageText, '404 Not Found');
        }
}
