<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\ExchangeRate\CreatePageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ManagingExchangeRatesContext implements Context
{
    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     */
    public function __construct(CreatePageInterface $createPage, IndexPageInterface $indexPage)
    {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
    }

    /**
     * @Given I want to add a new exchange rate
     */
    public function iWantToAddNewExchangeRate()
    {
        $this->createPage->open();
    }

    /**
     * @When I want to browse exchange rates of the store
     */
    public function iWantToBrowseExchangeRatesOfTheStore()
    {
        $this->indexPage->open();
    }

    /**
     * @When I specify its ratio as :ratio
     */
    public function iSpecifyItsRatioAs($ratio)
    {
        $this->createPage->specifyRatio($ratio);
    }

    /**
     * @When I choose :currencyCode as the base currency
     */
    public function iChooseAsBaseCurrency($currencyCode)
    {
        $this->createPage->chooseBaseCurrency($currencyCode);
    }

    /**
     * @When I choose :currencyCode as the counter currency
     */
    public function iChooseAsCounterCurrency($currencyCode)
    {
        $this->createPage->chooseCounterCurrency($currencyCode);
    }

    /**
     * @When I add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @Then I should see :count exchange rates on the list
     */
    public function iShouldSeeExchangeRatesOnTheList($count)
    {
        $actualCount = $this->indexPage->countItems();

        Assert::same(
            $actualCount,
            (int) $count,
            'Expected %2$d exchange rates to be on the list, but found %d instead.'
        );
    }

    /**
     * @Then the exchange rate between :baseCurrency and :counterCurrency should appear in the store
     */
    public function theExchangeRateBetweenAndShouldAppearInTheStore($baseCurrency, $counterCurrency)
    {
        $this->indexPage->open();

        $this->assertExchangeRateIsOnList($baseCurrency, $counterCurrency);
    }

    /**
     * @Then I should see an exchange rate between :baseCurrencyName and :counterCurrencyName on the list
     */
    public function iShouldSeeAnExchangeRateBetweenAndOnTheList($baseCurrencyName, $counterCurrencyName)
    {
        $this->assertExchangeRateIsOnList($baseCurrencyName, $counterCurrencyName);
    }

    /**
     * @param string $baseCurrencyName
     * @param string $counterCurrencyName
     *
     * @throws \InvalidArgumentException
     */
    private function assertExchangeRateIsOnList($baseCurrencyName, $counterCurrencyName)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage([
                'baseCurrency' => $baseCurrencyName,
                'counterCurrency' => $counterCurrencyName
            ]),
            sprintf(
                'An exchange rate with base currency %s and counter currency %s was not found on the list.',
                $baseCurrencyName,
                $counterCurrencyName
            )
        );
    }
}
