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

namespace Sylius\Behat\Element\Admin\ProductAttribute;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;

class FormElement extends BaseFormElement implements FormElementInterface
{
    use ChecksCodeImmutability;
    use SpecifiesItsField;

    private ?int $choiceListIndex = null;

    public function changeName(string $name, string $language): void
    {
        $this->getDocument()->fillField(sprintf('sylius_admin_product_attribute_translations_%s_name', $language), $name);
    }

    public function isTypeDisabled(): bool
    {
        return 'disabled' === $this->getElement('type')->getAttribute('disabled');
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    public function changeAttributeValue(string $oldValue, string $newValue, string $localeCode = 'en_US'): void
    {
        $this->getElement('choice_direct_input', ['%value%' => $oldValue, '%locale_code%' => $localeCode])->setValue($newValue);
    }

    public function hasAttributeValue(string $value, string $localeCode = 'en_US'): bool
    {
        return $this->hasElement('choice_direct_input', ['%value%' => $value, '%locale_code%' => $localeCode]);
    }

    public function addAttributeValue(string $value, string $localeCode): void
    {
        $this->addEmptyChoice();
        $this->waitForFormUpdate();
        $lastChoice = $this->getLastChoiceElement();
        $lastChoice->find('css', 'input[data-test-locale="' . $localeCode . '"]')->setValue($value);

        ++$this->choiceListIndex;
    }

    public function deleteAttributeValue(string $value, string $localeCode): void
    {
        if (null === $this->choiceListIndex) {
            $this->choiceListIndex = $this->getChoicesCount();
        }

        $input = $this->getElement('choice_direct_input', ['%value%' => $value, '%locale_code%' => $localeCode]);
        $this->getElement('delete_button', ['%key%' => $input->getAttribute('data-test-key')])->click();
        $this->waitForFormUpdate();
    }

    public function nameIt(string $name, string $language): void
    {
        $this->getDocument()->fillField(sprintf('sylius_admin_product_attribute_translations_%s_name', $language), $name);
    }

    public function disableTranslatability(): void
    {
        $this->getElement('translatable')->uncheck();
    }

    public function specifyMinValue(int $min): void
    {
        $this->getElement('min')->setValue($min);
    }

    public function specifyMaxValue(int $max): void
    {
        $this->getElement('max')->setValue($max);
    }

    public function checkMultiple(): void
    {
        $this->getElement('multiple')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add_button' => '#sylius_admin_product_attribute_configuration_choices_add',
            'choice' => '[data-test-choice-key="%key%"] input[data-test-locale="%locale_code%"]',
            'choice_direct_input' => 'input[value="%value%"][data-test-locale="%locale_code%"]',
            'choices' => '[data-test-choice-key]',
            'code' => '[data-test-code]',
            'delete_button' => '[data-test-choice-removal="%key%"]',
            'max' => '#sylius_admin_product_attribute_configuration_max',
            'min' => '#sylius_admin_product_attribute_configuration_min',
            'multiple' => 'label[for=sylius_admin_product_attribute_configuration_multiple]',
            'name' => '[data-test-name]',
            'translatable' => '[data-test-translatable]',
            'type' => '[data-test-type]',
        ]);
    }

    private function getChoicesCount(): int
    {
        return count($this->getDocument()->findAll('css', '[data-test-choices]'));
    }

    private function addEmptyChoice(): void
    {
        if (null === $this->choiceListIndex) {
            $this->choiceListIndex = $this->getChoicesCount();
        }

        $this->getElement('add_button')->click();
    }

    private function getLastChoiceElement(): NodeElement
    {
        $choices = $this->getDocument()->findAll('css', '[data-test-choice-key]');

        if (empty($choices)) {
            throw new ElementNotFoundException(
                $this->getSession(),
                'Last choice element',
                'css',
                '[data-test-choice-key]'
            );
        }

        return end($choices);
    }
}
