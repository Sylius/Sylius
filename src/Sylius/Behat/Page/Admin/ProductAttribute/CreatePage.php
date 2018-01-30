<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\ProductAttribute;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsCode;

    /**
     * @var int
     */
    private $choiceListIndex = 0;

    /**
     * {@inheritdoc}
     */
    public function nameIt($name, $language)
    {
        $this->getDocument()->fillField(sprintf('sylius_product_attribute_translations_%s_name', $language), $name);
    }

    /**
     * {@inheritdoc}
     */
    public function isTypeDisabled()
    {
        return 'disabled' === $this->getElement('type')->getAttribute('disabled');
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function specifyMinValue(int $min): void
    {
        $this->getElement('min')->setValue($min);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyMaxValue(int $max): void
    {
        $this->getElement('max')->setValue($max);
    }

    public function checkMultiple(): void
    {
        $this->getElement('multiple')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationErrors(): string
    {
        $validationMessage = $this->getDocument()->find('css', '.sylius-validation-error');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.sylius-validation-error');
        }

        return $validationMessage->getText();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
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
        ]);
    }
}
