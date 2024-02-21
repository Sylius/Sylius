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

trait FormTrait
{
    public function getDefinedFormElements(): array
    {
        return [
            'form' => '[data-live-name-value="SyliusAdmin.Product.Form"]',
            'attribute_value' => '[data-test-attribute-value][data-test-locale-code="%localeCode%"][data-test-attribute-name="%attributeName%"]',
            'product_attribute_autocomplete' => '[data-test-product-attribute-autocomplete]',
            'product_attribute_input' => 'input[name="product_attributes"]',
            'product_attribute_tab' => '[data-test-product-attribute-tab="%name%"]',
            'side_navigation_tab' => '[data-test-side-navigation-tab="%name%"]',
        ];
    }

    /*
     * Tabs management
     */

    private function changeTab(string $tabName): void
    {
        $this->getElement('side_navigation_tab', ['%name%' => $tabName])->click();
    }

    private function changeAttributeTab(string $attributeName): void
    {
        $this->getElement('product_attribute_tab', ['%name%' => $attributeName])->click();
    }

    /*
     * Attribute management
     */

    public function addAttribute(string $attributeName, string $value, string $localeCode): void
    {
        $this->changeTab('attributes');
        $this->selectAttributeToBeAdded($attributeName);
        $this->clickButton('Add');

        $this->waitForFormUpdate();

        if ('' === $value) {
            return;
        }

        $this
            ->getElement('attribute_value', ['%attributeName%' => $attributeName, '%localeCode%' => $localeCode])
            ->setValue($value)
        ;
    }

    public function getAttributeValue(string $attribute, string $localeCode): string
    {
        $this->changeTab('attributes');
        $this->changeAttributeTab($attribute);

        return $this
            ->getElement('attribute_value', ['%attributeName%' => $attribute, '%localeCode%' => $localeCode])
            ->getValue()
        ;
    }

    private function selectAttributeToBeAdded(string $attributeName): void
    {
        $driver = $this->getDriver();
        $this->autocompleteHelper->select(
            $driver,
            $this->getElement('product_attribute_input')->getXpath(),
            $attributeName,
        );
    }

    /*
     * Helpers
     */

    private function waitForFormUpdate(): void
    {
        $form = $this->getElement('form');
        $form->waitFor(5, fn () => !$form->hasAttribute('busy'));
    }

    private function clickButton(string $locator): void
    {
        $this->getDocument()->pressButton($locator);
    }
}
