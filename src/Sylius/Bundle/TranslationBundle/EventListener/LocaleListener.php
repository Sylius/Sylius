<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TranslationBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Inject current locale in the TranslatableListener
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class LocaleListener implements EventSubscriberInterface
{
    /**
     * The translatable listener instance.
     *
     * @var TranslatableListenerInterface
     */
    private $translatableListener;

    /**
     * Constructor
     *
     * @param TranslatableListenerInterface $translatableListener
     */
    public function __construct(TranslatableListenerInterface $translatableListener)
    {
        $this->translatableListener = $translatableListener;
    }

    /**
     * Set request locale
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $locale = $event->getRequest()->getLocale();
        $this->translatableListener->setCurrentLocale($locale);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            // IMPORTANT keep priority 34
            KernelEvents::REQUEST => array(array('onKernelRequest', 34)),
        );
    }
}
