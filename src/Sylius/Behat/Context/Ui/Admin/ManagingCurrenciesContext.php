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
use Sylius\Behat\Page\Admin\Currency\CreatePageInterface;
use Sylius\Behat\Page\Admin\Currency\IndexPageInterface;
use Sylius\Behat\Page\Admin\Currency\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Webmozart\Assert\Assert;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ManagingCurrenciesContext implements Context
{
    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     */
    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
    }

    /**
     * @Given I want to add a new currency
     */
    public function iWantToAddNewCurrency()
    {
        $this->createPage->open();
    }

    /**
     * @When I choose :currencyName
     */
    public function iChoose($currencyName)
    {
        $this->createPage->chooseName($currencyName);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @When I specify its exchange rate as :exchangeRate
     */
    public function iSpecifyExchangeRate($exchangeRate)
    {
        $this->createPage->specifyExchangeRate($exchangeRate);
    }

    /**
     * @Then the currency :currency should appear in the store
     * @Then I should see the currency :currency in the list
     */
    public function currencyShouldAppearInTheStore(CurrencyInterface $currency)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['code' => $currency->getCode()]),
            sprintf('Currency %s should exist but it does not.', $currency->getCode())
        );
    }

    /**
     * @Given /^I want to edit (this currency)$/
     */
    public function iWantToEditThisCurrency(CurrencyInterface $currency)
    {
        $this->updatePage->open(['id' => $currency->getId()]);
    }

    /**
     * @When I enable it
     */
    public function iEnableIt()
    {
        $this->updatePage->enable();
    }

    /**
     * @When I disable it
     */
    public function iDisableIt()
    {
        $this->updatePage->disable();
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then /^(this currency) should be disabled$/
     */
    public function thisCurrencyShouldBeDisabled(CurrencyInterface $currency)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isCurrencyDisabled($currency),
            sprintf('Currency %s should be disabled but it is not.', $currency->getCode())
        );
    }

    /**
     * @Then /^(this currency) should be enabled$/
     */
    public function thisCurrencyShouldBeEnabled(CurrencyInterface $currency)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isCurrencyEnabled($currency),
            sprintf('Currency %s should be enabled but it is not.', $currency->getCode())
        );
    }

    /**
     * @When I change exchange rate to :exchangeRate
     */
    public function iChangeExchangeRateTo($exchangeRate)
    {
       $this->updatePage->changeExchangeRate($exchangeRate);
    }

    /**
     * @Then this currency should have exchange rate :exchangeRate
     */
    public function thisCurrencyShouldHaveExchangeRate($exchangeRate)
    {
        Assert::eq(
            $exchangeRate,
            $this->updatePage->getExchangeRateValue(),
            sprintf(
                'Currency exchange rate should be equal %s, but was %s.',
                $exchangeRate,
                $this->updatePage->getExchangeRateValue()
            )
        );
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFiledShouldBeDisabled()
    {
        Assert::eq(
            'disabled',
            $this->updatePage->getCodeDisabledAttribute(),
            'Code field should be disabled but is not.'
        );
    }

    /**
     * @Then I should be notified that currency code must be unique
     */
    public function iShouldBeNotifiedThatCurrencyCodeMustBeUnique()
    {
        Assert::true(
            $this->createPage->checkValidationMessageFor('code', 'Currency code must be unique.'),
            'Unique code violation message should appear on page, but it does not.'
        );
    }

    /**
     * @Then there should still be only one currency with :element :code
     */
    public function thereShouldStillBeOnlyOneCurrencyWithCode($element, $codeValue)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage([$element => $codeValue]),
            sprintf('Currency with %s %s cannot be found.', $element, $codeValue)
        );
    }

    /**
     * @Then I should be notified that exchange rate is required
     */
    public function iShouldBeNotifiedThatExchangeRateIsRequired()
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::true(
            $currentPage->checkValidationMessageFor('exchangeRate', 'Please enter exchange rate.'),
            'Currency exchange rate should be required.'
        );
    }

    /**
     * @Then the currency :currencyName should not be added
     */
    public function theCurrencyShouldNotBeAdded($currencyName)
    {
        $this->indexPage->open();

        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['name' => $currencyName]),
            sprintf('Currency with name %s was created, but it should not.', $currencyName)
        );
    }

    /**
     * @When I remove its exchange rate
     */
    public function iRemoveItsExchangeRate()
    {
        $this->updatePage->changeExchangeRate('');
    }

    /**
     * @Then /^(this currency) should still have exchange rate equal to ((\d+)\.(\d+))$/
     */
    public function theCurrencyShouldStillHaveExchangeRateEquals(CurrencyInterface $currency, $exchangeRate)
    {
        $this->updatePage->open(['id' => $currency->getId()]);

        Assert::eq(
            $exchangeRate,
            $this->updatePage->getExchangeRateValue(),
            sprintf(
                'Currency exchange rate should be equal %s, but was %s.',
                $exchangeRate,
                $this->updatePage->getExchangeRateValue()
            )
        );
    }

    /**
     * @Given I want to browse currencies of the store
     */
    public function iWantToSeeAllCurrenciesInStore()
    {
        $this->indexPage->open();
    }

    /**
     * @Then /^I should see (\d+) currencies in the list$/
     */
    public function iShouldSeeCurrenciesInTheList($amountOfCurrencies)
    {
        Assert::eq(
            $amountOfCurrencies,
            $this->indexPage->countItems(),
            sprintf(
                'Amount of currencies should be equal %d, but was %d.',
                $amountOfCurrencies,
                $this->indexPage->countItems()
            )
        );
    }
}
