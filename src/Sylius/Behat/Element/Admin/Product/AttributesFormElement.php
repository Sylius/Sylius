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

final class AttributesFormElement extends BaseFormElement implements AttributesFormElementInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        private readonly AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters);
    }

    public function getAttributeValidationErrors(string $attributeName, string $localeCode): string
    {
        $this->clickTabIfItsNotActive();

        $validationError = $this->getElement('attribute')->find('css', '.sylius-validation-error');

        return $validationError->getText();
    }

    public function removeAttribute(string $attributeName, string $localeCode): void
    {
        $this->changeTab();

        $this->getElement('product_attribute_delete_button', ['%attributeName%' => $attributeName])->press();

        $this->waitForFormUpdate();
    }

    public function getAttributeSelectText(string $attribute, string $localeCode): string
    {
        $this->clickTabIfItsNotActive();

        return $this->getElement('attribute_select', ['%attributeName%' => $attribute, '%localeCode%' => $localeCode])->getText();
    }

    public function getNonTranslatableAttributeValue(string $attribute): string
    {
        $this->clickTabIfItsNotActive();

        return $this->getElement('non_translatable_attribute', ['%attributeName%' => $attribute])->getValue();
    }

    public function hasAttribute(string $attributeName): bool
    {
        return null !== $this->getDocument()->find('css', sprintf('.attribute .label:contains("%s")', $attributeName));
    }

    public function hasNonTranslatableAttributeWithValue(string $attributeName, string $value): bool
    {
        $attribute = $this->getDocument()->find('css', sprintf('.attribute .attribute-label:contains("%s")', $attributeName));

        return
            $attribute->getParent()->getParent()->find('css', '.attribute-input input')->getValue() === $value &&
            $attribute->find('css', '.globe.icon') !== null
        ;
    }

    public function addNonTranslatableAttribute(string $attributeName, string $value): void
    {
        $this->clickTabIfItsNotActive();

        $attributeOption = $this->getElement('attributes_choice')->find('css', sprintf('option:contains("%s")', $attributeName));
        $this->selectElementFromAttributesDropdown($attributeOption->getAttribute('value'));

        $this->getDocument()->pressButton('Add attributes');
        $this->waitForFormElement();

        $this->getElement('non_translatable_attribute_value', ['%attributeName%' => $attributeName])->setValue($value);
    }

    public function addAttribute(string $attributeName): void
    {
        $this->changeTab();
        $this->selectAttributeToBeAdded($attributeName);
        $this->clickButton('Add');

        $this->waitForFormUpdate();
    }

    public function updateAttribute(string $attributeName, string $value, string $localeCode): void
    {
        $this->changeTab();
        $this->changeAttributeTab($attributeName);

        $attributeValue = $this->getElement('attribute_value', ['%attributeName%' => $attributeName, '%localeCode%' => $localeCode]);

        match ($attributeValue->getTagName()) {
            'input' => $attributeValue->setValue($value),
            'select' => $attributeValue->selectOption($value),
            default => throw new \InvalidArgumentException('Unsupported attribute value type'),
        };

        $attributeValue->blur();
        $this->waitForFormUpdate();
    }

    public function getAttributeValue(string $attribute, string $localeCode): string
    {
        $this->changeTab();
        $this->changeAttributeTab($attribute);

        $attributeValue = $this->getElement('attribute_value', ['%attributeName%' => $attribute, '%localeCode%' => $localeCode]);

        return match ($attributeValue->getTagName()) {
            'input' => $attributeValue->getValue(),
            'select' => $attributeValue->getText(),
            default => throw new \InvalidArgumentException('Unsupported attribute value type'),
        };
    }

    public function addSelectedAttributes(): void
    {
        $this->changeTab();
        $this->clickButton('Add');
        $this->waitForFormUpdate();
    }

    public function getNumberOfAttributes(): int
    {
        return count($this->getDocument()->findAll('css', '[data-test-product-attribute-tab]'));
    }

    protected function getDefinedElements(): array
    {
        return [
            'product_attribute_delete_button' => '[data-test-product-attribute-delete-button="%attributeName%"]',
            'product_attribute_input' => 'input[name="product_attributes"]',
            'product_attribute_tab' => '[data-test-product-attribute-tab="%name%"]',
            'attribute_value' => '[data-test-attribute-value][data-test-locale-code="%localeCode%"][data-test-attribute-name="%attributeName%"]',
            'side_navigation_tab' => '[data-test-side-navigation-tab="%name%"]',
            'form' => '[data-live-name-value="sylius_admin:product:form"]',
        ];
    }

    private function selectAttributeToBeAdded(string $attributeName): void
    {
        $driver = $this->getDriver();
        $this->autocompleteHelper->selectByName(
            $driver,
            $this->getElement('product_attribute_input')->getXpath(),
            $attributeName,
        );
    }

    private function clickTabIfItsNotActive(): void
    {
        $attributesTab = $this->getElement('tab', ['%name%' => 'attributes']);
        if (!$attributesTab->hasClass('active')) {
            $attributesTab->click();
        }
    }

    private function changeAttributeTab(string $attributeName): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $this->getElement('product_attribute_tab', ['%name%' => $attributeName])->click();
    }

    private function clickButton(string $locator): void
    {
        if (DriverHelper::isJavascript($this->getDriver())) {
            $this->getDocument()->pressButton($locator);
        }
    }

    private function changeTab(): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $this->getElement('side_navigation_tab', ['%name%' => 'attributes'])->click();
    }
}
