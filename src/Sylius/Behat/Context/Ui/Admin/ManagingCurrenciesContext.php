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
use Sylius\Behat\Page\Admin\Currency\CreatePageInterface;
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
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
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
}
