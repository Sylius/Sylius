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

final class VariantsElement extends Element implements VariantsElementInterface
{
    public function countVariantsOnPage(): int
    {
        /** @var NodeElement $variants|array */
        $variants = $this->getDocument()->findAll('css', '[data-test-variant]');

        return \count($variants);
    }

    public function hasProductVariant(string $code): bool
    {
        return $this->hasElement('variant', ['%code%' => $code]);
    }

    public function hasProductVariantWithCodePriceAndCurrentStock(
        string $name,
        string $code,
        string $price,
        string $currentStock,
        string $channelCode,
    ): bool {
        /** @var NodeElement $variantRow */
        $variantRows = $this->getDocument()->findAll('css', '[data-test-variant]');

        /** @var NodeElement $variant */
        foreach ($variantRows as $variant) {
            if (
                $this->hasProductWithGivenNameCodePriceAndCurrentStock(
                    $variant,
                    $name,
                    $code,
                    $price,
                    $currentStock,
                    $channelCode,
                )
            ) {
                return true;
            }
        }

        return false;
    }

    public function hasProductVariantWithLowestPriceBeforeDiscountInChannel(
        string $productVariantCode,
        string $lowestPriceBeforeDiscount,
        string $channelCode,
    ): bool {
        /** @var NodeElement $variant */
        $variant = $this->getDocument()->find('css', sprintf('[data-test-lowest-price-before-the-discount="%s.%s"]', $productVariantCode, $channelCode));

        if ($variant) {
            return $variant->getText() === $lowestPriceBeforeDiscount;
        }

        return false;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'variant' => '[data-test-variant="%code%"]',
            'variant_pricing_row' => '[data-test-variant-pricing="%channel_code%.%variant_code%"]',
        ]);
    }

    private function hasProductWithGivenNameCodePriceAndCurrentStock(
        NodeElement $variant,
        string $name,
        string $code,
        string $price,
        string $currentStock,
        string $channelCode,
    ): bool {
        if (
            $variant->find('css', '[data-test-product-variant-code]')->getText() === $code &&
            $variant->find('css', '[data-test-product-variant-name]')->getText() === $name &&
            $this->getElement('variant_pricing_row', [
                '%channel_code%' => $channelCode,
                '%variant_code%' => $code,
            ])->find('css', '[data-test-price]')->getText() === $price &&
            $variant->find('css', '[data-test-current-stock]')->getText() === $currentStock
        ) {
            return true;
        }

        return false;
    }
}
