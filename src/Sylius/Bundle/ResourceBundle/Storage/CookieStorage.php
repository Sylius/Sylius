<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Storage;

use Sylius\Component\Resource\Storage\StorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

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
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 1024]],
            KernelEvents::RESPONSE => [['onKernelResponse', -1024]],
        ];
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->requestCookies = new ParameterBag($event->getRequest()->cookies->all());
        $this->responseCookies = new ParameterBag();
    }

    public function onKernelResponse(FilterResponseEvent $event): void
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
    public function has(string $name): bool
    {
        return !in_array($this->get($name), ['', null], true);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, $default = null)
    {
        return $this->responseCookies->get($name, $this->requestCookies->get($name, $default));
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, $value): void
    {
        $this->responseCookies->set($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $name): void
    {
        $this->set($name, null);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return array_merge($this->responseCookies->all(), $this->requestCookies->all());
    }
}
