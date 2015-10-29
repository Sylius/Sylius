<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\CoreBundle\Locale\ChannelAwareLocaleProvider;
use Sylius\Bundle\LocaleBundle\EventListener\LocaleSubscriber as BaseLocaleSubscriber;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Used to set the right locale on the request considering the locales of the current channel.
 *
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
final class LocaleSubscriber extends BaseLocaleSubscriber
{
    /**
     * @var ChannelAwareLocaleProvider
     */
    protected $localeProvider;

    /**
     * @param LocaleContextInterface $localeContext
     * @param ChannelAwareLocaleProvider $localeProvider
     */
    public function __construct(LocaleContextInterface $localeContext, ChannelAwareLocaleProvider $localeProvider)
    {
        parent::__construct($localeContext);
        $this->localeProvider = $localeProvider;
    }

    /**
     * Set the right locale via context and current channel.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->hasPreviousSession() or !$event->isMasterRequest()) {
            return;
        }

        $locale = $this->localeContext->getCurrentLocale();

        $request->setLocale($this->localeProvider->isLocaleAvailable($locale) ? $locale : ($this->localeProvider->isLocaleAvailable($request->getLocale()) ? $request->getLocale() : $this->localeProvider->getAvailableLocales()[0]));
    }
}
