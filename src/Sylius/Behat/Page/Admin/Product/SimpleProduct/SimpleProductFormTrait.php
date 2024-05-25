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

namespace Sylius\Behat\Page\Admin\Product\SimpleProduct;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Component\Core\Model\ChannelInterface;

trait SimpleProductFormTrait
{
    public function getDefinedFormElements(): array
    {
        return [
            'channel' => '[data-test-channel-code="%channel_code%"]',
            'channel_tab' => '[data-test-channel-tab="%channelCode%"]',
            'channels' => '[data-test-channels]',
            'code' => '[data-test-code]',
            'enabled' => '[data-test-enabled]',
            'field_name' => '[data-test-name="%locale%"]',
            'field_original_price' => '[data-test-original-price-in-channel="%channelCode%"]',
            'field_price' => '[data-test-price-in-channel="%channelCode%"]',
            'field_shipping_category' => '[name="sylius_admin_product[variant][shippingCategory]"]',
            'field_shipping_required' => '[name="sylius_admin_product[variant][shippingRequired]"]',
            'form' => '[data-live-name-value="sylius_admin:product:form"]',
            'generate_product_slug_button' => '[data-test-generate-product-slug-button="%localeCode%"]',
            'name' => '[data-test-name="%locale%"]',
            'meta_description' => '[data-test-meta-description="%locale%"]',
            'meta_keywords' => '[data-test-meta-keywords="%locale%"]',
            'prices_validation_message' => '[data-test-missing-channel-price]',
            'product_translation_accordion' => '[data-test-product-translations-accordion="%localeCode%"]',
            'side_navigation_tab' => '[data-test-side-navigation-tab="%name%"]',
            'slug' => '[data-test-slug="%locale%"]',
        ];
    }

    public function specifyCode(string $code): void
    {
        $this->getElement('code')->setValue($code);
    }

    public function specifyField(string $field, string $value): void
    {
        $this->getElement($field)->setValue($value);
    }

    public function nameItIn(string $name, string $localeCode): void
    {
        $this->changeTab('translations');
        $this->expandTranslationAccordion($localeCode);
        $this->getElement('field_name', ['%localeCode%' => $localeCode])->setValue($name);
    }

    public function specifyPrice(ChannelInterface $channel, string $price): void
    {
        $this->changeTab('channel-pricing');
        $this->changeChannelTab($channel->getCode());
        $this->getElement('field_price', ['%channelCode%' => $channel->getCode()])->setValue($price);
    }

    public function specifyOriginalPrice(ChannelInterface $channel, int $originalPrice): void
    {
        $this->changeTab('channel-pricing');
        $this->changeChannelTab($channel->getCode());
        $this->getElement('field_original_price', ['%channelCode%' => $channel->getCode()])->setValue($originalPrice);
    }

    public function selectShippingCategory(string $shippingCategoryName): void
    {
        $this->changeTab('shipping');
        $this->getElement('field_shipping_category')->selectOption($shippingCategoryName);
    }

    public function setShippingRequired(bool $isShippingRequired): void
    {
        $this->changeTab('details');

        if ($isShippingRequired) {
            $this->getElement('field_shipping_required')->check();

            return;
        }

        $this->getElement('field_shipping_required')->uncheck();
    }

    public function isShippingRequired(): bool
    {
        return $this->getElement('field_shipping_required')->isChecked();
    }

    public function generateSlug(string $localeCode): void
    {
        $this->getElement('generate_product_slug_button', ['%localeCode%' => $localeCode])->click();
        $this->waitForFormUpdate();
    }

    public function hasTab(string $name): bool
    {
        return $this->hasElement('side_navigation_tab', ['%name%' => $name]);
    }

    protected function getElement(string $name, array $parameters = []): NodeElement
    {
        if (!isset($parameters['%locale%'])) {
            $parameters['%locale%'] = 'en_US';
        }

        return parent::getElement($name, $parameters);
    }

    private function waitForFormUpdate(): void
    {
        $form = $this->getElement('form');
        sleep(1); // we need to sleep, as sometimes the check below is executed faster than the form sets the busy attribute
        $form->waitFor(1500, function () use ($form) {
            return !$form->hasAttribute('busy');
        });
    }



    /*
     * Tabs management
     */
    private function changeTab(string $tabName): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $this->getElement('side_navigation_tab', ['%name%' => $tabName])->click();
    }

    private function changeChannelTab(string $channelCode): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $this->getElement('channel_tab', ['%channelCode%' => $channelCode])->click();
    }

    private function expandTranslationAccordion(string $localeCode): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $translationAccordion = $this->getElement('product_translation_accordion', ['%localeCode%' => $localeCode]);

        if ($translationAccordion->getAttribute('aria-expanded') === 'true') {
            return;
        }

        $translationAccordion->click();
    }
}
