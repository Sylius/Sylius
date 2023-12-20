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

final class AssociationsElement extends Element implements AssociationsElementInterface
{
    public function hasAssociation(string $associationName): bool
    {
        return [] !== $this->getAssociatedProducts($this->getElement('associations'), $associationName);
    }

    public function isAssociatedWith(string $associationName, string $productName): bool
    {
        $associations = $this->getElement('associations');

        /** @var NodeElement $product */
        foreach ($this->getAssociatedProducts($associations, $associationName) as $product) {
            if ($product->getText() === $productName) {
                return true;
            }
        }

        return false;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'associations' => '#associations',
        ]);
    }

    private function getAssociatedProducts(NodeElement $associations, string $name): array
    {
        return $associations->findAll(
            'css',
            sprintf("div:contains('%s') ul li", $name),
        );
    }
}
