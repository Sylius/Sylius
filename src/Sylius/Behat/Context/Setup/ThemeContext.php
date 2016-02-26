<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
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
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @var ThemeFactoryInterface
     */
    private $themeFactory;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ThemeRepositoryInterface $themeRepository
     * @param ThemeFactoryInterface $themeFactory
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ThemeRepositoryInterface $themeRepository,
        ThemeFactoryInterface $themeFactory,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->themeRepository = $themeRepository;
        $this->themeFactory = $themeFactory;
        $this->channelRepository = $channelRepository;
    }

    /**
     * @Given there is :themeName theme defined
     */
    public function thereIsThemeDefined($themeName)
    {
        $theme = $this->themeFactory->createNamed($themeName);
        $theme->setTitle($themeName);
        $theme->setPath(__DIR__);

        $this->themeRepository->add($theme);
        $this->sharedStorage->set('theme', $theme);
    }

    /**
     * @Given /^("[^"]+" channel) is using ("[^"]+" theme)$/
     */
    public function channelIsUsingTheme(ChannelInterface $channel, ThemeInterface $theme)
    {
        $channel->setTheme($theme);

        $this->channelRepository->add($channel);

        $this->sharedStorage->set('channel', $channel);
        $this->sharedStorage->set('theme', $theme);
    }
}
