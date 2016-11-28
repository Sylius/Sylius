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

use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;

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

    public function disableTracking()
    {
        $this->getElement('tracked')->uncheck();
    }

    public function enableTracking()
    {
        $this->getElement('tracked')->check();
    }

    /**
     * {@inheritdoc}
     */
    public function isTracked()
    {
        return $this->getElement('tracked')->isChecked();
    }

    /**
     * {@inheritdoc}
     */
    public function getPricingConfigurationForChannelAndCurrencyCalculator(ChannelInterface $channel, CurrencyInterface $currency)
    {
        $priceElement = $this->getElement('pricing_configuration')->find('css', sprintf('label:contains("%s %s")', $channel->getCode(), $currency->getCode()))->getParent();

        return $priceElement->find('css', 'input')->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceForChannel($channelName)
    {
        return $this->getElement('price', ['%channel%' => $channelName])->getValue();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_product_variant_code',
            'price' => '#sylius_product_variant_channelPricings [data-form-collection="item"]:contains("%channel%") input',
            'pricing_configuration' => '#sylius_calculator_container',
            'tracked' => '#sylius_product_variant_tracked',
        ]);
    }
}
