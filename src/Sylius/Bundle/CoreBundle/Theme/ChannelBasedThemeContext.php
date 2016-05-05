<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Theme;

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
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            return $this->themeRepository->findOneByName($channel->getThemeName());
        } catch (ChannelNotFoundException $exception) {
            return null;
        } catch (\Exception $exception) {
            return null;
        }
    }
}
