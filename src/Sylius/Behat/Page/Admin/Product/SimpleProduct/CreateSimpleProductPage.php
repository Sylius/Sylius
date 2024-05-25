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
use Behat\Mink\Session;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Behat\Page\Admin\Product\Common\ProductAssociationsTrait;
use Sylius\Behat\Page\Admin\Product\Common\ProductAttributesTrait;
use Sylius\Behat\Page\Admin\Product\Common\ProductMediaTrait;
use Sylius\Behat\Page\Admin\Product\Common\ProductTaxonomyTrait;
use Sylius\Behat\Page\Admin\Product\Common\ProductTranslationsTrait;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\Routing\RouterInterface;

class CreateSimpleProductPage extends BaseCreatePage implements CreateSimpleProductPageInterface
{
    use ProductAssociationsTrait;
    use ProductAttributesTrait;
    use ProductChannelPricingsTrait;
    use ProductMediaTrait;
    use ProductTaxonomyTrait;
    use ProductTranslationsTrait;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        string $routeName,
        private readonly AutocompleteHelperInterface $autocompleteHelper,
    )
    {
        parent::__construct($session, $minkParameters, $router, $routeName);
    }

    public function getRouteName(): string
    {
        return parent::getRouteName() . '_simple';
    }

    public function create(): void
    {
        $this->waitForFormUpdate();

        parent::create();
    }

    public function checkChannel(string $channelCode): void
    {
        $this->getElement('channel', ['%channel_code%' => $channelCode])->check();
    }

    public function getChannelPricingValidationMessage(): string
    {
        return $this->getElement('prices_validation_message')->getText();
    }

    public function cancelChanges(): void
    {
        $this->getElement('cancel_button')->click();
    }

    private function changeTab(string $tabName): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $this->getElement('side_navigation_tab', ['%name%' => $tabName])->click();
    }

    public function specifyCode(string $code): void
    {
        $this->getElement('code')->setValue($code);
    }

    public function specifyField(string $field, string $value): void
    {
        $this->getElement($field)->setValue($value);
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

    protected function getDefinedElements(): array
    {
        return array_merge(
            parent::getDefinedElements(),
            $this->getDefinedProductMediaElements(),
            $this->getDefinedProductAssociationsElements(),
            $this->getDefinedProductAttributesElements(),
            $this->getDefinedProductTranslationsElements(),
            $this->getDefinedProductTaxonomyElements(),
            $this->getDefinedProductChannelPricingsElements(),
            [
                'code' => '[data-test-code]',
                'enabled' => '[data-test-enabled]',
                'field_shipping_category' => '[name="sylius_admin_product[variant][shippingCategory]"]',
                'field_shipping_required' => '[name="sylius_admin_product[variant][shippingRequired]"]',
                'form' => '[data-live-name-value="sylius_admin:product:form"]',
                'product_translation_accordion' => '[data-test-product-translations-accordion="%localeCode%"]',
                'side_navigation_tab' => '[data-test-side-navigation-tab="%name%"]',
            ],
        );
    }

    private function waitForFormUpdate(): void
    {
        $form = $this->getElement('form');
        sleep(1); // we need to sleep, as sometimes the check below is executed faster than the form sets the busy attribute
        $form->waitFor(1500, function () use ($form) {
            return !$form->hasAttribute('busy');
        });
    }

    private function selectElementFromAttributesDropdown(string $id): void
    {
        $this->getDriver()->executeScript('$(\'#sylius_product_attribute_choice\').dropdown(\'show\');');
        $this->getDriver()->executeScript(sprintf('$(\'#sylius_product_attribute_choice\').dropdown(\'set selected\', \'%s\');', $id));
    }

    private function waitForFormElement(int $timeout = 5): void
    {
        $form = $this->getElement('form');
        $this->getDocument()->waitFor($timeout, fn() => !str_contains($form->getAttribute('class'), 'loading'));
    }

    private function clickTabIfItsNotActive(string $tabName): void
    {
        $attributesTab = $this->getElement('tab', ['%name%' => $tabName]);
        if (!$attributesTab->hasClass('active')) {
            $attributesTab->click();
        }
    }
}
