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
use Webmozart\Assert\Assert;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;

    public function changeName(string $name, string $language): void
    {
        $this->getDocument()->fillField(sprintf('sylius_product_attribute_translations_%s_name', $language), $name);
    }

    public function isTypeDisabled(): bool
    {
        return 'disabled' === $this->getElement('type')->getAttribute('disabled');
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    public function changeAttributeValue(string $oldValue, string $newValue): void
    {
        $this->getElement('attribute_choice_list_element', ['%value%' => $oldValue])->setValue($newValue);
    }

    public function hasAttributeValue(string $value): bool
    {
        return $this->hasElement('attribute_choice_list_element', ['%value%' => $value]);
    }

    public function addAttributeValue(string $value, string $localeCode): void
    {
        $this->getDocument()->clickLink('Add');
        $this
            ->getLastAttributeChoiceElement()
            ->find('css', 'div[data-locale="' . $localeCode . '"] input')
            ->setValue($value)
        ;
    }

    public function deleteAttributeValue(string $value): void
    {
        $attributeChoiceElement = $this
            ->getElement('attribute_choice_list_element', ['%value%' => $value])
            ->getParent()->getParent()->getParent()->getParent()
        ;
        $attributeChoiceElement->clickLink('Delete');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'attribute_choice_list_element' => 'input[value="%value%"]',
            'attribute_choices' => '#sylius_product_attribute_configuration_choices',
            'code' => '#sylius_product_attribute_code',
            'type' => '#sylius_product_attribute_type',
            'name' => '#sylius_product_attribute_translations_en_US_name',
        ]);
    }

    /**
     * @return NodeElement[]
     */
    private function getAttributeChoiceElements(): array
    {
        $attributeChoices = $this->getElement('attribute_choices');

        return $attributeChoices->findAll('css', 'div[data-form-collection="item"]');
    }

    private function getLastAttributeChoiceElement(): NodeElement
    {
        $elements = $this->getAttributeChoiceElements();

        Assert::notEmpty($elements);

        return end($elements);
    }
}
