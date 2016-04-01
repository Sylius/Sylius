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
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Webmozart\Assert\Assert;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ManagingCurrenciesContext implements Context
{
    const RESOURCE_NAME = 'currency';

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
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->notificationChecker = $notificationChecker;
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
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated()
    {
        $this->notificationChecker->checkCreationNotification(self::RESOURCE_NAME);
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
     */
    public function currencyShouldAppearInTheStore(CurrencyInterface $currency)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isResourceOnPage(['code' => $currency->getCode()]),
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
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited()
    {
        $this->notificationChecker->checkEditionNotification(self::RESOURCE_NAME);
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
}
