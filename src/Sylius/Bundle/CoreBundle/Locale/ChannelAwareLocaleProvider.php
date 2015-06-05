<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Locale;

use Sylius\Bundle\LocaleBundle\Provider\LocaleProvider;
use Sylius\Component\Core\Channel\ChannelContextInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

/**
 * Locale provider, which returns locales enabled for this channel.
 *
 * @author Kristian Løvstrøm <kristian@loevstroem.dk>
 */
class ChannelAwareLocaleProvider extends LocaleProvider
{
    /**
     * Channel context.
     *
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(ChannelContextInterface $channelContext, $defaultLocale)
    {
        $this->channelContext = $channelContext;
        $this->defaultLocale  = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableLocales()
    {
        $currentChannel =  $this->channelContext->getChannel();

        return $currentChannel->getLocales()->filter(function (LocaleInterface $locale) {
            return $locale->isEnabled();
        })->toArray();
    }
}
