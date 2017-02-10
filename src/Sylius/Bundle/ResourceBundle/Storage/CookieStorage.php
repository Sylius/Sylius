<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Storage;

use Sylius\Component\Resource\Storage\StorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CookieStorage implements StorageInterface, EventSubscriberInterface
{
    /**
     * @var ParameterBag
     */
    private $requestCookies;

    /**
     * @var ParameterBag
     */
    private $responseCookies;

    public function __construct()
    {
        $this->requestCookies = new ParameterBag();
        $this->responseCookies = new ParameterBag();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 1024]],
            KernelEvents::RESPONSE => [['onKernelResponse', -1024]],
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->requestCookies = new ParameterBag($event->getRequest()->cookies->all());
        $this->responseCookies = new ParameterBag();
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $response = $event->getResponse();
        foreach ($this->responseCookies as $name => $value) {
            $response->headers->setCookie(new Cookie($name, $value));
        }

        $this->requestCookies = new ParameterBag();
        $this->responseCookies = new ParameterBag();
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        return !in_array($this->get($name), ['', null], true);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null)
    {
        return $this->responseCookies->get($name, $this->requestCookies->get($name, $default));
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        $this->responseCookies->set($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        $this->set($name, null);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return array_merge($this->responseCookies->all(), $this->requestCookies->all());
    }
}
