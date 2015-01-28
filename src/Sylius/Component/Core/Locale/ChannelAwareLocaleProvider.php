<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Locale;

use Sylius\Component\Core\Channel\ChannelContextInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;

/**
 * Locale provider, which returns locales enabled for this channel.
 *
 * @author Kristian Løvstrøm <kristian@loevstroem.dk>
 */
class ChannelAwareLocaleProvider implements LocaleProviderInterface
{
    /**
     * Channel context.
     *
     * @var ChannelContextInterface
     */
    protected $channelContext;

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
    public function getAvailableLocales()
    {
        $currentChannel =  $this->channelContext->getChannel();

        return $currentChannel->getLocales()->filter(function (LocaleInterface $locale) {
            return $locale->isEnabled();
        });
    }
}
