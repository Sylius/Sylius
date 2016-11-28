<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Provider;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Provider\AvailableLocalesProviderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelBasedLocaleProvider implements AvailableLocalesProviderInterface
{
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var string
     */
    private $defaultLocaleCode;

    /**
     * @param ChannelContextInterface $channelContext
     * @param string $defaultLocaleCode
     */
    public function __construct(ChannelContextInterface $channelContext, $defaultLocaleCode)
    {
        $this->channelContext = $channelContext;
        $this->defaultLocaleCode = $defaultLocaleCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableLocalesCodes()
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            return $channel
                ->getLocales()
                ->filter(function (LocaleInterface $locale) {
                    return $locale->isEnabled();
                })
                ->map(function (LocaleInterface $locale) {
                    return $locale->getCode();
                })
                ->toArray()
            ;
        } catch (ChannelNotFoundException $exception) {
            return [$this->defaultLocaleCode];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinedLocalesCodes()
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            return $channel
                ->getLocales()
                ->map(function (LocaleInterface $locale) {
                    return $locale->getCode();
                })
                ->toArray()
                ;
        } catch (ChannelNotFoundException $exception) {
            return [$this->defaultLocaleCode];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocaleCode()
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            return $channel->getDefaultLocale()->getCode();
        } catch (ChannelNotFoundException $exception) {
            return $this->defaultLocaleCode;
        }
    }
}
