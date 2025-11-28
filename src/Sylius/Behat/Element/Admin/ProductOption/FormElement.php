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

namespace Sylius\Behat\Element\Admin\ProductOption;

use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;
use Sylius\Behat\Element\NodeElement;

class FormElement extends BaseFormElement implements FormElementInterface
{
    use SpecifiesItsField;
    use ChecksCodeImmutability;

    public function setName(string $name, string $localeCode): void
    {
        $this->getElement('name', ['%locale_code%' => $localeCode])->setValue($name);
    }

    public function removeOptionValue(string $optionValue): void
    {
        $optionValues = $this->getDocument()->findAll('css', '[data-test-option-value]');
        foreach ($optionValues as $optionValueElement) {
            if ($optionValueElement->has('css', sprintf('input[value="%s"]', $optionValue))) {
                $optionValueElement->find('css', '[data-test-delete-option-value]')->click();
            }
        }
    }

    public function addOptionValue(string $code, string $localeCode, string $value): void
    {
        $this->getElement('add_option_value')->click();

        $lastValue = $this->getElement('last_option_value');
        $lastValue->find('css', '[data-test-code]')->setValue($code);
        $lastValue->find('css', sprintf('[id$="_translations_%s_value"]', $localeCode))->setValue($value);
    }

    public function hasOptionValue(string $optionValue, string $localeCode): bool
    {
        return $this->hasElement('option_value', ['%option_value%' => $optionValue, '%locale_code%' => $localeCode]);
    }

    public function applyToAllOptionValues(string $code, string $localeCode): void
    {
        $this->getElement('apply_to_all', ['%value_code%' => $code, '%locale_code%' => $localeCode])->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add_option_value' => '[data-test-add-option-value]',
            'apply_to_all' => '[data-test-option-value="%value_code%"] [data-test-option-value-locale="%locale_code%"] [data-test-apply-to-all]',
            'code' => '[data-test-code]',
            'delete_option_value' => '[data-test-delete-option-value]',
            'form' => '[data-live-name-value="sylius_admin:product_option:form"]',
            'last_option_value' => '[data-test-option-values] [data-test-option-value]:last-child',
            'name' => '#sylius_admin_product_option_translations_%locale_code%_name',
            'option_value' => '[data-test-option-values] input[id$="_translations_%locale_code%_value"][value="%option_value%"]',
        ]);
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }
}
