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
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Context\Ui\Admin\Helper\NavigationTrait;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\Routing\RouterInterface;

class UpdateSimpleProductPage extends BaseUpdatePage implements UpdateSimpleProductPageInterface
{
    use ChecksCodeImmutability;
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

    protected function getResourceName(): string
    {
        return 'product';
    }

    public function saveChanges(): void
    {
        $this->waitForFormUpdate();

        parent::saveChanges();
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

    public function goToVariantsList(): void
    {
        $this->getDocument()->clickLink('List variants');
    }

    public function goToVariantCreation(): void
    {
        $this->getDocument()->clickLink('Create');
    }

    public function goToVariantGeneration(): void
    {
        $this->getDocument()->clickLink('Generate');
    }

    public function getShowProductInSingleChannelUrl(): string
    {
        return $this->getElement('view_in_store')->getAttribute('href');
    }

    public function isShowInShopButtonDisabled(): bool
    {
        return $this->getElement('view_in_store')->hasClass('disabled');
    }

    public function showProductInChannel(string $channel): void
    {
        $this->getElement('view_in_store')->clickLink($channel);
    }

    public function showProductInSingleChannel(): void
    {
        $this->getElement('view_in_store')->click();
    }

    public function disable(): void
    {
        $this->getElement('enabled')->uncheck();
    }

    public function isEnabled(): bool
    {
        return $this->getElement('enabled')->isChecked();
    }

    public function enable(): void
    {
        $this->getElement('enabled')->check();
    }

    public function specifyCode(string $code): void
    {
        $this->getElement('code')->setValue($code);
    }

    public function specifyField(string $field, string $value): void
    {
        $this->getElement($field)->setValue($value);
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

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '[data-test-code]',
            'enabled' => '[data-test-enabled]',
            'field_shipping_category' => '[name="sylius_admin_product[variant][shippingCategory]"]',
            'field_shipping_required' => '[name="sylius_admin_product[variant][shippingRequired]"]',
            'product_translation_accordion' => '[data-test-product-translations-accordion="%localeCode%"]',
            'show_product_button' => '[data-test-show-product]',
            'side_navigation_tab' => '[data-test-side-navigation-tab="%name%"]',
            'tracked' => '[name="sylius_admin_product[variant][tracked]"]',
            'view_in_store' => '[data-test-view-in-store]',
        ]);
    }
}
