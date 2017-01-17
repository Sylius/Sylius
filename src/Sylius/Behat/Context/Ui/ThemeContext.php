<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Channel\IndexPageInterface;
use Sylius\Behat\Page\Admin\Channel\UpdatePageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var IndexPageInterface
     */
    private $channelIndexPage;

    /**
     * @var UpdatePageInterface
     */
    private $channelUpdatePage;

    /**
     * @var HomePageInterface
     */
    private $homePage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param IndexPageInterface $channelIndexPage
     * @param UpdatePageInterface $channelUpdatePage
     * @param HomePageInterface $homePage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        IndexPageInterface $channelIndexPage,
        UpdatePageInterface $channelUpdatePage,
        HomePageInterface $homePage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->channelIndexPage = $channelIndexPage;
        $this->channelUpdatePage = $channelUpdatePage;
        $this->homePage = $homePage;
    }

    /**
     * @When I set :channel channel theme to :theme
     */
    public function iSetChannelThemeTo(ChannelInterface $channel, ThemeInterface $theme)
    {
        $this->channelUpdatePage->open(['id' => $channel->getId()]);
        $this->channelUpdatePage->setTheme($theme);
        $this->channelUpdatePage->saveChanges();

        $this->sharedStorage->set('channel', $channel);
        $this->sharedStorage->set('theme', $theme);
    }

    /**
     * @When /^I unset theme on (that channel)$/
     */
    public function iUnsetThemeOnChannel(ChannelInterface $channel)
    {
        $this->channelUpdatePage->open(['id' => $channel->getId()]);
        $this->channelUpdatePage->unsetTheme();
        $this->channelUpdatePage->saveChanges();
    }

    /**
     * @Then /^(that channel) should not use any theme$/
     */
    public function channelShouldNotUseAnyTheme(ChannelInterface $channel)
    {
        $this->channelIndexPage->open();

        Assert::same($this->channelIndexPage->getUsedThemeName($channel->getCode()), 'Default');
    }

    /**
     * @Then /^(that channel) should use (that theme)$/
     */
    public function channelShouldUseTheme(ChannelInterface $channel, ThemeInterface $theme)
    {
        $this->channelIndexPage->open();

        Assert::same($this->channelIndexPage->getUsedThemeName($channel->getCode()), $theme->getName());
    }

    /**
     * @Then /^I should see a homepage from ((?:this|that) theme)$/
     */
    public function iShouldSeeThemedHomepage(ThemeInterface $theme)
    {
        $content = file_get_contents(rtrim($theme->getPath(), '/') . '/SyliusShopBundle/views/Homepage/index.html.twig');

        Assert::same($this->homePage->getContents(), $content);
    }

    /**
     * @Then I should not see a homepage from :theme theme
     */
    public function iShouldNotSeeThemedHomepage(ThemeInterface $theme)
    {
        $content = file_get_contents(rtrim($theme->getPath(), '/') . '/SyliusShopBundle/views/Homepage/index.html.twig');

        Assert::notSame($this->homePage->getContents(), $content);
    }
}
