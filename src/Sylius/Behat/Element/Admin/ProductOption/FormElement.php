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
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;
use Webmozart\Assert\Assert;

class FormElement extends BaseFormElement implements FormElementInterface
{
    use NamesIt;
    use SpecifiesItsField;
    use ChecksCodeImmutability;

    public function addOptionValue(string $code, string $value): void
    {
        $count = count($this->getProductOptionValues());

        $this->getElement('add_value')->click();
        $this->getDocument()->waitFor(5, fn () => $count + 1 === count($this->getProductOptionValues()));

        $optionValueForm = $this->getLastOptionValueElement();

        $optionValueForm->fillField('Code', $code);

        $optionValueForm->find('css', '[data-test-option-value-translation]')->setValue($value);
    }

    public function removeOptionValue(string $optionValue): void
    {
        $count = count($this->getProductOptionValues());

        $optionValues = $this->getProductOptionValues();

        foreach ($optionValues as $optionValueElement) {
            if ($optionValueElement->has('css', sprintf('input[value="%s"]', $optionValue))) {
                $optionValueElement->find('css', '[data-test-delete-value]')->click();

                $this->getDocument()->waitFor(5, fn () => $count - 1 === count($this->getProductOptionValues()));

                return;
            }
        }

        throw new ElementNotFoundException($this->getSession(), 'option value', 'css', sprintf('input[value="%s"]', $optionValue));
    }

    public function nameItIn(string $name, string $localeCode): void
    {
        $this->getElement('name', ['%localeCode%' => $localeCode])->setValue($name);
    }

    public function isThereOptionValue(string $optionValue): bool
    {
        $optionValues = $this->getElement('values');

        return $optionValues->has('css', sprintf('input[value="%s"]', $optionValue));
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add_value' => '[data-test-add-value]',
            'code' => '[data-test-code]',
            'name' => '#sylius_admin_product_option_translations_%localeCode%_name',
            'product_option_value' => '[data-test-product-option-value]',
            'values' => '[data-test-values]',
        ]);
    }

    private function getLastOptionValueElement(): NodeElement
    {
        $values = $this->getProductOptionValues();

        return end($values);
    }

    private function getProductOptionValues(): array
    {
        $items = $this->getElement('values')->findAll('css', '[data-test-product-option-value]');
        Assert::isArray($items);

        return $items;
    }
}
