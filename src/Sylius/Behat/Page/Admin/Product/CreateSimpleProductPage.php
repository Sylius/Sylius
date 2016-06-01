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

use Behat\Mink\Driver\Selenium2Driver;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CreateSimpleProductPage extends BaseCreatePage implements CreateSimpleProductPageInterface
{
    use SpecifiesItsCode;

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
    public function specifyPrice($price)
    {
        $this->getDocument()->fillField('Price', $price);
    }

    /**
     * {@inheritdoc}
     */
    public function addAttribute($attribute, $value)
    {
        $attributesTab = $this->getElement('tab', ['%name%' => 'attributes']);
        if (!$attributesTab->hasClass('active')) {
            $attributesTab->click();
        }

        $attributeOption = $this->getElement('attributes-choice')->find('css', 'option:contains("'.$attribute.'")');
        $id = $attributeOption->getAttribute('value');

        /** @var Selenium2Driver $driver */
        $driver = $this->getDriver();
        $driver->executeScript('$(\'[name="sylius_product_attribute_choice"]\').dropdown(\'show\');');
        $driver->executeScript('$(\'[name="sylius_product_attribute_choice"]\').dropdown(\'set selected\', '.$id.');');

        $this->getElement('add-attributes-button')->press();
        $this->waitWhileFormIsLoading();

        $this->getElement('attribute-value', ['%attribute%' => $attribute])->setValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return parent::getRouteName() . '_simple';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'add-attributes-button' => 'button:contains("Add attributes")',
            'attribute-value' => '.attribute:contains("%attribute%") input',
            'attributes-choice' => 'select[name="sylius_product_attribute_choice"]',
            'code' => '#sylius_product_code',
            'form' => 'form[name="sylius_product"]',
            'name' => '#sylius_product_translations_en_US_name',
            'price' => '#sylius_product_variant_price',
            'tab' => '.menu [data-tab="%name%"]',
        ]);
    }

    private function waitWhileFormIsLoading()
    {
        $form = $this->getElement('form');
        $this->getDocument()->waitFor(5, function () use ($form) {
            return false === strpos($form->getAttribute('class'), 'loading');
        });
    }
}
