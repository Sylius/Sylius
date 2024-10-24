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

namespace Sylius\Behat\Page\Admin\Channel;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\Routing\RouterInterface;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;
    use Toggles;
    use FormTrait;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        string $routeName,
        private AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters, $router, $routeName);
    }

    public function getLocales(): array
    {
        return array_map(
            fn (NodeElement $element) => $element->getText(),
            $this->getElement('locales')->findAll('css', 'option:selected'),
        );
    }

    public function getCurrencies(): array
    {
        return array_map(
            fn (NodeElement $element) => $element->getText(),
            $this->getElement('currencies')->findAll('css', 'option:selected'),
        );
    }

    public function getDefaultTaxZone(): ?string
    {
        return $this->getElement('default_tax_zone')->find('css', 'option:selected')?->getText();
    }

    public function isBaseCurrencyDisabled(): bool
    {
        return $this->getElement('base_currency')->hasAttribute('disabled');
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getToggleableElement(): NodeElement
    {
        return $this->getElement('enabled');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(
            parent::getDefinedElements(),
            $this->getDefinedFormElements(),
        );
    }
}
