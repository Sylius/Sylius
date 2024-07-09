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
use Sylius\Behat\Element\Admin\ExchangeRate\FormElementInterface;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Behat\Page\Admin\ExchangeRate\IndexPageInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Webmozart\Assert\Assert;

final readonly class ManagingExchangeRatesContext implements Context
{
    public function __construct(
        private CreatePageInterface $createPage,
        private IndexPageInterface $indexPage,
        private UpdatePageInterface $updatePage,
        private FormElementInterface $formElement,
    ) {
    }

    /**
     * @When I want to add a new exchange rate
     */
    public function iWantToAddNewExchangeRate(): void
    {
        $this->createPage->open();
    }

    /**
     * @When /^I want to edit (this exchange rate)$/
     * @When /^I am editing (this exchange rate)$/
     */
    public function iWantToEditThisExchangeRate(ExchangeRateInterface $exchangeRate): void
    {
        $this->updatePage->open(['id' => $exchangeRate->getId()]);
    }

    /**
     * @Given I am browsing exchange rates of the store
     * @When I browse exchange rates
     * @When I browse exchange rates of the store
     */
    public function iWantToBrowseExchangeRatesOfTheStore(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When /^I specify its ratio as (-?[0-9\.]+)$/
     * @When I don't specify its ratio
     */
    public function iSpecifyItsRatioAs(?string $ratio = null): void
    {
        $this->formElement->specifyField('Ratio', $ratio ?? '');
    }

    /**
     * @When I choose :currencyCode as the source currency
     */
    public function iChooseAsSourceCurrency(string $currencyCode): void
    {
        $this->formElement->specifyField('Source currency', $currencyCode);
    }

    /**
     * @When I choose :currencyCode as the target currency
     */
    public function iChooseAsTargetCurrency(string $currencyCode): void
    {
        $this->formElement->specifyField('Target currency', $currencyCode);
    }

    /**
     * @When I( try to) add it
     */
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    /**
     * @When I change ratio to :ratio
     */
    public function iChangeRatioTo(string $ratio): void
    {
        $this->formElement->specifyField('Ratio', $ratio);
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I delete the exchange rate between :sourceCurrencyName and :targetCurrencyName
     */
    public function iDeleteTheExchangeRateBetweenAnd(string $sourceCurrencyName, string $targetCurrencyName): void
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
    public function iChooseCurrencyAsACurrencyFilter(string $currencyName): void
    {
        $this->indexPage->chooseCurrencyFilter($currencyName);
    }

    /**
     * @When I filter
     */
    public function iFilter(): void
    {
        $this->indexPage->filter();
    }

    /**
     * @When I check (also) the exchange rate between :sourceCurrencyName and :targetCurrencyName
     */
    public function iCheckTheExchangeRateBetweenAnd(string $sourceCurrencyName, string $targetCurrencyName): void
    {
        $this->indexPage->checkResourceOnPage([
            'sourceCurrency' => $sourceCurrencyName,
            'targetCurrency' => $targetCurrencyName,
        ]);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @Then I should see :count exchange rates on the list
     */
    public function iShouldSeeExchangeRatesOnTheList(int $count = 0): void
    {
        $this->assertCountOfExchangeRatesOnTheList($count);
    }

    /**
     * @Then I should see a single exchange rate in the list
     * @Then I should( still) see one exchange rate on the list
     */
    public function iShouldSeeOneExchangeRateOnTheList(): void
    {
        $this->indexPage->open();

        $this->assertCountOfExchangeRatesOnTheList(1);
    }

    /**
     * @Then the exchange rate with ratio :ratio between :sourceCurrency and :targetCurrency should appear in the store
     */
    public function theExchangeRateBetweenAndShouldAppearInTheStore(
        string $ratio,
        CurrencyInterface $sourceCurrency,
        CurrencyInterface $targetCurrency,
    ): void {
        $this->indexPage->open();

        $this->assertExchangeRateWithRatioIsOnTheList($ratio, $sourceCurrency->getName(), $targetCurrency->getName());
    }

    /**
     * @Then I should see the exchange rate between :sourceCurrencyName and :targetCurrencyName in the list
     * @Then I should (also) see an exchange rate between :sourceCurrencyName and :targetCurrencyName on the list
     */
    public function iShouldSeeAnExchangeRateBetweenAndOnTheList(
        string $sourceCurrencyName,
        string $targetCurrencyName,
    ): void {
        Assert::true($this->indexPage->isSingleResourceOnPage([
            'sourceCurrency' => $sourceCurrencyName,
            'targetCurrency' => $targetCurrencyName,
        ]));
    }

    /**
     * @Then it should have a ratio of :ratio
     */
    public function thisExchangeRateShouldHaveRatioOf(string $ratio): void
    {
        Assert::eq($this->formElement->getRatio(), $ratio);
    }

    /**
     * @Then /^(this exchange rate) should no longer be on the list$/
     */
    public function thisExchangeRateShouldNoLongerBeOnTheList(ExchangeRateInterface $exchangeRate): void
    {
        $this->assertExchangeRateIsNotOnTheList(
            $exchangeRate->getSourceCurrency()->getName(),
            $exchangeRate->getTargetCurrency()->getName(),
        );
    }

    /**
     * @Then the exchange rate between :sourceCurrencyName and :targetCurrencyName should not be added
     */
    public function theExchangeRateBetweenAndShouldNotBeAdded(string $sourceCurrencyName, string $targetCurrencyName): void
    {
        $this->indexPage->open();

        $this->assertExchangeRateIsNotOnTheList($sourceCurrencyName, $targetCurrencyName);
    }

    /**
     * @Then /^(this exchange rate) should have a ratio of ([0-9\.]+)$/
     */
    public function thisExchangeRateShouldHaveARatioOf(ExchangeRateInterface $exchangeRate, string $ratio): void
    {
        $sourceCurrencyName = $exchangeRate->getSourceCurrency()->getName();
        $targetCurrencyName = $exchangeRate->getTargetCurrency()->getName();

        $this->assertExchangeRateWithRatioIsOnTheList($ratio, $sourceCurrencyName, $targetCurrencyName);
    }

    /**
     * @Then I should not be able to edit its source currency
     */
    public function iShouldNotBeAbleToEditItsSourceCurrency(): void
    {
        Assert::true($this->formElement->isFieldDisabled('source_currency'));
    }

    /**
     * @Then I should not be able to edit its target currency
     */
    public function iShouldNotBeAbleToEditItsTargetCurrency(): void
    {
        Assert::true($this->formElement->isFieldDisabled('target_currency'));
    }

    /**
     * @Then /^I should be notified that ([^"]+) is required$/
     */
    public function iShouldBeNotifiedThatIsRequired(string $element): void
    {
        Assert::same(
            $this->formElement->getValidationMessage($element),
            sprintf('Please enter exchange rate %s.', $element),
        );
    }

    /**
     * @Then I should be notified that the ratio must be greater than zero
     */
    public function iShouldBeNotifiedThatRatioMustBeGreaterThanZero(): void
    {
        Assert::same($this->formElement->getValidationMessage('ratio'), 'The ratio must be greater than 0.');
    }

    /**
     * @Then I should be notified that source and target currencies must differ
     */
    public function iShouldBeNotifiedThatSourceAndTargetCurrenciesMustDiffer(): void
    {
        $this->assertFormHasValidationMessage('The source and target currencies must differ.');
    }

    /**
     * @Then I should be notified that the currency pair must be unique
     */
    public function iShouldBeNotifiedThatTheCurrencyPairMustBeUnique(): void
    {
        $this->assertFormHasValidationMessage('The currency pair must be unique.');
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertExchangeRateWithRatioIsOnTheList(
        string $ratio,
        string $sourceCurrencyName,
        string $targetCurrencyName,
    ): void {
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
                $ratio,
            ),
        );
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertExchangeRateIsNotOnTheList(string $sourceCurrencyName, string $targetCurrencyName): void
    {
        Assert::false(
            $this->indexPage->isSingleResourceOnPage([
                'sourceCurrency' => $sourceCurrencyName,
                'targetCurrency' => $targetCurrencyName,
            ]),
            sprintf(
                'An exchange rate with source currency %s and target currency %s has been found on the list.',
                $sourceCurrencyName,
                $targetCurrencyName,
            ),
        );
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertCountOfExchangeRatesOnTheList(int $count): void
    {
        Assert::same(
            $this->indexPage->countItems(),
            $count,
            'Expected %2$d exchange rates to be on the list, but found %d instead.',
        );
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertFormHasValidationMessage(string $expectedMessage): void
    {
        Assert::true(
            $this->formElement->hasFormValidationError($expectedMessage),
            sprintf(
                'The validation message "%s" was not found on the page.',
                $expectedMessage,
            ),
        );
    }
}
