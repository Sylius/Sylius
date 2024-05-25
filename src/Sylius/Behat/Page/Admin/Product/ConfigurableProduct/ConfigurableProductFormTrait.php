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

namespace Sylius\Behat\Page\Admin\Product\ConfigurableProduct;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Service\DriverHelper;

trait ConfigurableProductFormTrait
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
            'form' => '[data-live-name-value="sylius_admin:product:form"]',
            'generate_product_slug_button' => '[data-test-generate-product-slug-button="%localeCode%"]',
            'meta_description' => '[data-test-meta-description="%locale%"]',
            'meta_keywords' => '[data-test-meta-keywords="%locale%"]',
            'name' => '[data-test-name="%locale%"]',
            'product_options_autocomplete' => '[data-test-product-options-autocomplete]',
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
        $this->getElement(lcfirst($field))->setValue($value);
    }

    public function nameItIn(string $name, string $localeCode): void
    {
        $this->changeTab('translations');
        $this->expandTranslationAccordion($localeCode);

        $this->getElement('name', ['%locale%' => $localeCode])->setValue($name);
    }

    public function selectOption(string $optionName): void
    {
        $this->changeTab('details');
        $productOptionsAutocomplete = $this->getElement('product_options_autocomplete');

        $this->autocompleteHelper->selectByName($this->getDriver(), $productOptionsAutocomplete->getXpath(), $optionName);
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

    /*
     * Helpers
     */

    private function waitForFormUpdate(): void
    {
        $form = $this->getElement('form');
        sleep(1); // we need to sleep, as sometimes the check below is executed faster than the form sets the busy attribute
        $form->waitFor(1500, function () use ($form) {
            return !$form->hasAttribute('busy');
        });
    }

    private function clickButton(string $locator): void
    {
        if (DriverHelper::isJavascript($this->getDriver())) {
            $this->getDocument()->pressButton($locator);
        }
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
