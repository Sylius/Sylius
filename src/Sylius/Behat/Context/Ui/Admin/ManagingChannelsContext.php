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
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
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

        Assert::true($this->indexPage->isSingleResourceOnPage(
            ['name' => $channelName]),
            sprintf('Channel with name %s has not been found.', $channelName)
        );
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
     * @Then I should be notified that at least one channel has to be defined is required
     */
    public function iShouldBeNotifiedThatAtLeastOneChannelHasToBeDefinedIsRequired()
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::true(
            $currentPage->checkValidationMessageFor('enabled', 'Must have at least one enabled entity'),
            sprintf('Channels enabled field should be required.')
        );
    }

    /**
     * @Then channel with :element :value should not be added
     */
    public function channelWithShouldNotBeAdded($element, $value)
    {
        $this->iWantToBrowseChannels();

        Assert::false(
            $this->indexPage->isSingleResourceOnPage([$element => $value]),
            sprintf('Channel with %s "%s" was created, but it should not.', $element, $value)
        );
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::true(
            $currentPage->checkValidationMessageFor($element, sprintf('Please enter channel %s.', $element)),
            sprintf('Tax category %s should be required.', $element)
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

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(
                [
                    'code' => $channel->getCode(),
                    'name' => $channelName,
                ]
            ),
            sprintf('Channel name %s has not been assigned properly.', $channelName)
        );
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
        Assert::true(
            $this->createPage->checkValidationMessageFor('code', 'Channel code has to be unique.'),
            'Unique code violation message should appear on page, but it does not.'
        );
    }

    /**
     * @Then there should still be only one channel with :element :value
     */
    public function thereShouldStillBeOnlyOneChannelWithCode($element, $value)
    {
        $this->iWantToBrowseChannels();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage([$element => $value]),
            sprintf('Channel with %s %s cannot be found.', $element, $value)
        );
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
        $foundRows = $this->indexPage->countItems();

        Assert::eq(
            (int) $numberOfChannels,
            $foundRows,
            sprintf('%s rows with channels should appear on page, %s rows has been found', $numberOfChannels, $foundRows)
        );
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true(
            $this->updatePage->isCodeDisabled(),
            'Code should be immutable, but it does not.'
        );
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
        $this->indexPage->deleteResourceOnPage(['name' => $channel->getName()]);
    }

    /**
     * @Then the :channelName channel should no longer exist in the registry
     */
    public function thisChannelShouldNoLongerExistInTheRegistry($channelName)
    {
        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['name' => $channelName]),
            sprintf('Channel with name %s exists but should not.', $channelName)
        );
    }

    /**
     * @Then I should be notified that it cannot be deleted
     */
    public function iShouldBeNotifiedThatItCannotBeDeleted()
    {
        $this->notificationChecker->checkNotification(
            "The channel cannot be deleted. At least one enabled channel is required.", 
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

        Assert::true(
            $this->updatePage->isLocaleChosen($locale),
            sprintf('Language %s should be selected but it is not', $locale)
        );
    }

    /**
     * @When I allow for paying in :currency
     */
    public function iAllowToPayingForThisChannel($currency)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->chooseCurrency($currency);
    }

    /**
     * @Then paying in :currency should be possible for the :channel channel
     */
    public function payingInEuroShouldBePossibleForTheChannel($currency, ChannelInterface $channel)
    {
        $this->updatePage->open(['id' => $channel->getId()]);

        Assert::true(
            $this->updatePage->isCurrencyChosen($currency),
            sprintf('Currency %s should be selected but it is not', $currency)
        );
    }

    /**
     * @When I select the :shippingMethodName shipping method
     */
    public function iSelectTheShippingMethod($shippingMethodName)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->chooseShippingMethod($shippingMethodName);
    }

    /**
     * @Then the :shippingMethodName shipping method should be available for the :channel channel
     */
    public function theShippingMethodShouldBeAvailableForTheChannel($shippingMethodName, ChannelInterface $channel)
    {
        $this->updatePage->open(['id' => $channel->getId()]);

        Assert::true(
            $this->updatePage->isShippingMethodChosen($shippingMethodName),
            sprintf('Shipping method %s should be selected but it is not', $shippingMethodName)
        );
    }

    /**
     * @When I select the :paymentMethodName payment method
     */
    public function iSelectThePaymentMethod($paymentMethodName)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->choosePaymentMethod($paymentMethodName);
    }

    /**
     * @Then the :paymentMethodName payment method should be available for the :channel channel
     */
    public function thePaymentMethodShouldBeAvailableForTheChannel($paymentMethodName, ChannelInterface $channel)
    {
        $this->updatePage->open(['id' => $channel->getId()]);

        Assert::true(
            $this->updatePage->isPaymentMethodChosen($paymentMethodName),
            sprintf('Payment method %s should be selected but it is not', $paymentMethodName)
        );
    }

    /**
     * @param ChannelInterface $channel
     * @param bool $state
     */
    private function assertChannelState(ChannelInterface $channel, $state)
    {
        $this->iWantToBrowseChannels();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(
                [
                    'name' => $channel->getName(),
                    'enabled' => $state,
                ]
            ), sprintf('Channel with name %s and state %s has not been found.', $channel->getName(), $state)
        );
    }
}
