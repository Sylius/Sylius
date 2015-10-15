<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Storage;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class CookieStorage implements StorageInterface
{
    /**
     * @var Request
     */
    protected $request;
    protected $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function hasData($key)
    {
        return $this->request->cookies->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key, $default = null)
    {
        return $this->request->cookies->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function setData($key, $value)
    {
        $this->request->cookies->set($key, $value);

        $this->eventDispatcher->addListener('kernel.response', function (FilterResponseEvent $event) use ($key, $value) {
            $event->getResponse()->headers->setCookie(
                new Cookie($key, $value, new \DateTime('+30 days'))
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function removeData($key)
    {
        $this->request->cookies->remove($key);

        $this->eventDispatcher->addListener('kernel.response', function (FilterResponseEvent $event) use ($key) {
            $event->getResponse()->headers->setCookie(
                new Cookie($key, null)
            );
        });
    }
}
