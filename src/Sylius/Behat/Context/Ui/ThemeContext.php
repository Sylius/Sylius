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
use Sylius\Behat\Page\Admin\Channel\IndexPageInterface;
use Sylius\Behat\Page\Admin\Channel\UpdatePageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

final class ThemeContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private IndexPageInterface $channelIndexPage,
        private UpdatePageInterface $channelUpdatePage,
        private HomePageInterface $homePage,
    ) {
    }

    /**
     * @When I set :channel channel theme to :theme
     */
    public function iSetChannelThemeTo(ChannelInterface $channel, ThemeInterface $theme)
    {
        $this->channelUpdatePage->open(['id' => $channel->getId()]);
        $this->channelUpdatePage->setTheme($theme->getName());
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
        $content = file_get_contents(rtrim($theme->getPath(), '/') . '/templates/bundles/SyliusShopBundle/Homepage/index.html.twig');

        Assert::same($this->homePage->getContent(), $content);
    }

    /**
     * @Then I should not see a homepage from :theme theme
     */
    public function iShouldNotSeeThemedHomepage(ThemeInterface $theme)
    {
        $content = file_get_contents(rtrim($theme->getPath(), '/') . '/templates/bundles/SyliusShopBundle/Homepage/index.html.twig');

        Assert::notSame($this->homePage->getContent(), $content);
    }
}
