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
     * @When I switch the current currency to the :currencyCode currency
     */
    public function iSwitchTheCurrentCurrencyToTheCurrency($currencyCode)
    {
        $this->homePage->open();
        $this->homePage->switchCurrency($currencyCode);
    }

    /**
     * @Then I should shop using the :currencyCode currency
     */
    public function iShouldShopUsingTheCurrency($currencyCode)
    {
        $this->homePage->open();
        Assert::same($currencyCode, $this->homePage->getActiveCurrency());
    }

    /**
     * @Then I should be able to shop using the :currencyCode currency
     */
    public function iShouldBeAbleToShopUsingTheCurrency($currencyCode)
    {
        $this->homePage->open();
        Assert::oneOf($currencyCode, $this->homePage->getAvailableCurrencies());
    }
}
