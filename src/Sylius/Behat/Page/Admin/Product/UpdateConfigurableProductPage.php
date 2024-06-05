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
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Behat\Service\AutocompleteHelper;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\Routing\RouterInterface;

class UpdateConfigurableProductPage extends BaseUpdatePage implements UpdateConfigurableProductPageInterface
{
    use ChecksCodeImmutability;

    /**
     * @param array<array-key, string> $minkParameters
     */
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        string $routeName,
        private AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters, $router, $routeName);
    }

    public function saveChanges(): void
    {
        $this->waitForFormUpdate();

        parent::saveChanges();
    }

    public function isProductOptionChosen(string $option): bool
    {
        $optionElement = $this->getElement('options')->getParent();

        return AutocompleteHelper::isValueVisible($this->getSession(), $optionElement, $option);
    }

    public function isProductOptionsDisabled(): bool
    {
        return 'disabled' === $this->getElement('options')->getAttribute('disabled');
    }

    public function hasTab(string $name): bool
    {
        return $this->hasElement('side_navigation_tab', ['%name%' => $name]);
    }

    public function checkChannel(string $channelCode): void
    {
        $this->getElement('channel', ['%channel_code%' => $channelCode])->check();
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
        $this->changeTab('details');
        $productOptionsAutocomplete = $this->getElement('product_options_autocomplete');

        $this->autocompleteHelper->selectByName($this->getDriver(), $productOptionsAutocomplete->getXpath(), $optionName);
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
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
                'options' => '[data-test-options]',
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
}
