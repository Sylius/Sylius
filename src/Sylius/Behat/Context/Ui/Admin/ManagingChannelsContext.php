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
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Behat\Context\Ui\Admin\Helper\ValidationTrait;
use Sylius\Behat\Element\Admin\Channel\DiscountedProductsCheckingPeriodInputElementInterface;
use Sylius\Behat\Element\Admin\Channel\ExcludeTaxonsFromShowingLowestPriceInputElementInterface;
use Sylius\Behat\Element\Admin\Channel\LowestPriceFlagElementInterface;
use Sylius\Behat\Element\Admin\Channel\ShippingAddressInCheckoutRequiredElementInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Channel\CreatePageInterface;
use Sylius\Behat\Page\Admin\Channel\IndexPageInterface;
use Sylius\Behat\Page\Admin\Channel\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Webmozart\Assert\Assert;

final class ManagingChannelsContext implements Context
{
    use ValidationTrait;

    public function __construct(
        private IndexPageInterface $indexPage,
        private CreatePageInterface $createPage,
        private UpdatePageInterface $updatePage,
        private ShippingAddressInCheckoutRequiredElementInterface $shippingAddressInCheckoutRequiredElement,
        private CurrentPageResolverInterface $currentPageResolver,
        private NotificationCheckerInterface $notificationChecker,
        private DiscountedProductsCheckingPeriodInputElementInterface $discountedProductsCheckingPeriodInputElement,
        private LowestPriceFlagElementInterface $lowestPriceFlagElement,
        private ExcludeTaxonsFromShowingLowestPriceInputElementInterface $excludeTaxonsFromShowingLowestPriceInputElement,
    ) {
    }

