<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Theme;

use Doctrine\DBAL\DBALException;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelBasedThemeContext implements ThemeContextInterface
{
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @param ChannelContextInterface $channelContext
     * @param ThemeRepositoryInterface $themeRepository
     */
    public function __construct(ChannelContextInterface $channelContext, ThemeRepositoryInterface $themeRepository)
    {
        $this->channelContext = $channelContext;
        $this->themeRepository = $themeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getTheme()
    {
        $themeName = $this->getChannelThemeName();
        if (null === $themeName) {
            return null;
        }

        return $this->themeRepository->findOneByName($themeName);
    }

    /**
     * @return string|null
     */
    private function getChannelThemeName()
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            $themeName = $channel->getThemeName();
        } catch (ChannelNotFoundException $exception) {
            $themeName = null;
        } catch (\Exception $exception) {
            $themeName = null;
        }

        return $themeName;
    }
}
