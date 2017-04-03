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
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Channel\CreatePageInterface;
use Sylius\Behat\Page\Admin\Channel\IndexPageInterface;
use Sylius\Behat\Page\Admin\Channel\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingChannelsContext implements Context
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
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given I want to create a new channel
     */
    public function iWantToCreateANewChannel()
    {
        $this->createPage->open();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null)
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I name it :name
     * @When I rename it to :name
     * @When I do not name it
     * @When I remove its name
     */
    public function iNameIt($name = null)
    {
        $this->createPage->nameIt($name);
    }

    /**
     * @When I choose :currency as the base currency
     * @When I do not choose base currency
     */
    public function iChooseAsABaseCurrency($currency = null)
    {
        $this->createPage->chooseBaseCurrency($currency);
    }

    /**
     * @When I choose :locale as a default locale
     * @When I do not choose default locale
     */
    public function iChooseAsADefaultLocale($locale = null)
    {
        $this->createPage->chooseDefaultLocale($locale);
    }

    /**
     * @When I allow to skip shipping step if only one shipping method is available
     */
    public function iAllowToSkipShippingStepIfOnlyOneShippingMethodIsAvailable()
    {
        $this->createPage->allowToSkipShippingStep();
    }

    /**
     * @When I allow to skip payment step if only one payment method is available
     */
    public function iAllowToSkipPaymentStepIfOnlyOnePaymentMethodIsAvailable()
    {
        $this->createPage->allowToSkipPaymentStep();
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
     * @Then the channel :channelName should appear in the registry
     * @Then the channel :channelName should be in the registry
     */
    public function theChannelShouldAppearInTheRegistry($channelName)
    {
        $this->iWantToBrowseChannels();

        Assert::true($this->indexPage->isSingleResourceOnPage(['nameAndDescription' => $channelName]));
    }

    /**
     * @Then /^(this channel) should still be in the registry$/
     */
    public function thisChannelShouldAppearInTheRegistry(ChannelInterface $channel)
    {
        $this->theChannelShouldAppearInTheRegistry($channel->getName());
    }

    /**
     * @When I describe it as :description
     */
    public function iDescribeItAs($description)
    {
        $this->createPage->describeItAs($description);
    }

    /**
     * @When I set its hostname as :hostname
     */
    public function iSetItsHostnameAs($hostname)
    {
        $this->createPage->setHostname($hostname);
    }

    /**
     * @When I set its contact email as :contactEmail
     */
    public function iSetItsContactEmailAs($contactEmail)
    {
        $this->createPage->setContactEmail($contactEmail);
    }

    /**
     * @When I define its color as :color
     */
    public function iDefineItsColorAs($color)
    {
        $this->createPage->defineColor($color);
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
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->disable();
    }

    /**
     * @Then I should be notified that at least one channel has to be defined
     */
    public function iShouldBeNotifiedThatAtLeastOneChannelHasToBeDefinedIsRequired()
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage('enabled'), 'Must have at least one enabled entity');
    }

    /**
     * @Then channel with :element :value should not be added
     */
    public function channelWithShouldNotBeAdded($element, $value)
    {
        $this->iWantToBrowseChannels();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $value]));
    }

    /**
     * @Then /^I should be notified that ([^"]+) is required$/
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same(
            $currentPage->getValidationMessage(StringInflector::nameToCode($element)),
            sprintf('Please enter channel %s.', $element)
        );
    }

    /**
     * @Given I want to modify a channel :channel
     * @Given /^I want to modify (this channel)$/
     */
    public function iWantToModifyChannel(ChannelInterface $channel)
    {
        $this->updatePage->open(['id' => $channel->getId()]);
    }

    /**
     * @Then /^(this channel) name should be "([^"]+)"$/
     * @Then /^(this channel) should still be named "([^"]+)"$/
     */
    public function thisChannelNameShouldBe(ChannelInterface $channel, $channelName)
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
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then I should be notified that channel with this code already exists
     */
    public function iShouldBeNotifiedThatChannelWithThisCodeAlreadyExists()
    {
        Assert::same($this->createPage->getValidationMessage('code'), 'Channel code has to be unique.');
    }

    /**
     * @Then there should still be only one channel with :element :value
     */
    public function thereShouldStillBeOnlyOneChannelWithCode($element, $value)
    {
        $this->iWantToBrowseChannels();

        Assert::true($this->indexPage->isSingleResourceOnPage([$element => $value]));
    }

    /**
     * @When /^I want to browse channels$/
     */
    public function iWantToBrowseChannels()
    {
        $this->indexPage->open();
    }

    /**
     * @Then I should see :numberOfChannels channels in the list
     */
    public function iShouldSeeChannelsInTheList($numberOfChannels)
    {
        Assert::same($this->indexPage->countItems(), (int) $numberOfChannels);
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @Then /^(this channel) should be disabled$/
     */
    public function thisChannelShouldBeDisabled(ChannelInterface $channel)
    {
        $this->assertChannelState($channel, false);
    }

    /**
     * @Then /^(this channel) should be enabled$/
     * @Then channel with name :channel should still be enabled
     */
    public function thisChannelShouldBeEnabled(ChannelInterface $channel)
    {
        $this->assertChannelState($channel, true);
    }

    /**
     * @When I delete channel :channel
     */
    public function iDeleteChannel(ChannelInterface $channel)
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['nameAndDescription' => $channel->getName()]);
    }

    /**
     * @Then the :channelName channel should no longer exist in the registry
     */
    public function thisChannelShouldNoLongerExistInTheRegistry($channelName)
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['nameAndDescription' => $channelName]));
    }

    /**
     * @Then I should be notified that it cannot be deleted
     */
    public function iShouldBeNotifiedThatItCannotBeDeleted()
    {
        $this->notificationChecker->checkNotification(
            'The channel cannot be deleted. At least one enabled channel is required.',
            NotificationType::failure()
        );
    }

    /**
     * @When I make it available in :locale
     */
    public function iMakeItAvailableIn($locale)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->chooseLocale($locale);
    }

    /**
     * @Then the channel :channel should be available in :locale
     */
    public function theChannelShouldBeAvailableIn(ChannelInterface $channel, $locale)
    {
        $this->updatePage->open(['id' => $channel->getId()]);

        Assert::true($this->updatePage->isLocaleChosen($locale));
    }

    /**
     * @When I allow for paying in :currencyCode
     */
    public function iAllowToPayingForThisChannel($currencyCode)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->chooseCurrency($currencyCode);
    }

    /**
     * @Then paying in :currencyCode should be possible for the :channel channel
     */
    public function payingInEuroShouldBePossibleForTheChannel($currencyCode, ChannelInterface $channel)
    {
        $this->updatePage->open(['id' => $channel->getId()]);

        Assert::true($this->updatePage->isCurrencyChosen($currencyCode));
    }

    /**
     * @When I select the :taxZone as default tax zone
     */
    public function iSelectDefaultTaxZone($taxZone)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->chooseDefaultTaxZone($taxZone);
    }

    /**
     * @Given I remove its default tax zone
     */
    public function iRemoveItsDefaultTaxZone()
    {
        $this->updatePage->chooseDefaultTaxZone(null);
    }

    /**
     * @When I select the :taxCalculationStrategy as tax calculation strategy
     */
    public function iSelectTaxCalculationStrategy($taxCalculationStrategy)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->chooseTaxCalculationStrategy($taxCalculationStrategy);
    }

    /**
     * @Then the default tax zone for the :channel channel should be :taxZone
     */
    public function theDefaultTaxZoneForTheChannelShouldBe(ChannelInterface $channel, $taxZone)
    {
        $this->updatePage->open(['id' => $channel->getId()]);

        Assert::true($this->updatePage->isDefaultTaxZoneChosen($taxZone));
    }

    /**
     * @Given channel :channel should not have default tax zone
     */
    public function channelShouldNotHaveDefaultTaxZone(ChannelInterface $channel)
    {
        $this->updatePage->open(['id' => $channel->getId()]);

        Assert::false($this->updatePage->isAnyDefaultTaxZoneChosen());
    }

    /**
     * @Then the tax calculation strategy for the :channel channel should be :taxCalculationStrategy
     */
    public function theTaxCalculationStrategyForTheChannelShouldBe(ChannelInterface $channel, $taxCalculationStrategy)
    {
        $this->updatePage->open(['id' => $channel->getId()]);

        Assert::true($this->updatePage->isTaxCalculationStrategyChosen($taxCalculationStrategy));
    }

    /**
     * @Then the base currency field should be disabled
     */
    public function theBaseCurrencyFieldShouldBeDisabled()
    {
        Assert::true($this->updatePage->isBaseCurrencyDisabled());
    }

    /**
     * @param ChannelInterface $channel
     * @param bool $state
     */
    private function assertChannelState(ChannelInterface $channel, $state)
    {
        $this->iWantToBrowseChannels();

        Assert::true($this->indexPage->isSingleResourceOnPage([
            'nameAndDescription' => $channel->getName(),
            'enabled' => $state,
        ]));
    }
}
