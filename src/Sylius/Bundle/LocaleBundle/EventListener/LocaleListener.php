<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\EventListener;

use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Used to set the right locale on the request.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LocaleListener implements EventSubscriberInterface
{
    /**
     * @var LocaleContextInterface
     */
    protected $localeContext;

    /**
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(LocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;
    }

    /**
     * Set the right locale via context.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->hasPreviousSession()) {
            return;
        }

        // TODO At this point the session is not started
        // so without this it will always return the default value
        // This is not an elegant solution, maybe moving it to the storage
        // and having the storage start the session or injecting session in localeContext
//        $session = $event->getRequest()->getSession();
//        if (!$session->isStarted()) {
//            $session->start();
//        }

        $request->setLocale($this->localeContext->getLocale() ?: $this->localeContext->getDefaultLocale());
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 35)),
        );
    }
}
