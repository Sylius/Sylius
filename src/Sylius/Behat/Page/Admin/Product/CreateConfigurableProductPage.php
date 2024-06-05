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
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\Routing\RouterInterface;

class CreateConfigurableProductPage extends BaseCreatePage implements CreateConfigurableProductPageInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        string $routeName,
        private readonly AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters, $router, $routeName);
    }

    public function create(): void
    {
        $this->waitForFormUpdate();

        parent::create();
    }

    public function specifyCode(string $code): void
    {
        $this->getElement('code')->setValue($code);
    }

    public function specifyField(string $field, string $value): void
    {
        $this->getElement(lcfirst($field))->setValue($value);
    }

    public function selectOption(string $optionName): void
    {
        $this->changeTab();
        $productOptionsAutocomplete = $this->getElement('product_options_autocomplete');

        $this->autocompleteHelper->selectByName($this->getDriver(), $productOptionsAutocomplete->getXpath(), $optionName);
    }

    /**
     * @return string[]
     */
    protected function getDefinedElements(): array
    {
        return array_merge(
            parent::getDefinedElements(),
            [
                'channel' => '[data-test-channel-code="%channel_code%"]',
                'channel_tab' => '[data-test-channel-tab="%channelCode%"]',
                'channels' => '[data-test-channels]',
                'code' => '[data-test-code]',
                'enabled' => '[data-test-enabled]',
                'form' => '[data-live-name-value="sylius_admin:product:form"]',
                'product_options_autocomplete' => '[data-test-product-options-autocomplete]',
                'side_navigation_tab' => '[data-test-side-navigation-tab="%name%"]',
            ],
        );
    }

    protected function getElement(string $name, array $parameters = []): NodeElement
    {
        if (!isset($parameters['%locale%'])) {
            $parameters['%locale%'] = 'en_US';
        }

        return parent::getElement($name, $parameters);
    }

    private function changeTab(): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $this->getElement('side_navigation_tab', ['%name%' => 'details'])->click();
    }
}
