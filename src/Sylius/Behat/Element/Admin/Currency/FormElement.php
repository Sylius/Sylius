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

namespace Sylius\Behat\Element\Admin\Currency;

use Behat\Mink\Session;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;

final class FormElement extends BaseFormElement implements FormElementInterface
{
    public function __construct(
        Session $session,
        array|MinkParameters $minkParameters,
        private readonly AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters);
    }

    public function chooseCurrency(string $currencyName): void
    {
        $this->autocompleteHelper->selectByName(
            $this->getDriver(),
            $this->getElement('code')->getXpath(),
            $currencyName,
        );
    }

    public function isCurrencyAvailable(string $currencyName): bool
    {
        $elements = $this->autocompleteHelper->search(
            $this->getDriver(),
            $this->getElement('code')->getXpath(),
            $currencyName,
        );

        foreach ($elements as $element) {
            if ($element === $currencyName) {
                return true;
            }
        }

        return false;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '[data-test-code]',
        ]);
    }
}
