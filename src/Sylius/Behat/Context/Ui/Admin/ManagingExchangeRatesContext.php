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
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ManagingExchangeRatesContext implements Context
{
    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @param IndexPageInterface $indexPage
     */
    public function __construct(IndexPageInterface $indexPage)
    {
        $this->indexPage = $indexPage;
    }

    /**
     * @When I want to browse exchange rates of the store
     */
    public function iWantToBrowseExchangeRatesOfTheStore()
    {
        $this->indexPage->open();
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
     * @Then I should see an exchange rate between :baseCurrencyName and :counterCurrencyName on the list
     */
    public function iShouldSeeAnExchangeRateBetweenAndOnTheList($baseCurrencyName, $counterCurrencyName)
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
