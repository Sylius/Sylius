<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ProductVariant;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsCode;

    /**
     * {@inheritdoc}
     */
    public function specifyPrice($price, $channel)
    {
        $this->getDocument()->fillField($channel, $price);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyCurrentStock($currentStock)
    {
        $this->getDocument()->fillField('Current stock', $currentStock);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyHeightWidthDepthAndWeight($height, $width, $depth, $weight)
    {
        $this->getDocument()->fillField('Height', $height);
        $this->getDocument()->fillField('Width', $width);
        $this->getDocument()->fillField('Depth', $depth);
        $this->getDocument()->fillField('Weight', $weight);
    }

    /**
     * {@inheritdoc}
     */
    public function nameIt($name)
    {
        $this->getDocument()->fillField('Name', $name);
    }

    /**
     * {@inheritdoc}
     */
    public function selectOption($optionName, $optionValue)
    {
        $optionName = strtoupper($optionName);
        $this->getElement('option_select', ['%option-name%' => $optionName])->selectOption($optionValue);
    }

    /**
     * {@inheritdoc}
     */
    public function choosePricingCalculator($name)
    {
        $this->getElement('price_calculator')->selectOption($name);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyPriceForChannelAndCurrency($price, ChannelInterface $channel, CurrencyInterface $currency)
    {
        $calculatorElement = $this->getElement('calculator');
        $calculatorElement
            ->waitFor(5, function () use ($channel, $currency) {
                return $this->getElement('calculator')->hasField(sprintf('%s %s', $channel->getName(), $currency->getCode()));
            })
        ;

        $calculatorElement->fillField(sprintf('%s %s', $channel->getName(), $currency->getCode()), $price);
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationMessageForForm()
    {
        $formElement = $this->getDocument()->find('css', 'form[name="sylius_product_variant"]');
        if (null === $formElement) {
            throw new ElementNotFoundException($this->getSession(), 'Field element');
        }

        $validationMessage = $formElement->find('css', '.sylius-validation-error');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.sylius-validation-error');
        }

        return $validationMessage->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function selectShippingCategory($shippingCategoryName)
    {
        $this->getElement('shipping_category')->selectOption($shippingCategoryName);
    }

    /**
     * {@inheritdoc}
     */
    public function getPricesValidationMessage()
    {
        return $this->getElement('prices_validation_message')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstPriceValidationMessage()
    {
        return $this->getElement('first_price_validation_message')->getText();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'calculator' => '#sylius_calculator_container',
            'code' => '#sylius_product_variant_code',
            'depth' => '#sylius_product_variant_depth',
            'form' => 'form[name="sylius_product_variant"]',
            'height' => '#sylius_product_variant_height',
            'on_hand' => '#sylius_product_variant_onHand',
            'option_select' => '#sylius_product_variant_optionValues_%option-name%',
            'price_calculator' => '#sylius_product_variant_pricingCalculator',
            'shipping_category' => '#sylius_product_variant_shippingCategory',
            'prices_validation_message' => '#sylius_product_variant_channelPricings ~ .sylius-validation-error',
            'first_price_validation_message' => '#sylius_product_variant_channelPricings_0 .sylius-validation-error',
            'weight' => '#sylius_product_variant_weight',
            'width' => '#sylius_product_variant_width',
        ]);
    }
}
