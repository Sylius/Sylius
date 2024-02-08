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

namespace Sylius\Behat\Page\Admin\Product;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Component\Core\Model\ProductVariantInterface;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function isSimpleProductPage(): bool
    {
        return !$this->hasElement('variants');
    }

    public function isShowInShopButtonDisabled(): bool
    {
        return $this->getElement('show_product_single_button')->hasClass('disabled');
    }

    public function getAppliedCatalogPromotionsLinks(string $variantName, string $channelName): array
    {
        $appliedPromotions = $this->getAppliedCatalogPromotions($variantName, $channelName);

        return array_map(fn (NodeElement $element): string => $element->getAttribute('href'), $appliedPromotions);
    }

    public function getAppliedCatalogPromotionsNames(string $variantName, string $channelName): array
    {
        $appliedPromotions = $this->getAppliedCatalogPromotions($variantName, $channelName);

        return array_map(fn (NodeElement $element): string => $element->getText(), $appliedPromotions);
    }

    public function getName(): string
    {
        return $this->getElement('product_name')->getText();
    }

    public function getBreadcrumb(): string
    {
        return $this->getElement('breadcrumb')->getText();
    }

    public function getRouteName(): string
    {
        return 'sylius_admin_product_show';
    }

    public function showProductInChannel(string $channel): void
    {
        $this->getElement('show_product_dropdown')->clickLink($channel);
    }

    public function showProductInSingleChannel(): void
    {
        $this->getElement('show_product_single_button')->click();
    }

    public function showProductEditPage(): void
    {
        $this->getElement('edit_product_button')->click();
    }

    public function showVariantEditPage(ProductVariantInterface $variant): void
    {
        $this->getElement('edit_variant_button', ['%variantCode%' => $variant->getCode()])->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'edit_product_button' => '#edit-product',
            'edit_variant_button' => '#variants .variants-accordion__title:contains("%variantCode%") .edit-variant',
            'product_name' => '#header h1 .content > span',
            'show_product_dropdown' => '.scrolling.menu',
            'show_product_single_button' => '.ui.labeled.icon.button',
            'variants' => '#variants',
            'breadcrumb' => '.breadcrumb > div',
        ]);
    }

    private function getAppliedCatalogPromotions(string $variantName, string $channelName): array
    {
        $pricingElement = $this->getPricingRow($variantName, $channelName);

        return $pricingElement->findAll('css', '.applied-promotion');
    }

    private function getPricingRow(string $variantName, string $channelName): NodeElement
    {
        /** @var NodeElement|null $pricingRow */
        $pricingRow = $this->getDocument()->find(
            'css',
            sprintf('tr:contains("%s") + tr', $variantName),
        );

        $pricingRow = $pricingRow->find('css', sprintf('td:contains("%s")', $channelName));

        if ($pricingRow === null) {
            throw new \InvalidArgumentException(sprintf('Cannot find pricing row for variant "%s" in channel "%s"', $variantName, $channelName));
        }

        return $pricingRow;
    }
}