    /**
     * @When I want to create a new channel
     */
    public function iWantToCreateANewChannel(): void
    {
        $this->createPage->open();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(?string $code = null): void
    {
        $this->createPage->specifyCode($code ?? '');
    }

    /**
     * @When I name it :name
     * @When I rename it to :name
     * @When I do not name it
     * @When I remove its name
     */
    public function iNameIt(?string $name = null): void
    {
        $this->createPage->nameIt($name ?? '');
    }

    /**
     * @When I specify its name as a too long string
     */
    public function iSpecifyItsNameAsATooLongString(): void
    {
        $this->createPage->nameIt($this->getTooLongString());
    }

    /**
     * @When I choose :currency as the base currency
     * @When I do not choose base currency
     */
    public function iChooseAsABaseCurrency(?CurrencyInterface $currency = null): void
    {
        $this->createPage->chooseBaseCurrency($currency ? $currency->getName() : null);
    }

    /**
     * @When I choose :defaultLocaleName as a default locale
     * @When I do not choose default locale
     */
    public function iChooseAsADefaultLocale(?string $defaultLocaleName = null): void
    {
        $this->createPage->chooseDefaultLocale($defaultLocaleName);
    }

    /**
     * @When I choose :firstCountry and :secondCountry as operating countries
     */
    public function iChooseOperatingCountries(string ...$countries): void
    {
        $this->createPage->chooseOperatingCountries($countries);
    }

    /**
     * @When I specify menu taxon as :menuTaxon
     */
    public function iSpecifyMenuTaxonAs(string $menuTaxon): void
    {
        $this->createPage->specifyMenuTaxon($menuTaxon);
    }

    /**
     * @When I change its menu taxon to :menuTaxon
     */
    public function iChangeItsMenuTaxonTo(string $menuTaxon): void
    {
        $this->updatePage->changeMenuTaxon($menuTaxon);
    }

    /**
     * @When I allow to skip shipping step if only one shipping method is available
     */
    public function iAllowToSkipShippingStepIfOnlyOneShippingMethodIsAvailable(): void
    {
        $this->createPage->allowToSkipShippingStep();
    }

    /**
     * @When I allow to skip payment step if only one payment method is available
     */
    public function iAllowToSkipPaymentStepIfOnlyOnePaymentMethodIsAvailable(): void
    {
        $this->createPage->allowToSkipPaymentStep();
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
     * @When /^I choose (billing|shipping) address as a required address in the checkout$/
     */
    public function iChooseAddressAsARequiredAddressInTheCheckout(string $type): void
    {
        $this->shippingAddressInCheckoutRequiredElement->requireAddressTypeInCheckout($type);
    }

    /**
     * @Then I should see the channel :channelName in the list
     * @Then the channel :channelName should appear in the registry
     * @Then the channel :channelName should be in the registry
     */
    public function theChannelShouldAppearInTheRegistry(string $channelName): void
    {
        $this->iWantToBrowseChannels();

        Assert::true($this->indexPage->isSingleResourceOnPage(['nameAndDescription' => $channelName]));
    }

    /**
     * @Then /^(this channel) should still be in the registry$/
     */
    public function thisChannelShouldAppearInTheRegistry(ChannelInterface $channel): void
    {
        $this->theChannelShouldAppearInTheRegistry($channel->getName());
    }

    /**
     * @When I describe it as :description
     */
    public function iDescribeItAs(string $description): void
    {
        $this->createPage->describeItAs($description);
    }

    /**
     * @When I set its hostname as :hostname
     */
    public function iSetItsHostnameAs(string $hostname): void
    {
        $this->createPage->setHostname($hostname);
    }

    /**
     * @When I specify its hostname as a too long string
     */
    public function iSpecifyItsHostnameAsATooLongString(): void
    {
        $this->createPage->setHostname($this->getTooLongString());
    }

    /**
     * @When I set its contact email as :contactEmail
     */
    public function iSetItsContactEmailAs(string $contactEmail): void
    {
        $this->createPage->setContactEmail($contactEmail);
    }

    /**
     * @When I specify its contact email as a too long string
     */
    public function iSpecifyItsContactEmailAsATooLongString(): void
    {
        $this->createPage->setContactEmail($this->getTooLongString());
    }

    /**
     * @When I set its contact phone number as :contactPhoneNumber
     */
    public function iSetItsContactPhoneNumberAs(string $contactPhoneNumber): void
    {
        $this->createPage->setContactPhoneNumber($contactPhoneNumber);
    }

    /**
     * @When I specify its contact phone number as a too long string
     */
    public function iSpecifyItsContactPhoneNumberAsATooLongString(): void
    {
        $this->createPage->setContactPhoneNumber($this->getTooLongString());
    }

    /**
     * @When I define its color as :color
     */
    public function iDefineItsColorAs(string $color): void
    {
        $this->createPage->defineColor($color);
    }

    /**
     * @When I specify its color as a too long string
     */
    public function iSpecifyItsColorAsATooLongString(): void
    {
        $this->createPage->defineColor($this->getTooLongString());
    }

    /**
     * @When I enable it
     */
    public function iEnableIt(): void
    {
        $this->updatePage->enable();
    }

    /**
     * @When I disable it
     */
    public function iDisableIt(): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->disable();
    }

    /**
     * @When I exclude the :taxon taxon from showing the lowest price of discounted products
     */
    public function iExcludeTheTaxonFromShowingTheLowestPriceOfDiscountedProducts(TaxonInterface $taxon): void
    {
        $this->excludeTaxonsFromShowingLowestPriceInputElement->excludeTaxon($taxon);
    }

    /**
     * @When /^I exclude the ("([^"]+)" and "([^"]+)" taxons) from showing the lowest price of discounted products$/
     */
    public function iExcludeTheTaxonsFromShowingTheLowestPriceOfDiscountedProducts(iterable $taxons): void
    {
        foreach ($taxons as $taxon) {
            $this->excludeTaxonsFromShowingLowestPriceInputElement->excludeTaxon($taxon);
        }
    }

    /**
     * @When I remove the :taxon taxon from excluded taxons from showing the lowest price of discounted products
     */
    public function iRemoveTheTaxonFromExcludedTaxonsFromShowingTheLowestPriceOfDiscountedProducts(TaxonInterface $taxon): void
    {
        $this->excludeTaxonsFromShowingLowestPriceInputElement->removeExcludedTaxon($taxon);
    }

    /**
     * @Then I should be notified that at least one channel has to be defined
     */
    public function iShouldBeNotifiedThatAtLeastOneChannelHasToBeDefinedIsRequired(): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage('enabled'), 'Must have at least one enabled entity');
    }

    /**
     * @Then channel with :element :value should not be added
     */
    public function channelWithShouldNotBeAdded(string $element, string $value): void
    {
        $this->iWantToBrowseChannels();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $value]));
    }

    /**
     * @Then /^I should be notified that ([^"]+) is required$/
     */
    public function iShouldBeNotifiedThatIsRequired(string $element): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same(
            $currentPage->getValidationMessage(StringInflector::nameToCode($element)),
            sprintf('Please enter channel %s.', $element),
        );
    }

    /**
     * @Given I am modifying a channel :channel
     * @When I want to modify a channel :channel
     * @When /^I want to modify (this channel)$/
     * @When I want to modify a billing data of channel :channel
     */
    public function iWantToModifyChannel(ChannelInterface $channel): void
    {
        $this->updatePage->open(['id' => $channel->getId()]);
    }

    /**
     * @Then /^(this channel) name should be "([^"]+)"$/
     * @Then /^(this channel) should still be named "([^"]+)"$/
     */
    public function thisChannelNameShouldBe(ChannelInterface $channel, string $channelName): void
    {
        $this->iWantToBrowseChannels();

        Assert::true($this->indexPage->isSingleResourceOnPage([
            'code' => $channel->getCode(),
            'nameAndDescription' => $channelName,
        ]));
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     * @When I save it
     */
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then I should be notified that channel with this code already exists
     */
    public function iShouldBeNotifiedThatChannelWithThisCodeAlreadyExists(): void
    {
        Assert::same($this->createPage->getValidationMessage('code'), 'Channel code has to be unique.');
    }

    /**
     * @Then there should still be only one channel with :element :value
     */
    public function thereShouldStillBeOnlyOneChannelWithCode(string $element, string $value): void
    {
        $this->iWantToBrowseChannels();

        Assert::true($this->indexPage->isSingleResourceOnPage([$element => $value]));
    }

    /**
     * @When I browse channels
     * @When I want to browse channels
     */
    public function iWantToBrowseChannels(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When I check (also) the :channelName channel
     */
    public function iCheckTheChannel(string $channelName): void
    {
        $this->indexPage->checkResourceOnPage(['nameAndDescription' => $channelName]);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @Then I should see a single channel in the list
     * @Then I should see :numberOfChannels channels in the list
     */
    public function iShouldSeeChannelsInTheList(int $numberOfChannels = 1): void
    {
        Assert::same($this->indexPage->countItems(), $numberOfChannels);
    }

    /**
     * @Then the code field should be disabled
     * @Then I should not be able to edit its code
     */
    public function theCodeFieldShouldBeDisabled(): void
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @Then /^(this channel) should be disabled$/
     */
    public function thisChannelShouldBeDisabled(ChannelInterface $channel): void
    {
        $this->assertChannelState($channel, false);
    }

    /**
     * @Then /^(this channel) should be enabled$/
     * @Then channel with name :channel should still be enabled
     */
    public function thisChannelShouldBeEnabled(ChannelInterface $channel): void
    {
        $this->assertChannelState($channel, true);
    }

    /**
     * @When I delete channel :channel
     */
    public function iDeleteChannel(ChannelInterface $channel): void
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['nameAndDescription' => $channel->getName()]);
    }

    /**
     * @Then the :channelName channel should no longer exist in the registry
     */
    public function thisChannelShouldNoLongerExistInTheRegistry(string $channelName): void
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['nameAndDescription' => $channelName]));
    }

    /**
     * @Then I should be notified that it cannot be deleted
     */
    public function iShouldBeNotifiedThatItCannotBeDeleted(): void
    {
        $this->notificationChecker->checkNotification(
            'The channel cannot be deleted. At least one enabled channel is required.',
            NotificationType::failure(),
        );
    }

    /**
     * @When I make it available (only) in :nameOfLocale
     */
    public function iMakeItAvailableIn(string $nameOfLocale): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->chooseLocale($nameOfLocale);
    }

    /**
     * @Then the channel :channel should be available in :nameOfLocale
     */
    public function theChannelShouldBeAvailableIn(ChannelInterface $channel, string $nameOfLocale): void
    {
        $this->updatePage->open(['id' => $channel->getId()]);

        Assert::true($this->updatePage->isLocaleChosen($nameOfLocale));
    }

    /**
     * @When I allow for paying in :currencyCode
     */
    public function iAllowToPayingForThisChannel(string $currencyCode): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->chooseCurrency($currencyCode);
    }

    /**
     * @Then paying in :currencyCode should be possible for the :channel channel
     */
    public function payingInCurrencyShouldBePossibleForTheChannel(string $currencyCode, ChannelInterface $channel): void
    {
        $this->updatePage->open(['id' => $channel->getId()]);

        Assert::true($this->updatePage->isCurrencyChosen($currencyCode));
    }

    /**
     * @When I select the :taxZone as default tax zone
     */
    public function iSelectDefaultTaxZone(string $taxZone): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->chooseDefaultTaxZone($taxZone);
    }

    /**
     * @Given I remove its default tax zone
     */
    public function iRemoveItsDefaultTaxZone(): void
    {
        $this->updatePage->chooseDefaultTaxZone('');
    }

    /**
     * @When I select the :taxCalculationStrategy as tax calculation strategy
     */
    public function iSelectTaxCalculationStrategy(string $taxCalculationStrategy): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->chooseTaxCalculationStrategy($taxCalculationStrategy);
    }

    /**
     * @When /^I specify (-?\d+) days as the lowest price for discounted products checking period$/
     */
    public function iSpecifyDaysAsTheLowestPriceForDiscountedProductsCheckingPeriod(int $days): void
    {
        $this->discountedProductsCheckingPeriodInputElement->specifyPeriod($days);
    }

    /**
     * @Then /^the "[^"]+" channel should have the lowest price for discounted products checking period set to (\d+) days$/
     * @Then its lowest price for discounted products checking period should be set to :days days
     */
    public function theChannelShouldHaveTheLowestPriceForDiscountedProductsCheckingPeriodSetToDays(int $days): void
    {
        $lowestPriceForDiscountedProductsCheckingPeriod = $this->discountedProductsCheckingPeriodInputElement->getPeriod();

        Assert::same($days, $lowestPriceForDiscountedProductsCheckingPeriod);
    }

    /**
     * @Then I should be notified that the lowest price for discounted products checking period must be lower
     */
    public function iShouldBeNotifiedThatTheLowestPriceForDiscountedProductsCheckingPeriodMustBeLower(): void
    {
        Assert::same(
            'Value must be less than 2147483647',
            $this->updatePage->getValidationMessage('discounted_products_checking_period'),
        );
    }

    /**
     * @When /^I (enable|disable) showing the lowest price of discounted products$/
     */
    public function iEnableShowingTheLowestPriceOfDiscountedProducts(string $visible): void
    {
        $this->lowestPriceFlagElement->$visible();
    }

    /**
     * @Then I should be notified that the lowest price for discounted products checking period must be greater than 0
     */
    public function iShouldBeNotifiedThatTheLowestPriceForDiscountedProductsCheckingPeriodMustBeGreaterThanZero(): void
    {
        Assert::same(
            'Value must be greater than 0',
            $this->updatePage->getValidationMessage('discounted_products_checking_period'),
        );
    }

    /**
     * @Then /^the ("[^"]+" channel) should have the lowest price of discounted products prior to the current discount (enabled|disabled)$/
     */
    public function theChannelShouldHaveTheLowestPriceOfDiscountedProductsPriorToTheCurrentDiscountEnabledOrDisabled(
        ChannelInterface $channel,
        string $visible,
    ): void {
        Assert::same(
            'enabled' === $visible,
            $this->lowestPriceFlagElement->isEnabled(),
        );
    }

    /**
     * @Then /^this channel should have ("([^"]+)" and "([^"]+)" taxons) excluded from displaying the lowest price of discounted products$/
     */
    public function thisChannelShouldHaveTaxonsExcludedFromDisplayingTheLowestPriceOfDiscountedProducts(iterable $taxons): void
    {
        foreach ($taxons as $taxon) {
            Assert::true(
                $this->excludeTaxonsFromShowingLowestPriceInputElement->hasTaxonExcluded($taxon),
                sprintf('The taxon with code %s should be excluded from displaying the lowest price of discounted products', $taxon->getCode()),
            );
        }
    }

    /**
     * @Then the default tax zone for the :channel channel should be :taxZone
     */
    public function theDefaultTaxZoneForTheChannelShouldBe(ChannelInterface $channel, string $taxZone): void
    {
        $this->updatePage->open(['id' => $channel->getId()]);

        Assert::true($this->updatePage->isDefaultTaxZoneChosen($taxZone));
    }

    /**
     * @Given channel :channel should not have default tax zone
     */
    public function channelShouldNotHaveDefaultTaxZone(ChannelInterface $channel): void
    {
        $this->updatePage->open(['id' => $channel->getId()]);

        Assert::false($this->updatePage->isAnyDefaultTaxZoneChosen());
    }

    /**
     * @Then the tax calculation strategy for the :channel channel should be :taxCalculationStrategy
     */
    public function theTaxCalculationStrategyForTheChannelShouldBe(
        ChannelInterface $channel,
        string $taxCalculationStrategy,
    ): void {
        $this->updatePage->open(['id' => $channel->getId()]);

        Assert::true($this->updatePage->isTaxCalculationStrategyChosen($taxCalculationStrategy));
    }

    /**
     * @Then the base currency field should be disabled
     * @Then I should not be able to edit its base currency
     */
    public function theBaseCurrencyFieldShouldBeDisabled(): void
    {
        Assert::true($this->updatePage->isBaseCurrencyDisabled());
    }

    /**
     * @Then I should be notified that the default locale has to be enabled
     */
    public function iShouldBeNotifiedThatTheDefaultLocaleHasToBeEnabled(): void
    {
        Assert::same(
            $this->updatePage->getValidationMessage('default_locale'),
            'Default locale has to be enabled.',
        );
    }

    /**
     * @Given /^(this channel) menu taxon should be "([^"]+)"$/
     * @Given the channel :channel should have :menuTaxon as a menu taxon
     */
    public function thisChannelMenuTaxonShouldBe(ChannelInterface $channel, string $menuTaxon): void
    {
        if (!$this->updatePage->isOpen(['id' => $channel->getId()])) {
            $this->updatePage->open(['id' => $channel->getId()]);
        }

        Assert::same($this->updatePage->getMenuTaxon(), $menuTaxon);
    }

    /**
     * @Then this channel should have :taxon taxon excluded from displaying the lowest price of discounted products
     */
    public function thisChannelShouldHaveTaxonExcludedFromDisplayingTheLowestPriceOfDiscountedProducts(
        TaxonInterface $taxon,
    ): void {
        Assert::true(
            $this->excludeTaxonsFromShowingLowestPriceInputElement->hasTaxonExcluded($taxon),
            sprintf('The taxon with code %s should be excluded from displaying the lowest price of discounted products', $taxon->getCode()),
        );
    }

    /**
     * @Then this channel should not have :taxon taxon excluded from displaying the lowest price of discounted products
     */
    public function thisChannelShouldNotHaveTaxonExcludedFromDisplayingTheLowestPriceOfDiscountedProducts(
        TaxonInterface $taxon,
    ): void {
        Assert::false(
            $this->excludeTaxonsFromShowingLowestPriceInputElement->hasTaxonExcluded($taxon),
            sprintf('The taxon with code %s should be not be excluded from displaying the lowest price of discounted products', $taxon->getCode()),
        );
    }

    /**
     * @Then /^the required address in the checkout for this channel should be (billing|shipping)$/
     */
    public function theRequiredAddressInTheCheckoutForThisChannelShouldBe(string $type): void
    {
        Assert::same($this->shippingAddressInCheckoutRequiredElement->getRequiredAddressTypeInCheckout(), $type);
    }

    private function assertChannelState(ChannelInterface $channel, bool $state): void
    {
        $this->iWantToBrowseChannels();

        Assert::true($this->indexPage->isSingleResourceOnPage([
            'nameAndDescription' => $channel->getName(),
            'enabled' => $state ? 'Enabled' : 'Disabled',
        ]));
    }

    private function getTooLongString(): string
    {
        return str_repeat('a@', 128);
    }

    protected function resolveCurrentPage(): SymfonyPageInterface
    {
        return $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);
    }
}
