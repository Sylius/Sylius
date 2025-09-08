<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Element\Admin\Currency\FormElementInterface;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Currency\IndexPageInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Webmozart\Assert\Assert;

final readonly class ManagingCurrenciesContext implements Context
{
    public function __construct(
        private IndexPageInterface $indexPage,
        private CreatePageInterface $createPage,
        private FormElementInterface $formElement,
    ) {
    }

    /**
     * @When I want to add a new currency
     */
    public function iWantToAddNewCurrency(): void
    {
        $this->createPage->open();
    }

    /**
     * @When I choose :currencyName
     */
    public function iChoose($currencyName): void
    {
        $this->formElement->chooseCurrency($currencyName);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    /**
     * @Then the currency :currency should appear in the store
     * @Then I should see the currency :currency on the list
     */
    public function currencyShouldAppearInTheStore(CurrencyInterface $currency): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $currency->getCode()]));
    }

    /**
     * @When I want to browse currencies of the store
     */
    public function iWantToSeeAllCurrenciesInStore(): void
    {
        $this->indexPage->open();
    }

    /**
     * @Then /^I should see (\d+) currencies on the list$/
     */
    public function iShouldSeeCurrenciesInTheList(int $amountOfCurrencies): void
    {
        Assert::same($this->indexPage->countItems(), $amountOfCurrencies);
    }

    /**
     * @Then I should not be able to choose :name
     */
    public function iShouldNotBeAbleToChoose(string $name): void
    {
        Assert::false($this->formElement->isCurrencyAvailable($name));
    }
}
