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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
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
     * @var CurrencyStorageInterface
     */
    private $currencyStorage;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param HomePageInterface $homePage
     */
    public function __construct(HomePageInterface $homePage, CurrencyStorageInterface $currencyStorage, SharedStorageInterface $sharedStorage)
    {
        $this->homePage = $homePage;
        $this->currencyStorage = $currencyStorage;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I switch to the :currencyCode currency
     * @When I change my currency to :currencyCode
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

    /**
     * @Then I should not be able to shop without default currency
     */
    public function iShouldNotBeAbleToShop()
    {
        $this->homePage->tryToOpen();

        Assert::false($this->homePage->isOpen(), 'Homepage should not be opened!');
    }
}
