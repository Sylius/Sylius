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
use Sylius\Behat\Page\Admin\ExchangeRate\UpdatePageInterface;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
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
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     */
    public function __construct(
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
    }

    /**
     * @Given I want to add a new exchange rate
     */
    public function iWantToAddNewExchangeRate()
    {
        $this->createPage->open();
    }

    /**
     * @Given /^I want to edit (this exchange rate)$/
     * @When /^I am editing (this exchange rate)$/
     */
    public function iWantToEditThisExchangeRate(ExchangeRateInterface $exchangeRate)
    {
        $this->updatePage->open(['id' => $exchangeRate->getId()]);
    }

    /**
     * @When I want to browse exchange rates of the store
     */
    public function iWantToBrowseExchangeRatesOfTheStore()
    {
        $this->indexPage->open();
    }

    /**
     * @When /^I specify its ratio as (-?[0-9\.]+)$/
     * @When I don't specify its ratio
     */
    public function iSpecifyItsRatioAs($ratio = null)
    {
        if (null !== $ratio) {
            $this->createPage->specifyRatio($ratio);
        }
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
     * @When I( try to) add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @When I change ratio to :ratio
     */
    public function iChangeRatioTo($ratio)
    {
        $this->updatePage->changeRatio((float)$ratio);
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I delete the exchange rate between :baseCurrencyName and :counterCurrencyName
     */
    public function iDeleteTheExchangeRateBetweenAnd($baseCurrencyName, $counterCurrencyName)
    {
        $this->indexPage->open();

        $this->indexPage->deleteResourceOnPage([
            'baseCurrency' => $baseCurrencyName,
            'counterCurrency' => $counterCurrencyName,
        ]);
    }

    /**
     * @Then I should see :count exchange rates on the list
     * @Then there should be no exchange rates on the list
     */
    public function iShouldSeeExchangeRatesOnTheList($count = 0)
    {
        $this->assertCountOfExchangeRatesOnTheList($count);
    }

    /**
     * @Then I should( still) see one exchange rate on the list
     */
    public function iShouldSeeOneExchangeRateOnTheList()
    {
        $this->indexPage->open();

        $this->assertCountOfExchangeRatesOnTheList(1);
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
     * @Then it should have a ratio of :ratio
     */
    public function thisExchangeRateShouldHaveRatioOf($ratio)
    {
        Assert::eq(
            $ratio,
            $this->updatePage->getRatio(),
            'Exchange rate\'s ratio should be %s, but is %s instead.'
        );
    }

    /**
     * @Then /^(this exchange rate) should no longer be on the list$/
     */
    public function thisExchangeRateShouldNoLongerBeOnTheList(ExchangeRateInterface $exchangeRate)
    {
        $this->assertExchangeRateIsNotOnTheList(
            $exchangeRate->getBaseCurrency()->getName(),
            $exchangeRate->getCounterCurrency()->getName()
        );
    }

    /**
     * @Then the exchange rate between :baseCurrencyName and :counterCurrencyName should not be added
     * @Then the exchange rate with base currency :baseCurrencyName should not be added
     * @Then the exchange rate with counter currency :counterCurrencyName should not be added
     */
    public function theExchangeRateBetweenAndShouldNotBeAdded($baseCurrencyName = null, $counterCurrencyName = null)
    {
        $this->indexPage->open();

        $this->assertExchangeRateIsNotOnTheList($baseCurrencyName, $counterCurrencyName);
    }

    /**
     * @Then /^(this exchange rate) should have a ratio of ([0-9\.]+)$/
     */
    public function thisExchangeRateShouldHaveARatioOf(ExchangeRateInterface $exchangeRate, $ratio)
    {
        $baseCurrencyName = $exchangeRate->getBaseCurrency()->getName();
        $counterCurrencyName = $exchangeRate->getCounterCurrency()->getName();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage([
                'ratio' => $ratio,
                'baseCurrency' => $baseCurrencyName,
                'counterCurrency' => $counterCurrencyName,
            ]),
            sprintf(
                'An exchange rate between %s and %s with a ratio of %s has not been found on the list.',
                $baseCurrencyName,
                $counterCurrencyName,
                $ratio
            )
        );
    }

    /**
     * @Then I should see that the base currency is disabled
     */
    public function iShouldSeeThatTheBaseCurrencyIsDisabled()
    {
        Assert::true(
            $this->updatePage->isBaseCurrencyDisabled(),
            'The base currency is not disabled.'
        );
    }

    /**
     * @Then I should see that the counter currency is disabled
     */
    public function iShouldSeeThatTheCounterCurrencyIsDisabled()
    {
        Assert::true(
            $this->updatePage->isCounterCurrencyDisabled(),
            'The counter currency is not disabled.'
        );
    }

    /**
     * @Then /^I should be notified that ([^"]+) is required$/
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        Assert::same($this->createPage->getValidationMessage($element), sprintf('Please enter exchange rate %s.', $element));
    }

    /**
     * @Then I should be notified that the ratio must be greater than zero
     */
    public function iShouldBeNotifiedThatRatioMustBeGreaterThanZero()
    {
        Assert::same($this->createPage->getValidationMessage('ratio'), 'The ratio must be greater than 0.');
    }

    /**
     * @Then I should be notified that base and counter currencies must differ
     */
    public function iShouldBeNotifiedThatBaseAndCounterCurrenciesMustDiffer()
    {
        $expectedMessage = 'The base and counter currencies must differ.';

        $this->assertFormHasValidationMessage($expectedMessage);
    }

    /**
     * @Then I should be notified that the currency pair must be unique
     */
    public function iShouldBeNotifiedThatTheCurrencyPairMustBeUnique()
    {
        $expectedMessage = 'The currency pair must be unique.';

        $this->assertFormHasValidationMessage($expectedMessage);
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

    /**
     * @param string $baseCurrencyName
     * @param string $counterCurrencyName
     *
     * @throws \InvalidArgumentException
     */
    private function assertExchangeRateIsNotOnTheList($baseCurrencyName, $counterCurrencyName)
    {
        Assert::false(
            $this->indexPage->isSingleResourceOnPage([
                'baseCurrency' => $baseCurrencyName,
                'counterCurrency' => $counterCurrencyName,
            ]),
            sprintf(
                'An exchange rate with base currency %s and counter currency %s has been found on the list.',
                $baseCurrencyName,
                $counterCurrencyName
            )
        );
    }

    /**
     * @param int $count
     *
     * @throws \InvalidArgumentException
     */
    private function assertCountOfExchangeRatesOnTheList($count)
    {
        $actualCount = $this->indexPage->countItems();

        Assert::same(
            $actualCount,
            (int) $count,
            'Expected %2$d exchange rates to be on the list, but found %d instead.'
        );
    }

    /**
     * @param string $expectedMessage
     *
     * @throws \InvalidArgumentException
     */
    private function assertFormHasValidationMessage($expectedMessage)
    {
        Assert::true(
            $this->createPage->hasFormValidationError($expectedMessage),
            sprintf(
                'The validation message "%s" was not found on the page.',
                $expectedMessage
            )
        );
    }
}
