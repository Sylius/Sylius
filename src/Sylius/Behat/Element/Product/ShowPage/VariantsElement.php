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

final class VariantsElement extends Element implements VariantsElementInterface
{
    public function countVariantsOnPage(): int
    {
        /** @var NodeElement $variants|array */
        $variants = $this->getDocument()->findAll('css', '#variants .variant');

        return \count($variants);
    }

    public function hasProductVariantWithCodePriceAndCurrentStock(string $name, string $code, string $price, string $currentStock): bool
    {
        /** @var NodeElement $variantRow */
        $variantRows = $this->getDocument()->findAll('css', '#variants .variant');

        /** @var NodeElement $variant */
        foreach ($variantRows as $variant) {
            if (
                $this->hasProductWithGivenNameCodePriceAndCurrentStock($variant, $name, $code, $price, $currentStock)
            ) {
                return true;
            }
        }

        return false;
    }

    private function hasProductWithGivenNameCodePriceAndCurrentStock(NodeElement $variant, string $name, string $code, string $price, string $currentStock): bool
    {
        if (
            $variant->find('css', '.title span.variant-name')->getText() === $name &&
            $variant->find('css', '.title span.variant-code')->getText() === $code &&
            $variant->find('css', '.content .pricing tr:contains("WEB-US") td:nth-child(2)')->getText() === $price &&
            $variant->find('css', '.content span.current-stock-label span.current-stock')->getText() === $currentStock
        ) {
            return true;
        }

        return false;
    }
}
