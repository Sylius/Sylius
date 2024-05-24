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

namespace Sylius\Behat\Page\Admin\Product\Common;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;

trait ProductAssociationsTrait
{
    public function getDefinedProductAssociationsElements(): array
    {
        return [
            'field_associations' => '[name="sylius_admin_product[associations][%association%][]"]',
        ];
    }

    public function associateProducts(ProductAssociationTypeInterface $productAssociationType, array $productsNames): void
    {
        $this->changeTab('associations');
        $associationField = $this->getElement('field_associations', ['%association%' => $productAssociationType->getCode()]);

        foreach ($productsNames as $productName) {
            $this->autocompleteHelper->selectByName(
                $this->getDriver(),
                $associationField->getXpath(),
                $productName,
            );
            $this->waitForFormUpdate();
        }
    }

    public function removeAssociatedProduct(ProductInterface $product, ProductAssociationTypeInterface $productAssociationType): void
    {
        $this->changeTab('associations');
        $associationField = $this->getElement('field_associations', ['%association%' => $productAssociationType->getCode()]);

        $this->autocompleteHelper->removeByValue(
            $this->getDriver(),
            $associationField->getXpath(),
            $product->getCode(),
        );
    }

    public function hasAssociatedProduct(ProductInterface $product, ProductAssociationTypeInterface $productAssociationType): bool
    {
        $this->changeTab('associations');
        $associationField = $this->getElement('field_associations', ['%association%' => $productAssociationType->getCode()]);

        return in_array($product->getCode(), $associationField->getValue(), true);
    }
}
