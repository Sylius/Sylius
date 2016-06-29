<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CoreBundle\Locale;

use Sylius\Channel\Context\ChannelContextInterface;
use Sylius\Locale\Model\LocaleInterface;
use Sylius\Locale\Provider\LocaleProviderInterface;

/**
 * Locale provider, which returns locales enabled for this channel.
 *
 * @author Kristian Løvstrøm <kristian@loevstroem.dk>
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ChannelAwareLocaleProvider implements LocaleProviderInterface
{
    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @var string[]|null
     */
    protected $localesCodes = null;

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
        if (null === $this->localesCodes) {
            $this->localesCodes = $this->getEnabledLocalesCodes();
        }

        return $this->localesCodes;
    }

    /**
     * {@inheritdoc}
     */
    public function isLocaleAvailable($locale)
    {
        return in_array($locale, $this->getAvailableLocales());
    }

    /**
     * @return string[]
     */
    protected function getEnabledLocalesCodes()
    {
        $localesCodes = [];

        /** @var LocaleInterface[] $locales */
        $locales = $this->channelContext->getChannel()->getLocales();
        foreach ($locales as $locale) {
            if (!$locale->isEnabled()) {
                continue;
            }

            $localesCodes[] = $locale->getCode();
        }

        return $localesCodes;
    }
}
