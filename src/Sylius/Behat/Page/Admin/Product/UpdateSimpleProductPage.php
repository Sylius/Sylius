<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Product;

use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UpdateSimpleProductPage extends BaseUpdatePage implements UpdateSimpleProductPageInterface
{
    use ChecksCodeImmutability;

    /**
     * {@inheritdoc}
     */
    public function nameItIn($name, $localeCode)
    {
        $this->getDocument()->fillField(
            sprintf('sylius_product_translations_%s_name', $localeCode), $name
        );
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
    public function specifyPrice($price)
    {
        $this->getDocument()->fillField('Price', $price);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeValue($attribute)
    {
        $attributesTab = $this->getElement('tab', ['%name%' => 'attributes']);
        if (!$attributesTab->hasClass('active')) {
            $attributesTab->click();
        }

        return $this->getElement('attribute', ['%attribute%' => $attribute])->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttribute($attribute)
    {
        return null !== $this->getDocument()->find('css', '.attribute .label:contains("'.$attribute.'")');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'attribute' => '.attribute .label:contains("%attribute%") ~ input',
            'code' => '#sylius_product_code',
            'price' => '#sylius_product_variant_price',
            'name' => '#sylius_product_translations_en_US_name',
            'tab' => '.menu [data-tab="%name%"]',
        ]);
    }
}
