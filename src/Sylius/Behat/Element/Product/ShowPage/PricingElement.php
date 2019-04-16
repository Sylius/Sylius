<?php

declare(strict_types=1);

namespace Sylius\Behat\Element\Product\ShowPage;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class PricingElement extends Element implements PricingElementInterface
{
    public function getPriceForChannel(string $channelName): string
    {
        /** @var NodeElement $priceForChannel */
        $channelPriceRow = $this->getDocument()->find('css', sprintf('#pricing tr:contains("%s")', $channelName));

        $priceForChannel = $channelPriceRow->find('css', 'td:nth-child(2)');

        return $priceForChannel->getText();
    }

    public function getOrginalPriceForChannel(string $channelName): string
    {
        /** @var NodeElement $priceForChannel */
        $channelPriceRow = $this->getDocument()->find('css', sprintf('#pricing tr:contains("%s")', $channelName));

        $priceForChannel = $channelPriceRow->find('css', 'td:nth-child(3)');

        return $priceForChannel->getText();
    }
}
