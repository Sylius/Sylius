<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CurrencyContext implements Context
{
    /**
     * @var HomePageInterface
     */
    private $homePage;

    /**
     * @param HomePageInterface $homePage
     */
    public function __construct(HomePageInterface $homePage)
    {
        $this->homePage = $homePage;
    }

    /**
     * @When I switch to the :currencyCode currency
     * @Given I changed my currency to :currencyCode
     */
    public function iSwitchTheCurrencyToTheCurrency($currencyCode)
    {
        $this->homePage->open();
        $this->homePage->switchCurrency($currencyCode);
    }

    /**
     * @Then I should (still) shop using the :currencyCode currency
     */
    public function iShouldShopUsingTheCurrency($currencyCode)
    {
        $this->homePage->open();

        Assert::same($this->homePage->getActiveCurrency(), $currencyCode);
    }

    /**
     * @Then I should be able to shop using the :currencyCode currency
     */
    public function iShouldBeAbleToShopUsingTheCurrency($currencyCode)
    {
        $this->homePage->open();

        Assert::oneOf($currencyCode, $this->homePage->getAvailableCurrencies());
    }

    /**
     * @Then I should not be able to shop using the :currencyCode currency
     */
    public function iShouldNotBeAbleToShopUsingTheCurrency($currencyCode)
    {
        $this->homePage->open();

        if (in_array($currencyCode, $this->homePage->getAvailableCurrencies(), true)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected "%s" not to be in "%s"',
                $currencyCode,
                implode('", "', $this->homePage->getAvailableCurrencies())
            ));
        }
    }
}
