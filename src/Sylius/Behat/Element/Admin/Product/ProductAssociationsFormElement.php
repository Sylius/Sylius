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

namespace Sylius\Behat\Element\Admin\Product;

use Behat\Mink\Session;
use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;

final class ProductAssociationsFormElement extends BaseFormElement implements ProductAssociationsFormElementInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        private readonly AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters);
    }

    public function associateProducts(ProductAssociationTypeInterface $productAssociationType, array $productsNames): void
    {
        $this->changeTab();
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
        $this->changeTab();
        $associationField = $this->getElement('field_associations', ['%association%' => $productAssociationType->getCode()]);

        $this->autocompleteHelper->removeByValue(
            $this->getDriver(),
            $associationField->getXpath(),
            $product->getCode(),
        );
    }

    public function hasAssociatedProduct(ProductInterface $product, ProductAssociationTypeInterface $productAssociationType): bool
    {
        $this->changeTab();
        $associationField = $this->getElement('field_associations', ['%association%' => $productAssociationType->getCode()]);

        return in_array($product->getCode(), $associationField->getValue(), true);
    }

    protected function getDefinedElements(): array
    {
        return [
            'field_associations' => '[name="sylius_admin_product[associations][%association%][]"]',
            'form' => 'form',
            'side_navigation_tab' => '[data-test-side-navigation-tab="%name%"]',
        ];
    }

    private function changeTab(): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $this->getElement('side_navigation_tab', ['%name%' => 'associations'])->click();
    }
}
