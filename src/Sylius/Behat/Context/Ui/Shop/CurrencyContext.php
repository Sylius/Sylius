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

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Webmozart\Assert\Assert;

final readonly class CurrencyContext implements Context
{
    public function __construct(private HomePageInterface $homePage)
    {
    }

    /**
     * @When I browse currencies
     */
    public function iBrowseCurrencies(): void
    {
        $this->homePage->open();
    }

    /**
     * @Given I changed my currency to :currencyCode
     * @When I switch to the :currencyCode currency
     */
    public function iSwitchTheCurrencyToTheCurrency(string $currencyCode): void
    {
        $this->homePage->open();
        $this->homePage->switchCurrency($currencyCode);
    }

    /**
     * @Then I should (still) shop using the :currencyCode currency
     */
    public function iShouldShopUsingTheCurrency(string $currencyCode): void
    {
        $this->homePage->open();

        Assert::same($this->homePage->getActiveCurrency(), $currencyCode);
    }

    /**
     * @Then I should be able to shop using the :currencyCode currency
     */
    public function iShouldBeAbleToShopUsingTheCurrency(string $currencyCode): void
    {
        $this->homePage->open();

        Assert::oneOf($currencyCode, $this->homePage->getAvailableCurrencies());
    }

    /**
     * @Then I should not be able to shop using the :currencyCode currency
     */
    public function iShouldNotBeAbleToShopUsingTheCurrency(string $currencyCode): void
    {
        $this->homePage->open();

        if (in_array($currencyCode, $this->homePage->getAvailableCurrencies(), true)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected "%s" not to be in "%s"',
                $currencyCode,
                implode('", "', $this->homePage->getAvailableCurrencies()),
            ));
        }
    }

    /**
     * @Then I should see :firstCurrency and :secondCurrency in the list
     */
    public function iShouldSeeCurrenciesInTheList(string ...$currenciesCodes): void
    {
        $this->homePage->open();

        if (in_array($currenciesCodes, $this->homePage->getAvailableCurrencies(), true)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected "%s" not to be in "%s"',
                $currenciesCodes,
                implode('", "', $this->homePage->getAvailableCurrencies()),
            ));
        }
    }
}
