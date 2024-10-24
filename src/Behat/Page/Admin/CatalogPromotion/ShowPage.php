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

namespace Sylius\Behat\Page\Admin\CatalogPromotion;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_admin_catalog_promotion_show';
    }

    public function getName(): string
    {
        return $this->getElement('name')->getText();
    }

    public function getStartDate(): string
    {
        return $this->getElement('start_date')->getText();
    }

    public function getEndDate(): string
    {
        return $this->getElement('end_date')->getText();
    }

    public function getPriority(): int
    {
        return (int) $this->getElement('priority')->getText();
    }

    public function hasActionWithPercentageDiscount(string $amount): bool
    {
        $amountsElements = $this->getElement('actions')->findAll('css', '[data-test-action] [data-test-amount]');
        foreach ($amountsElements as $amountElement) {
            if ($amountElement->getText() === $amount) {
                return true;
            }
        }

        return false;
    }

    public function hasActionWithFixedDiscount(string $amount, ChannelInterface $channel): bool
    {
        $amountsElements = $this->getElement('actions')->findAll('css', '[data-test-' . $channel->getCode() . '-amount]');
        foreach ($amountsElements as $amountElement) {
            if ($amountElement->getText() === $amount) {
                return true;
            }
        }

        return false;
    }

    public function hasScopeWithVariant(ProductVariantInterface $variant): bool
    {
        $variantsElements = $this->getElement('scopes')->findAll('css', '[data-test-variants] li');
        foreach ($variantsElements as $variantElement) {
            if ($variantElement->getText() === $variant->getCode()) {
                return true;
            }
        }

        return false;
    }

    public function hasScopeWithProduct(ProductInterface $product): bool
    {
        $productsElements = $this->getElement('scopes')->findAll('css', '[data-test-products] li');
        foreach ($productsElements as $productElement) {
            if ($productElement->getText() === $product->getCode()) {
                return true;
            }
        }

        return false;
    }

    public function isExclusive(): bool
    {
        return null !== $this->getElement('exclusive')->find('css', 'svg.text-green');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'actions' => '[data-test-actions]',
            'end_date' => '[data-test-end-date]',
            'exclusive' => '[data-test-exclusive]',
            'name' => '[data-test-name]',
            'priority' => '[data-test-priority]',
            'scopes' => '[data-test-scopes]',
            'start_date' => '[data-test-start-date]',
        ]);
    }
}
