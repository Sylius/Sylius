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

namespace Sylius\Behat\Page\Admin\ProductVariant;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    public function specifyPrice(int $price): void
    {
        $this->getDocument()->fillField('Price', $price);
    }

    public function disableTracking(): void
    {
        $this->getElement('tracked')->uncheck();
    }

    public function enableTracking(): void
    {
        $this->getElement('tracked')->check();
    }

    public function isTracked(): bool
    {
        return $this->getElement('tracked')->isChecked();
    }

    public function getPricingConfigurationForChannelAndCurrencyCalculator(ChannelInterface $channel, CurrencyInterface $currency): string
    {
        $priceElement = $this->getElement('pricing_configuration')->find('css', sprintf('label:contains("%s %s")', $channel->getCode(), $currency->getCode()))->getParent();

        return $priceElement->find('css', 'input')->getValue();
    }

    public function getPriceForChannel(string $channelName): string
    {
        return $this->getElement('price', ['%channelName%' => $channelName])->getValue();
    }

    public function getOriginalPriceForChannel(string $channelName): string
    {
        return $this->getElement('original_price', ['%channelName%' => $channelName])->getValue();
    }

    public function getNameInLanguage(string $language): string
    {
        return $this->getElement('name', ['%language%' => $language])->getValue();
    }

    public function specifyCurrentStock(int $amount): void
    {
        $this->getElement('on_hand')->setValue($amount);
    }

    public function isShippingRequired(): bool
    {
        return $this->getElement('shipping_required')->isChecked();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_product_variant_code',
            'name' => '#sylius_product_variant_translations_%language%_name',
            'on_hand' => '#sylius_product_variant_onHand',
            'original_price' => '#sylius_product_variant_channelPricings > .field:contains("%channelName%") input[name$="[originalPrice]"]',
            'price' => '#sylius_product_variant_channelPricings > .field:contains("%channelName%") input[name$="[price]"]',
            'pricing_configuration' => '#sylius_calculator_container',
            'shipping_required' => '#sylius_product_variant_shippingRequired',
            'tracked' => '#sylius_product_variant_tracked',
        ]);
    }
}
