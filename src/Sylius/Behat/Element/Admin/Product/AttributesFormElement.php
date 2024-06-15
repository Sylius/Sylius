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

    public function addAttribute(string $attributeName): void
    {
        $this->changeTab();
        $this->selectAttributeToBeAdded($attributeName);
        $this->clickButton('Add');

        $this->waitForFormUpdate();
    }

    public function addSelectedAttributes(): void
    {
        $this->changeTab();
        $this->clickButton('Add');
        $this->waitForFormUpdate();
    }

    public function updateAttribute(string $attributeName, string $value, string $localeCode): void
    {
        $this->changeTab();
        $this->changeAttributeTab($attributeName);

        $attributeValue = $this->getElement('attribute_value', ['%attribute_name%' => $attributeName, '%locale_code%' => $localeCode]);

        match ($attributeValue->getTagName()) {
            'input' => $attributeValue->setValue($value),
            'select' => $attributeValue->selectOption($value),
            default => throw new \InvalidArgumentException('Unsupported attribute value type'),
        };

        $attributeValue->blur();
        $this->waitForFormUpdate();
    }

    public function removeAttribute(string $attributeName): void
    {
        $this->changeTab();

        $this->getElement('attribute_delete_button', ['%attribute_name%' => $attributeName])->press();

        $this->waitForFormUpdate();
    }

    public function hasAttribute(string $attributeName): bool
    {
        $this->changeTab();

        return $this->hasElement('attribute_tab', ['%name%' => $attributeName]);
    }

    public function getNumberOfAttributes(): int
    {
        return count($this->getDocument()->findAll('css', '[data-test-attribute-tab]'));
    }

    public function getAttributeValue(string $attributeName, string $localeCode): string
    {
        $this->changeTab();
        $this->changeAttributeTab($attributeName);

        $attributeValue = $this->getElement('attribute_value', ['%attribute_name%' => $attributeName, '%locale_code%' => $localeCode]);

        return match ($attributeValue->getTagName()) {
            'input' => $attributeValue->getValue(),
            'select' => $attributeValue->getText(),
            default => throw new \InvalidArgumentException('Unsupported attribute value type'),
        };
    }

    public function getAttributeSelectText(string $attributeName, string $localeCode): string
    {
        $this->clickTabIfItsNotActive();

        return $this->getElement('attribute_select', ['%attribute_name%' => $attributeName, '%locale_code%' => $localeCode])->getText();
    }

    public function getValueNonTranslatableAttribute(string $attributeName): string
    {
        $this->changeTab();
        $this->changeAttributeTab($attributeName);

        return $this->getElement('attribute_input', ['%name%' => $attributeName])->getValue();
    }

    public function getAttributeValidationErrors(string $attributeName, string $localeCode): string
    {
        $this->changeTab();
        $this->changeAttributeTab($attributeName);

        return $this->getValidationMessage('attribute_value', ['%attribute_name%' => $attributeName, '%locale_code%' => $localeCode]);
    }

    public function hasAttributeError(string $attributeName, string $localeCode): bool
    {
        $this->changeTab();
        $this->changeAttributeTab($attributeName);

        $attributeValue = $this->getElement('attribute_value', ['%attribute_name%' => $attributeName, '%locale_code%' => $localeCode]);

        return $attributeValue->hasClass('is-invalid');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(
            parent::getDefinedElements(), [
            'attribute_add_button' => '[data-test-attribute-add-button]',
            'attribute_autocomplete' => '[data-test-attribute-autocomplete] input[name="product_attributes"]',
            'attribute_delete_button' => '[data-test-attribute-delete-button="%attribute_name%"]',
            'attribute_input' => '[data-test-attribute-name="%name%"]',
            'attribute_tab' => '[data-test-attribute-tab="%name%"]',
            'attribute_value' => '[data-test-attribute-value][data-test-locale-code="%locale_code%"][data-test-attribute-name="%attribute_name%"]',
            'side_navigation_tab' => '[data-test-side-navigation-tab="%name%"]',
        ]);
    }

    private function selectAttributeToBeAdded(string $attributeName): void
    {
        $this->autocompleteHelper->selectByName(
            $this->getDriver(),
            $this->getElement('attribute_autocomplete')->getXpath(),
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

    private function clickButton(string $locator): void
    {
        $this->getElement('attribute_add_button')->click();
    }

    private function changeTab(): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $this->getElement('side_navigation_tab', ['%name%' => 'attributes'])->click();
    }

    private function changeAttributeTab(string $attributeName): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $this->getElement('attribute_tab', ['%name%' => $attributeName])->click();
    }
}
