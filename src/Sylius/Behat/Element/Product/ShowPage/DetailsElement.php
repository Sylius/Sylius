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

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Element\Element;

final class DetailsElement extends Element implements DetailsElementInterface
{
    public function getProductCode(): string
    {
        return $this->getElement('product_code')->getText();
    }

    public function hasChannel(string $channelName): bool
    {
        $channels = $this->getElement('channels');

        /** @var NodeElement $channel */
        foreach ($channels->findAll('css', 'span') as $channel) {
            if ($channel->getText() === $channelName) {
                return true;
            }
        }

        return false;
    }

    public function countChannels(): int
    {
        if (!$this->hasElement('channels')) {
            return 0;
        }

        $channels = $this->getElement('channels')->findAll('css', 'span.channel-name');

        return \count($channels);
    }

    public function getProductCurrentStock(): int
    {
        return (int) $this->getElement('current_stock')->getText();
    }

    public function getProductTaxCategory(): string
    {
        return $this->getElement('tax_category')->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'channels' => '#details tr:contains("Channels") td:nth-child(2)',
            'current_stock' => '#details tr:contains("Current stock") td:nth-child(2)',
            'product_code' => '#details tr:contains("Code") td:nth-child(2)',
            'tax_category' => '#details tr:contains("Tax category") td:nth-child(2)',
        ]);
    }
}
