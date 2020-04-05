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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\ThemeBundle\Configuration\Test\TestThemeConfigurationManagerInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class ThemeContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var ThemeRepositoryInterface */
    private $themeRepository;

    /** @var ObjectManager */
    private $channelManager;

    /** @var TestThemeConfigurationManagerInterface */
    private $testThemeConfigurationManager;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ThemeRepositoryInterface $themeRepository,
        ObjectManager $channelManager,
        TestThemeConfigurationManagerInterface $testThemeConfigurationManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->themeRepository = $themeRepository;
        $this->channelManager = $channelManager;
        $this->testThemeConfigurationManager = $testThemeConfigurationManager;
    }

    /**
     * @Given the store has :themeName theme
     */
    public function storeHasTheme($themeName)
    {
        $this->testThemeConfigurationManager->add([
            'name' => $themeName,
        ]);

        $this->sharedStorage->set('theme', $this->themeRepository->findOneByName($themeName));
    }

    /**
     * @Given channel :channel uses :theme theme
     */
    public function channelUsesTheme(ChannelInterface $channel, ThemeInterface $theme)
    {
        $channel->setThemeName($theme->getName());

        $this->channelManager->persist($channel);
        $this->channelManager->flush();

        $this->sharedStorage->set('channel', $channel);
        $this->sharedStorage->set('theme', $theme);
    }

    /**
     * @Given channel :channel does not use any theme
     */
    public function channelDoesNotUseAnyTheme(ChannelInterface $channel)
    {
        $channel->setThemeName(null);

        $this->channelManager->flush();

        $this->sharedStorage->set('channel', $channel);
    }

    /**
     * @Given /^(this theme) changes homepage template contents to "([^"]+)"$/
     */
    public function themeChangesHomepageTemplateContents(ThemeInterface $theme, $contents)
    {
        $this->changeTemplateContents('/SyliusShopBundle/views/Homepage/index.html.twig', $theme, $contents);
    }

    /**
     * @Given /^(this theme) changes plugin main template's content to "([^"]+)"$/
     */
    public function themeChangesPluginMainTemplateContent(ThemeInterface $theme, string $content): void
    {
        $this->changeTemplateContents('/SyliusTestPlugin/views/main.html.twig', $theme, $content);
    }

    private function changeTemplateContents(string $templatePath, ThemeInterface $theme, string $contents): void
    {
        $file = rtrim($theme->getPath(), '/') . $templatePath;
        $dir = dirname($file);

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($file, $contents);
    }
}
