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

final class DetailsElement extends Element implements DetailsElementInterface
{
    public function getProductCode(): string
    {
        return $this->getElement('product_code')->getText();
    }

    public function hasChannel(string $channelCode): bool
    {
        if ($this->hasElement('channel', ['%channelCode%' => $channelCode])) {
            return true;
        }

        return false;
    }

    public function countChannels(): int
    {
        if (!$this->hasElement('channel')) {
            return 0;
        }

        $channels = $this->getDocument()->findAll('css', ['data-test-channel']);

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
            'channel' => '[data-test-channel="%channelCode%"]',
            'current_stock' => '[data-test-current-stock]',
            'product_code' => '[data-test-product-code]',
            'tax_category' => '[data-test-tax-category]',
        ]);
    }
}
