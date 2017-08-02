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

namespace Sylius\Component\Core\Provider;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class ChannelBasedLocaleProvider implements LocaleProviderInterface
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
