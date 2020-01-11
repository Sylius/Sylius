<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Channel\CreatePageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Page\TestPlugin\MainPageInterface;
use Sylius\Behat\Service\Setter\ChannelContextSetterInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

final class ChannelContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var ChannelContextSetterInterface */
    private $channelContextSetter;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var CreatePageInterface */
    private $channelCreatePage;

    /** @var HomePageInterface */
    private $homePage;

    /** @var MainPageInterface */
    private $pluginMainPage;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ChannelContextSetterInterface $channelContextSetter,
        ChannelRepositoryInterface $channelRepository,
        CreatePageInterface $channelCreatePage,
        HomePageInterface $homePage,
        MainPageInterface $pluginMainPage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->channelContextSetter = $channelContextSetter;
        $this->channelRepository = $channelRepository;
        $this->channelCreatePage = $channelCreatePage;
        $this->homePage = $homePage;
        $this->pluginMainPage = $pluginMainPage;
    }

    /**
     * @Given /^I changed (?:|back )my current (channel to "([^"]+)")$/
     * @When /^I change (?:|back )my current (channel to "([^"]+)")$/
     */
    public function iChangeMyCurrentChannelTo(ChannelInterface $channel): void
    {
        $this->channelContextSetter->setChannel($channel);
    }

    /**
     * @When I create a new channel :channelName
     */
    public function iCreateNewChannel(string $channelName): void
    {
        $this->channelCreatePage->open();
        $this->channelCreatePage->nameIt($channelName);
        $this->channelCreatePage->specifyCode($channelName);
        $this->channelCreatePage->create();

        $channel = $this->channelRepository->findOneBy(['name' => $channelName]);
        $this->sharedStorage->set('channel', $channel);
    }

    /**
     * @When /^I visit (this channel)'s homepage$/
     * @When /^I (?:am browsing|start browsing|try to browse|browse) (that channel)$/
     * @When /^I (?:am browsing|start browsing|try to browse|browse) (?:|the )("[^"]+" channel)$/
     * @When /^I (?:am browsing|start browsing|try to browse|browse) (?:|the )(channel "[^"]+")$/
     */
    public function iVisitChannelHomepage(ChannelInterface $channel): void
    {
        $this->channelContextSetter->setChannel($channel);

        $defaultLocale = $channel->getDefaultLocale();
        $this->homePage->open(['_locale' => $defaultLocale->getCode()]);
    }

    /**
     * @When I visit plugin's main page
     */
    public function visitPluginMainPage(): void
    {
        $this->pluginMainPage->open();
    }

    /**
     * @Then I should see a plugin's main page with content :content
     */
    public function shouldSeePluginMainPageWithContent(string $content): void
    {
        Assert::same($this->pluginMainPage->getContent(), $content);
    }
}
