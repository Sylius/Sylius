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
use Sylius\Behat\Page\Shop\LegacyHomePageInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

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
     * @var LegacyHomePageInterface
     */
    private $homePage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param IndexPageInterface $channelIndexPage
     * @param UpdatePageInterface $channelUpdatePage
     * @param LegacyHomePageInterface $homePage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        IndexPageInterface $channelIndexPage,
        UpdatePageInterface $channelUpdatePage,
        LegacyHomePageInterface $homePage
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

        expect($this->channelIndexPage->getUsedThemeName($channel->getCode()))->toBe('');
    }

    /**
     * @Then /^(that channel) should use (that theme)$/
     */
    public function channelShouldUseTheme(ChannelInterface $channel, ThemeInterface $theme)
    {
        $this->channelIndexPage->open();

        expect($this->channelIndexPage->getUsedThemeName($channel->getCode()))->toBe($theme->getName());
    }

    /**
     * @Then /^I should see a homepage from ((?:this|that) theme)$/
     */
    public function iShouldSeeThemedHomepage(ThemeInterface $theme)
    {
        $content = file_get_contents(rtrim($theme->getPath(), '/') . '/SyliusWebBundle/views/Frontend/Homepage/main.html.twig');

        expect($this->homePage->getContents())->toBe($content);
    }

    /**
     * @Then I should not see a homepage from :theme theme
     */
    public function iShouldNotSeeThemedHomepage(ThemeInterface $theme)
    {
        $content = file_get_contents(rtrim($theme->getPath(), '/') . '/SyliusWebBundle/views/Frontend/Homepage/main.html.twig');

        expect($this->homePage->getContents())->notToBe($content);
    }
}
