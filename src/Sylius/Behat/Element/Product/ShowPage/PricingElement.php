<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Element\Product\ShowPage;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Element\Element;

final class PricingElement extends Element implements PricingElementInterface
{
    public function getPriceForChannel(string $channelName): string
    {
        /** @var NodeElement|null $priceForChannel */
        $channelPriceRow = $this->getChannelPriceRow($channelName);

        if (null === $channelPriceRow) {
            return '';
        }

        $priceForChannel = $channelPriceRow->find('css', 'td:nth-child(2)');

        return $priceForChannel->getText();
    }

    public function getOriginalPriceForChannel(string $channelName): string
    {
        /** @var NodeElement $priceForChannel */
        $channelPriceRow = $this->getChannelPriceRow($channelName);

        $priceForChannel = $channelPriceRow->find('css', 'td:nth-child(3)');

        return $priceForChannel->getText();
    }

    public function getCatalogPromotionsNamesForChannel(string $channelName): array
    {
        $appliedPromotions = $this->getAppliedPromotionsForChannel($channelName);

        return array_map(function(NodeElement $element): string {
            return $element->getText();
        }, $appliedPromotions);
    }

    public function getCatalogPromotionLinksForChannel(string $channelName): array
    {
        $appliedPromotions = $this->getAppliedPromotionsForChannel($channelName);

        return array_map(function(NodeElement $element): string {
            return $element->getAttribute('href');
        }, $appliedPromotions);
    }

    private function getChannelPriceRow(string $channelName): ?NodeElement
    {
        return $this->getDocument()->find('css', sprintf('#pricing tr:contains("%s")', $channelName));
    }

    private function getAppliedPromotionsForChannel(string $channelName): array
    {
        $channelPriceRow = $this->getChannelPriceRow($channelName);

        return $channelPriceRow->findAll('css', '.applied-promotion');
    }
}
