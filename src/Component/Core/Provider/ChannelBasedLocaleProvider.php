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

namespace Sylius\Component\Core\Provider;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;

final class ChannelBasedLocaleProvider implements LocaleProviderInterface
{
    public function __construct(private ChannelContextInterface $channelContext, private string $defaultLocaleCode)
    {
    }

    public function getAvailableLocalesCodes(): array
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            return $channel
                ->getLocales()
                ->map(function (LocaleInterface $locale) {
                    return (string) $locale->getCode();
                })
                ->toArray()
            ;
        } catch (ChannelNotFoundException) {
            return [$this->defaultLocaleCode];
        }
    }

    public function getDefaultLocaleCode(): string
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            return $channel->getDefaultLocale()->getCode();
        } catch (ChannelNotFoundException) {
            return $this->defaultLocaleCode;
        }
    }
}
