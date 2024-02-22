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

use Sylius\Behat\Service\DriverHelper;

trait FormTrait
{
    public function getDefinedFormElements(): array
    {
        return [
            'attribute_value' => '[data-test-attribute-value][data-test-locale-code="%localeCode%"][data-test-attribute-name="%attributeName%"]',
            'form' => '[data-live-name-value="SyliusAdmin.Product.Form"]',
            'field_name' => '[name="sylius_product[translations][%localeCode%][name]"]',
            'field_slug' => '[name="sylius_product[translations][%localeCode%][slug]"]',
            'generate_product_slug_button' => '[data-test-generate-product-slug-button="%localeCode%"]',
            'product_attribute_autocomplete' => '[data-test-product-attribute-autocomplete]',
            'product_attribute_delete_button' => '[data-test-product-attribute-delete-button="%attributeName%"]',
            'product_attribute_input' => 'input[name="product_attributes"]',
            'product_attribute_tab' => '[data-test-product-attribute-tab="%name%"]',
            'product_translation_accordion' => '[data-test-product-translation-accordion="%localeCode%"]',
            'side_navigation_tab' => '[data-test-side-navigation-tab="%name%"]',
        ];
    }

    /*
     * Filling fields
     */

    public function nameItIn(string $name, string $localeCode): void
    {
        $this->changeTab('translations');
        $this->expandTranslationAccordion($localeCode);
        $this->getElement('field_name', ['%localeCode%' => $localeCode])->setValue($name);

        if (DriverHelper::isJavascript($this->getDriver())) {
            $this->getElement('generate_product_slug_button', ['%localeCode%' => $localeCode])->click();
            $this->waitForFormUpdate();
        }
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

    private function expandTranslationAccordion(string $localeCode): void
    {
        $translationAccordion = $this->getElement('product_translation_accordion', ['%localeCode%' => $localeCode]);

        if ($translationAccordion->getAttribute('aria-expanded') === 'true') {
            return;
        }

        $translationAccordion->click();
    }

    /*
     * Attribute management
     */

    public function addAttribute(string $attributeName): void
    {
        $this->changeTab('attributes');
        $this->selectAttributeToBeAdded($attributeName);
        $this->clickButton('Add');

        $this->waitForFormUpdate();
    }

    public function updateAttribute(string $attributeName, string $value, string $localeCode): void
    {
        $this->changeTab('attributes');
        $this->changeAttributeTab($attributeName);

        $this
            ->getElement('attribute_value', ['%attributeName%' => $attributeName, '%localeCode%' => $localeCode])
            ->setValue($value)
        ;
    }

    public function removeAttribute(string $attributeName, string $localeCode): void
    {
        $this->changeTab('attributes');

        $this->getElement('product_attribute_delete_button', ['%attributeName%' => $attributeName])->press();

        $this->waitForFormUpdate();
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
