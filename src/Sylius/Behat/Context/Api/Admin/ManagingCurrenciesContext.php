<?php

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Sylius\Component\Currency\Model\CurrencyInterface;

final class ManagingCurrenciesContext implements Context
{
    /**
     * @When I want to browse currencies of the store
     */
    public function iWantToSeeAllCurrenciesInStore(): void
    {
        throw new PendingException();
    }

    /**
     * @Then I should see :count currencies in the list
     */
    public function iShouldSeeCurrenciesInTheList(int $count): void
    {
        throw new PendingException();
    }

    /**
     * @Then I should see the currency :currency in the list
     */
    public function currencyShouldAppearInTheStore(CurrencyInterface $currency): void
    {
        throw new PendingException();
    }
}
