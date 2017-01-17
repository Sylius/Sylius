<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ProductOption;

use Behat\Mink\Element\Element;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;

    /**
     * {@inheritdoc}
     */
    public function nameItIn($name, $language)
    {
        $this->getDocument()->fillField(
            sprintf('sylius_product_option_translations_%s_name', $language), $name
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isThereOptionValue($optionValue)
    {
        $optionValues = $this->getElement('values');

        return $optionValues->has('css', sprintf('input[value="%s"]', $optionValue));
    }

    /**
     * {@inheritdoc}
     */
    public function addOptionValue($code, $value)
    {
        $this->getDocument()->clickLink('Add value');

        $optionValueForm = $this->getLastOptionValueElement();

        $optionValueForm->fillField('Code', $code);
        $optionValueForm->fillField('English (United States)', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function removeOptionValue($optionValue)
    {
        if ($this->isThereOptionValue($optionValue)) {
            $optionValues = $this->getElement('values');

            $item = $optionValues
                ->find('css', sprintf('div[data-form-collection="item"] input[value="%s"]', $optionValue))
                ->getParent()
                ->getParent()
                ->getParent()
                ->getParent()
            ;
            $item->clickLink('Delete');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getCodeElement()
    {
        return $this->getElement('code');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_product_option_code',
            'name' => '#sylius_product_option_translations_en_US_name',
            'values' => '#sylius_product_option_values',
        ]);
    }

    /**
     * @return Element
     */
    private function getLastOptionValueElement()
    {
        $optionValues = $this->getElement('values');
        $items = $optionValues->findAll('css', 'div[data-form-collection="item"]');

        Assert::notEmpty($items);

        return end($items);
    }
}
