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

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsCode;

    /**
     * {@inheritdoc}
     */
    public function specifyPrice(int $price, string $channelName): void
    {
        $this->getElement('price', ['%channelName%' => $channelName])->setValue($price);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyOriginalPrice(int $originalPrice, string $channelName): void
    {
        $this->getElement('original_price', ['%channelName%' => $channelName])->setValue($originalPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyCurrentStock(int $currentStock): void
    {
        $this->getDocument()->fillField('Current stock', $currentStock);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyHeightWidthDepthAndWeight(int $height, int $width, int $depth, int $weight): void
    {
        $this->getDocument()->fillField('Height', $height);
        $this->getDocument()->fillField('Width', $width);
        $this->getDocument()->fillField('Depth', $depth);
        $this->getDocument()->fillField('Weight', $weight);
    }

    /**
     * {@inheritdoc}
     */
    public function nameItIn(string $name, string $language): void
    {
        $this->getDocument()->fillField(
            sprintf('sylius_product_variant_translations_%s_name', $language), $name
        );
    }

    /**
     * {@inheritdoc}
     */
    public function selectOption(string $optionName, string $optionValue): void
    {
        $optionName = strtoupper($optionName);
        $this->getElement('option_select', ['%option-name%' => $optionName])->selectOption($optionValue);
    }

    /**
     * {@inheritdoc}
     */
    public function choosePricingCalculator(string $name): void
    {
        $this->getElement('price_calculator')->selectOption($name);
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function selectShippingCategory(string $shippingCategoryName): void
    {
        $this->getElement('shipping_category')->selectOption($shippingCategoryName);
    }

    /**
     * {@inheritdoc}
     */
    public function getPricesValidationMessage(): string
    {
        return $this->getElement('prices_validation_message')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingRequired(bool $isShippingRequired): void
    {
        if ($isShippingRequired) {
            $this->getElement('shipping_required')->check();

            return;
        }

        $this->getElement('shipping_required')->uncheck();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_product_variant_code',
            'depth' => '#sylius_product_variant_depth',
            'form' => 'form[name="sylius_product_variant"]',
            'height' => '#sylius_product_variant_height',
            'on_hand' => '#sylius_product_variant_onHand',
            'option_select' => '#sylius_product_variant_optionValues_%option-name%',
            'price_calculator' => '#sylius_product_variant_pricingCalculator',
            'shipping_category' => '#sylius_product_variant_shippingCategory',
            'shipping_required' => '#sylius_product_variant_shippingRequired',
            'original_price' => '#sylius_product_variant_channelPricings > .field:contains("%channelName%") input[name$="[originalPrice]"]',
            'price' => '#sylius_product_variant_channelPricings > .field:contains("%channelName%") input[name$="[price]"]',
            'prices_validation_message' => '#sylius_product_variant_channelPricings ~ .sylius-validation-error, #sylius_product_variant_channelPricings .sylius-validation-error',
            'weight' => '#sylius_product_variant_weight',
            'width' => '#sylius_product_variant_width',
        ]);
    }
}
