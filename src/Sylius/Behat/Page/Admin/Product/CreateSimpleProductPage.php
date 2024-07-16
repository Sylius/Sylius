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

namespace Sylius\Behat\Page\Admin\Product;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Sylius\Behat\Context\Ui\Admin\Helper\NavigationTrait;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\Routing\RouterInterface;

class CreateSimpleProductPage extends BaseCreatePage implements CreateSimpleProductPageInterface
{
    use NavigationTrait;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        string $routeName,
        private readonly AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters, $router, $routeName);
    }

    public function getResourceName(): string
    {
        return 'product';
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
        $this->changeTab('details');

        $this->getElement('channel', ['%channel_code%' => $channelCode])->check();
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
        $this->changeTab('details');

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
            [
                'channel' => '[data-test-channel-code="%channel_code%"]',
                'code' => '[data-test-code]',
                'enabled' => '[data-test-enabled]',
                'field_shipping_category' => '[name="sylius_admin_product[variant][shippingCategory]"]',
                'field_shipping_required' => '[name="sylius_admin_product[variant][shippingRequired]"]',
                'product_translation_accordion' => '[data-test-product-translations-accordion="%localeCode%"]',
                'show_product_button' => '[data-test-show-product]',
                'side_navigation_tab' => '[data-test-side-navigation-tab="%name%"]',
            ],
        );
    }
}
