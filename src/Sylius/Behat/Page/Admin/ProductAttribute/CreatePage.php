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

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsField;

    private int $choiceListIndex = 0;

    public function nameIt(string $name, string $language): void
    {
        $this->getDocument()->fillField(sprintf('sylius_product_attribute_translations_%s_name', $language), $name);
    }

    public function isTypeDisabled(): bool
    {
        return 'disabled' === $this->getElement('type')->getAttribute('disabled');
    }

    public function disableTranslation(): void
    {
        $this->getElement('translation')->uncheck();
    }

    public function addAttributeValue(string $value, string $localeCode): void
    {
        $this->getDocument()->clickLink('Add');
        $this
            ->getElement('attribute_choice_list_element', [
                '%index%' => $this->choiceListIndex,
                '%localeCode%' => $localeCode,
            ])
            ->setValue($value)
        ;
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
        $validationMessage = $this->getDocument()->find('css', '.sylius-validation-error');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.sylius-validation-error');
        }

        return $validationMessage->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'attribute_choice_list' => 'div[data-form-collection="list"]',
            'attribute_choice_list_element' => '#sylius_product_attribute_configuration_choices_%index%_%localeCode%',
            'code' => '#sylius_product_attribute_code',
            'max' => '#sylius_product_attribute_configuration_max',
            'min' => '#sylius_product_attribute_configuration_min',
            'multiple' => 'label[for=sylius_product_attribute_configuration_multiple]',
            'name' => '#sylius_product_attribute_translations_en_US_name',
            'type' => '#sylius_product_attribute_type',
            'translation' => '#sylius_product_attribute_translatable',
        ]);
    }
}
