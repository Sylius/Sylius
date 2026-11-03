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

namespace Sylius\Bundle\ShopBundle\Theme;

use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Contracts\Service\ResetInterface;

final class ChannelBasedThemeContext implements ThemeContextInterface, ResetInterface
{
    private false|ThemeInterface|null $theme = false;

    public function __construct(private ChannelContextInterface $channelContext, private ThemeRepositoryInterface $themeRepository)
    {
    }

    public function getTheme(): ?ThemeInterface
    {
        if (false === $this->theme) {
            try {
                /** @var ChannelInterface $channel */
                $channel = $this->channelContext->getChannel();
                $themeName = $channel->getThemeName();
                $this->theme = null === $themeName
                    ? null
                    : $this->themeRepository->findOneByName($themeName)
                ;
            } catch (ChannelNotFoundException|\Exception) {
                return null;
            }
        }

        return $this->theme;
    }

    public function reset(): void
    {
        // false, not null: null is a resolved "this channel has no theme".
        $this->theme = false;
    }
}
