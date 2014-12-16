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
use Prezent\Doctrine\Translatable\EventListener\TranslatableListener;

/**
 * Inject current locale in the TranslatableListener
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class LocaleListener implements EventSubscriberInterface
{
    /**
     * @var TranslatableListener
     */
    private $translatableListener;

    /**
     * Constructor
     *
     * @param TranslatableListener $translatableListener
     */
    public function __construct(TranslatableListener $translatableListener)
    {
        $this->translatableListener = $translatableListener;
    }

    /**
     * Set request locale
     *
     * @param GetResponseEvent $event
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $locale = $event->getRequest()->getLocale();
        $this->translatableListener->setCurrentLocale($locale);
    }

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            // IMPORTANT keep priority 34
            KernelEvents::REQUEST => array(array('onKernelRequest', 34)),
        );
    }
}