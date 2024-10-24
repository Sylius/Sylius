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
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private ChannelContextSetterInterface $channelContextSetter,
        private ChannelRepositoryInterface $channelRepository,
        private CreatePageInterface $channelCreatePage,
        private HomePageInterface $homePage,
        private MainPageInterface $pluginMainPage,
    ) {
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
        $this->sharedStorage->set('hostname', $channel->getHostname());

        $this->channelContextSetter->setChannel($channel);

        $defaultLocale = $channel->getDefaultLocale();
        $this->homePage->open(['_locale' => $defaultLocale->getCode()]);

        $this->sharedStorage->set('current_locale_code', $defaultLocale->getCode());
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
