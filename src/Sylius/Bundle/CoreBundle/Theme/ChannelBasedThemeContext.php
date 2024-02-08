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

namespace Sylius\Bundle\CoreBundle\Theme;

use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelBasedThemeContext implements ThemeContextInterface
{
    public function __construct(private ChannelContextInterface $channelContext, private ThemeRepositoryInterface $themeRepository)
    {
    }

    public function getTheme(): ?ThemeInterface
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();
            $themeName = $channel->getThemeName();

            if (null === $themeName) {
                return null;
            }

            return $this->themeRepository->findOneByName($themeName);
        } catch (ChannelNotFoundException) {
            return null;
        } catch (\Exception) {
            return null;
        }
    }
}
