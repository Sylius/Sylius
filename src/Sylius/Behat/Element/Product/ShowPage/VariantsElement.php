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
        $variants = $this->getDocument()->findAll('css', '#variants .item');

        return \count($variants);
    }

    public function hasProductVariant(string $name): bool
    {
        $variantRows = $this->getDocument()->findAll('css', '#variants .variants-accordion__title');

        /** @var NodeElement $variant */
        foreach ($variantRows as $variant) {
            if ($variant->find('css', '.content .variant-name')->getText() === $name) {
                return true;
            }
        }

        return false;
    }

    public function hasProductVariantWithCodePriceAndCurrentStock(
        string $name,
        string $code,
        string $price,
        string $currentStock,
        string $channel,
    ): bool {
        /** @var NodeElement $variantRow */
        $variantRows = $this->getDocument()->findAll('css', '#variants .variants-accordion__title');

        /** @var NodeElement $variant */
        foreach ($variantRows as $variant) {
            if (
                $this->hasProductWithGivenNameCodePriceAndCurrentStock(
                    $variant,
                    $name,
                    $code,
                    $price,
                    $currentStock,
                    $channel,
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

    private function hasProductWithGivenNameCodePriceAndCurrentStock(
        NodeElement $variant,
        string $name,
        string $code,
        string $price,
        string $currentStock,
        string $channel,
    ): bool {
        $variantContent = $variant->getParent()->find(
            'css',
            sprintf('.variants-accordion__content.%s', $this->getItemIndexClass($variant)),
        );

        if (
            $variant->find('css', '.content .variant-name')->getText() === $name &&
            $variant->find('css', '.content .variant-code')->getText() === $code &&
            $variantContent->find('css', sprintf('tr.pricing:contains("%s") td:nth-child(2)', $channel))->getText() === $price &&
            $variant->find('css', '.current-stock')->getText() === $currentStock
        ) {
            return true;
        }

        return false;
    }

    private function getItemIndexClass(NodeElement $variant): string
    {
        return explode(' ', $variant->getAttribute('class'))[1];
    }
}
