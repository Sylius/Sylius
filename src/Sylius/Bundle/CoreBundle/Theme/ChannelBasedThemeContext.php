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

namespace Sylius\Bundle\CoreBundle\Theme;

use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelBasedThemeContext implements ThemeContextInterface
{
    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var ThemeRepositoryInterface */
    private $themeRepository;

    /** @var string|null */
    private $defaultThemeName;

    public function __construct(
        ChannelContextInterface $channelContext,
        ThemeRepositoryInterface $themeRepository,
        ?string $defaultThemeName = null
    ) {
        $this->channelContext = $channelContext;
        $this->themeRepository = $themeRepository;
        $this->defaultThemeName = $defaultThemeName;
    }

    public function getTheme(): ?ThemeInterface
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();
            $channelThemeName = $channel->getThemeName();

            if (null !== $channelThemeName) {
                $theme = $this->themeRepository->findOneByName($channelThemeName);

                if (null !== $theme) {
                    return $theme;
                }
            }

            return $this->getDefaultTheme();
        } catch (ChannelNotFoundException $exception) {
            return $this->getDefaultTheme();
        } catch (\Exception $exception) {
            return $this->getDefaultTheme();
        }
    }

    public function getDefaultTheme(): ?ThemeInterface
    {
        if (null !== $this->defaultThemeName) {
            return $this->themeRepository->findOneByName($this->defaultThemeName);
        }

        return null;
    }
}
