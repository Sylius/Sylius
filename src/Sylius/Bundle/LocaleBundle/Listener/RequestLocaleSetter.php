<?php

namespace Sylius\Bundle\LocaleBundle\Listener;

use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class RequestLocaleSetter
{
    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @param LocaleContextInterface $localeContext
     * @param LocaleProviderInterface $localeProvider
     */
    public function __construct(
        LocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider
    ) {
        $this->localeContext = $localeContext;
        $this->localeProvider = $localeProvider;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws LocaleNotFoundException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $request->setLocale($this->localeContext->getLocaleCode());
        $request->setDefaultLocale($this->localeProvider->getDefaultLocaleCode());
    }
}
