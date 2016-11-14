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
    public function specifyPrice($price)
    {
        $this->getDocument()->fillField('Price', $price);
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
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'calculator' => '#sylius_calculator_container',
            'code' => '#sylius_product_variant_code',
            'option_select' => '#sylius_product_variant_optionValues_%option-name%',
            'price' => '#sylius_product_variant_price',
            'price_calculator' => '#sylius_product_variant_pricingCalculator',
        ]);
    }
}
