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

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class CookieStorage implements StorageInterface
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function hasData($key)
    {
        return $this->requestStack->getMasterRequest()->cookies->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key, $default = null)
    {
        return $this->requestStack->getMasterRequest()->cookies->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function setData($key, $value)
    {
        $this->requestStack->getMasterRequest()->cookies->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function removeData($key)
    {
        $this->requestStack->getMasterRequest()->cookies->remove($key);
    }
}
