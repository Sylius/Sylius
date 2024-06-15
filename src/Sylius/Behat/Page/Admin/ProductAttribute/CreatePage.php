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

namespace Sylius\Behat\Page\Admin\ProductAttribute;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsField;

    private ?int $choiceListIndex = null;

    public function nameIt(string $name, string $language): void
    {
        $this->getDocument()->fillField(sprintf('sylius_admin_product_attribute_translations_%s_name', $language), $name);
    }

    public function isTypeDisabled(): bool
    {
        return 'disabled' === $this->getElement('type')->getAttribute('disabled');
    }

    public function disableTranslatability(): void
    {
        $this->getElement('translatable')->uncheck();
    }

    public function addAttributeValue(string $value, string $localeCode): void
    {
        $this->addEmptyChoice();
        $this->waitForFormUpdate();
        $lastChoice = $this->getLastChoiceElement();
        $lastChoice->find('css', 'input[data-test-locale="' . $localeCode . '"]')->setValue($value);

        ++$this->choiceListIndex;
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

    public function getValidationErrors(): string
    {
        $validationMessage = $this->getDocument()->find('css', '.sylius-validation-error, .alert.alert-danger');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.sylius-validation-error, .alert.alert-danger');
        }

        return $validationMessage->getText();
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

    private function addEmptyChoice(): int
    {
        if (null === $this->choiceListIndex) {
            $this->choiceListIndex = $this->getChoicesCount();
        }

        $this->getElement('add_button')->click();

        return $this->choiceListIndex;
    }

    private function getChoicesCount(): int
    {
        return count($this->getDocument()->findAll('css', '[data-test-choice-key]'));
    }
}
