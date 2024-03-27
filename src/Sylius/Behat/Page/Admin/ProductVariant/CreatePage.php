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

namespace Sylius\Behat\Page\Admin\ProductVariant;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Component\Core\Model\ChannelInterface;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsField;

    public function specifyPrice(string $price, ChannelInterface $channel): void
    {
        $this->getElement('price', ['%channelCode%' => $channel->getCode()])->setValue($price);
    }

    public function specifyMinimumPrice(string $price, ChannelInterface $channel): void
    {
        $this->getElement('minimum_price', ['%channelCode%' => $channel->getCode()])->setValue($price);
    }

    public function specifyOriginalPrice(string $originalPrice, ChannelInterface $channel): void
    {
        $this->getElement('original_price', ['%channelCode%' => $channel->getCode()])->setValue($originalPrice);
    }

    public function specifyCurrentStock(string $currentStock): void
    {
        $this->getDocument()->fillField('Current stock', $currentStock);
    }

    public function specifyHeightWidthDepthAndWeight(string $height, string $width, string $depth, string $weight): void
    {
        $this->getDocument()->fillField('Height', $height);
        $this->getDocument()->fillField('Width', $width);
        $this->getDocument()->fillField('Depth', $depth);
        $this->getDocument()->fillField('Weight', $weight);
    }

    public function nameItIn(string $name, string $language): void
    {
        $this->getDocument()->fillField(
            sprintf('sylius_product_variant_translations_%s_name', $language),
            $name,
        );
    }

    public function selectOption(string $optionName, string $optionValue): void
    {
        $optionName = strtoupper($optionName);
        $this->getElement('option_select', ['%option-name%' => $optionName])->selectOption($optionValue);
    }

    public function choosePricingCalculator(string $name): void
    {
        $this->getElement('price_calculator')->selectOption($name);
    }

    public function getValidationMessageForForm(): string
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

    public function selectShippingCategory(string $shippingCategoryName): void
    {
        $this->getElement('shipping_category')->selectOption($shippingCategoryName);
    }

    public function getPricesValidationMessage(): string
    {
        return $this->getElement('prices_validation_message')->getText();
    }

    public function setShippingRequired(bool $isShippingRequired): void
    {
        if ($isShippingRequired) {
            $this->getElement('shipping_required')->check();

            return;
        }

        $this->getElement('shipping_required')->uncheck();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_product_variant_code',
            'depth' => '#sylius_product_variant_depth',
            'form' => 'form[name="sylius_product_variant"]',
            'height' => '#sylius_product_variant_height',
            'minimum_price' => '#sylius_product_variant_channelPricings_%channelCode%_minimumPrice',
            'on_hand' => '#sylius_product_variant_onHand',
            'option_select' => '#sylius_product_variant_optionValues_%option-name%',
            'price_calculator' => '#sylius_product_variant_pricingCalculator',
            'shipping_category' => '#sylius_product_variant_shippingCategory',
            'shipping_required' => '#sylius_product_variant_shippingRequired',
            'original_price' => '#sylius_product_variant_channelPricings_%channelCode%_originalPrice',
            'price' => '#sylius_product_variant_channelPricings_%channelCode%_price',
            'prices_validation_message' => '#sylius_product_variant_channelPricings ~ .sylius-validation-error, #sylius_product_variant_channelPricings .sylius-validation-error',
            'weight' => '#sylius_product_variant_weight',
            'width' => '#sylius_product_variant_width',
        ]);
    }
}
