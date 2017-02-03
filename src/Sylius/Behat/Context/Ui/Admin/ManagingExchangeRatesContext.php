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
use Sylius\Behat\Page\Admin\ExchangeRate\CreatePageInterface;
use Sylius\Behat\Page\Admin\ExchangeRate\IndexPageInterface;
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
     * @When I am browsing exchange rates of the store
     * @When I browse exchange rates of the store
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
        $this->createPage->specifyRatio($ratio);
    }

    /**
     * @When I choose :currencyCode as the source currency
     */
    public function iChooseAsSourceCurrency($currencyCode)
    {
        $this->createPage->chooseSourceCurrency($currencyCode);
    }

    /**
     * @When I choose :currencyCode as the target currency
     */
    public function iChooseAsTargetCurrency($currencyCode)
    {
        $this->createPage->chooseTargetCurrency($currencyCode);
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
     * @When I delete the exchange rate between :sourceCurrencyName and :targetCurrencyName
     */
    public function iDeleteTheExchangeRateBetweenAnd($sourceCurrencyName, $targetCurrencyName)
    {
        $this->indexPage->open();

        $this->indexPage->deleteResourceOnPage([
            'sourceCurrency' => $sourceCurrencyName,
            'targetCurrency' => $targetCurrencyName,
        ]);
    }

    /**
     * @When I choose :currencyName as a currency filter
     */
    public function iChooseCurrencyAsACurrencyFilter($currencyName)
    {
        $this->indexPage->chooseCurrencyFilter($currencyName);
    }

    /**
     * @When I filter
     */
    public function iFilter()
    {
        $this->indexPage->filter();
    }

    /**
     * @Then I should see :count exchange rates on the list
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
     * @Then the exchange rate with ratio :ratio between :sourceCurrency and :targetCurrency should appear in the store
     */
    public function theExchangeRateBetweenAndShouldAppearInTheStore($ratio, $sourceCurrency, $targetCurrency)
    {
        $this->indexPage->open();

        $this->assertExchangeRateWithRatioIsOnTheList($ratio, $sourceCurrency, $targetCurrency);
    }

    /**
     * @Then I should (also) see an exchange rate between :sourceCurrencyName and :targetCurrencyName on the list
     */
    public function iShouldSeeAnExchangeRateBetweenAndOnTheList($sourceCurrencyName, $targetCurrencyName)
    {
        Assert::true($this->indexPage->isSingleResourceOnPage([
            'sourceCurrency' => $sourceCurrencyName,
            'targetCurrency' => $targetCurrencyName,
        ]));
    }

    /**
     * @Then it should have a ratio of :ratio
     */
    public function thisExchangeRateShouldHaveRatioOf($ratio)
    {
        Assert::eq($this->updatePage->getRatio(), $ratio);
    }

    /**
     * @Then /^(this exchange rate) should no longer be on the list$/
     */
    public function thisExchangeRateShouldNoLongerBeOnTheList(ExchangeRateInterface $exchangeRate)
    {
        $this->assertExchangeRateIsNotOnTheList(
            $exchangeRate->getSourceCurrency()->getName(),
            $exchangeRate->getTargetCurrency()->getName()
        );
    }

    /**
     * @Then the exchange rate between :sourceCurrencyName and :targetCurrencyName should not be added
     */
    public function theExchangeRateBetweenAndShouldNotBeAdded($sourceCurrencyName, $targetCurrencyName)
    {
        $this->indexPage->open();

        $this->assertExchangeRateIsNotOnTheList($sourceCurrencyName, $targetCurrencyName);
    }

    /**
     * @Then /^(this exchange rate) should have a ratio of ([0-9\.]+)$/
     */
    public function thisExchangeRateShouldHaveARatioOf(ExchangeRateInterface $exchangeRate, $ratio)
    {
        $sourceCurrencyName = $exchangeRate->getSourceCurrency()->getName();
        $targetCurrencyName = $exchangeRate->getTargetCurrency()->getName();

        $this->assertExchangeRateWithRatioIsOnTheList($ratio, $sourceCurrencyName, $targetCurrencyName);
    }

    /**
     * @Then I should see that the source currency is disabled
     */
    public function iShouldSeeThatTheSourceCurrencyIsDisabled()
    {
        Assert::true($this->updatePage->isSourceCurrencyDisabled());
    }

    /**
     * @Then I should see that the target currency is disabled
     */
    public function iShouldSeeThatTheTargetCurrencyIsDisabled()
    {
        Assert::true($this->updatePage->isTargetCurrencyDisabled());
    }

    /**
     * @Then /^I should be notified that ([^"]+) is required$/
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        Assert::same(
            $this->createPage->getValidationMessage($element),
            sprintf('Please enter exchange rate %s.', $element)
        );
    }

    /**
     * @Then I should be notified that the ratio must be greater than zero
     */
    public function iShouldBeNotifiedThatRatioMustBeGreaterThanZero()
    {
        Assert::same($this->createPage->getValidationMessage('ratio'), 'The ratio must be greater than 0.');
    }

    /**
     * @Then I should be notified that source and target currencies must differ
     */
    public function iShouldBeNotifiedThatSourceAndTargetCurrenciesMustDiffer()
    {
        $this->assertFormHasValidationMessage('The source and target currencies must differ.');
    }

    /**
     * @Then I should be notified that the currency pair must be unique
     */
    public function iShouldBeNotifiedThatTheCurrencyPairMustBeUnique()
    {
        $this->assertFormHasValidationMessage('The currency pair must be unique.');
    }

    /**
     * @param float $ratio
     * @param string $sourceCurrencyName
     * @param string $targetCurrencyName
     *
     * @throws \InvalidArgumentException
     */
    private function assertExchangeRateWithRatioIsOnTheList($ratio, $sourceCurrencyName, $targetCurrencyName)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage([
                'ratio' => $ratio,
                'sourceCurrency' => $sourceCurrencyName,
                'targetCurrency' => $targetCurrencyName,
            ]),
            sprintf(
                'An exchange rate between %s and %s with a ratio of %s has not been found on the list.',
                $sourceCurrencyName,
                $targetCurrencyName,
                $ratio
            )
        );
    }

    /**
     * @param string $sourceCurrencyName
     * @param string $targetCurrencyName
     *
     * @throws \InvalidArgumentException
     */
    private function assertExchangeRateIsNotOnTheList($sourceCurrencyName, $targetCurrencyName)
    {
        Assert::false(
            $this->indexPage->isSingleResourceOnPage([
                'sourceCurrency' => $sourceCurrencyName,
                'targetCurrency' => $targetCurrencyName,
            ]),
            sprintf(
                'An exchange rate with source currency %s and target currency %s has been found on the list.',
                $sourceCurrencyName,
                $targetCurrencyName
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
        Assert::same(
            $this->indexPage->countItems(),
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
