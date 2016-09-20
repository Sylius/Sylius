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
use Webmozart\Assert\Assert;

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
        $this->clickAttributesTabIfItsNotActive();

        $attributeOption = $this->getElement('attributes_choice')->find('css', sprintf('option:contains("%s")', $attribute));
        $this->selectElementFromAttributesDropdown($attributeOption->getAttribute('value'));

        $this->getDocument()->pressButton('Add attributes');
        $this->waitForFormElement();

        $this->getElement('attribute_value', ['%attribute%' => $attribute])->setValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function removeAttribute($attribute)
    {
        $this->clickAttributesTabIfItsNotActive();

        $this->getElement('attribute_delete_button', ['%attribute%' => $attribute])->press();
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
            'attribute_delete_button' => '.attribute .label:contains("%attribute%") ~ button',
            'attribute_value' => '.attribute .label:contains("%attribute%") ~ input',
            'attributes_choice' => 'select[name="sylius_product_attribute_choice"]',
            'code' => '#sylius_product_code',
            'form' => 'form[name="sylius_product"]',
            'name' => '#sylius_product_translations_en_US_name',
            'price' => '#sylius_product_variant_price',
            'tab' => '.menu [data-tab="%name%"]',
        ]);
    }

    /**
     * @param int $id
     */
    private function selectElementFromAttributesDropdown($id)
    {
        /** @var Selenium2Driver $driver */
        $driver = $this->getDriver();
        Assert::isInstanceOf($driver, Selenium2Driver::class);

        $driver->executeScript('$(\'[name="sylius_product_attribute_choice"]\').dropdown(\'show\');');
        $driver->executeScript(sprintf('$(\'[name="sylius_product_attribute_choice"]\').dropdown(\'set selected\', %s);', $id));
    }

    /**
     * @param int $timeout
     */
    private function waitForFormElement($timeout = 5)
    {
        $form = $this->getElement('form');
        $this->getDocument()->waitFor($timeout, function () use ($form) {
            return false === strpos($form->getAttribute('class'), 'loading');
        });
    }

    private function clickAttributesTabIfItsNotActive()
    {
        $attributesTab = $this->getElement('tab', ['%name%' => 'attributes']);
        if (!$attributesTab->hasClass('active')) {
            $attributesTab->click();
        }
    }
}
