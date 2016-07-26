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
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelBasedLocaleProvider implements LocaleProviderInterface
{
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(ChannelContextInterface $channelContext)
    {
        $this->channelContext = $channelContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableLocalesCodes()
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            $locales = $channel
                ->getLocales()
                ->filter(function (LocaleInterface $locale) {
                    return $locale->isEnabled();
                })
                ->toArray()
            ;

            return array_map(
                function (LocaleInterface $locale) { return $locale->getCode(); },
                $locales
            );
        } catch (ChannelNotFoundException $exception) {
            throw new LocaleNotFoundException(null, $exception);
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
            throw new LocaleNotFoundException(null, $exception);
        }
    }
}
