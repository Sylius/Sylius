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

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;

class FormElement extends BaseFormElement implements FormElementInterface
{
    use SpecifiesItsField;
    use ChecksCodeImmutability;

    public function nameItIn(string $name, string $localeCode): void
    {
        $this->getElement('name', ['%locale_code%' => $localeCode])->setValue($name);
    }

    public function addOptionValue(string $code, string $localeCode, string $value): void
    {
        $this->getElement('add_value')->press();
        $this->waitForFormUpdate();

        $lastValue = $this->getElement('last_value');
        $lastValue->fillField('Code', $code);
        $lastValue->find('css', sprintf('[id$="_translations_%s_value"]', $localeCode))->setValue($value);
        $this->waitForFormUpdate();
    }

    public function isThereOptionValue(string $optionValue, string $localeCode): bool
    {
        return $this->getElement('values')->has('css', sprintf('input[id$="_translations_%s_value"][value="%s"]', $localeCode, $optionValue));
    }

    public function applyToAllOptionValues(string $code, string $localeCode): void
    {
        $this->getElement('apply_to_all_value', ['%value_code%' => $code, '%locale_code%' => $localeCode])->click();
        $this->waitForFormUpdate();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add_value' => '[data-test-add-value]',
            'apply_to_all_value' => '[data-test-value="%value_code%"] [data-test-value-locale="%locale_code%"] [data-test-apply-to-all]',
            'code' => '[data-test-code]',
            'form' => '[data-live-name-value="sylius_admin:product_option:form"]',
            'last_value' => '[data-test-values] [data-test-value]:last-child',
            'name' => '#sylius_admin_product_option_translations_%locale_code%_name',
            'values' => '[data-test-values]',
        ]);
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }
}
