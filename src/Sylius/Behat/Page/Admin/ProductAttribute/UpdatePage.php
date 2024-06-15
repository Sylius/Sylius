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
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;

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

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add_button' => '#sylius_admin_product_attribute_configuration_choices_add',
            'choice' => '[data-test-choice-key="%key%"] input[data-test-locale="%locale_code%"]',
            'choice_direct_input' => 'input[value="%value%"][data-test-locale="%locale_code%"]',
            'choices' => '[data-test-choice-key="%key%"]',
            'code' => '[data-test-code]',
            'delete_button' => '[data-test-choice-removal="%key%"]',
            'name' => '[data-test-name]',
            'type' => '[data-test-type]',
        ]);
    }

    private function getChoicesCount(): int
    {
        return count($this->getDocument()->findAll('css', '[data-test-choices]'));
    }

    private function getLastChoiceElement(): NodeElement
    {
        $choices = $this->getDocument()->findAll('css', '[data-test-choice-key]');

        return end($choices);
    }

    private function getAttributeValues(): array
    {
        $attributeChoices = $this->getChoicesCount();
        $values = [];

        foreach ($attributeChoices as $attributeChoice) {
            $values[] = $attributeChoice->find('css', 'input')->getValue();
        }

        return $values;
    }

    private function addEmptyChoice(): void
    {
        if (null === $this->choiceListIndex) {
            $this->choiceListIndex = $this->getChoicesCount();
        }

        $this->getElement('add_button')->click();
    }
}
