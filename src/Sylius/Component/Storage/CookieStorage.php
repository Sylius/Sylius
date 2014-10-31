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

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class CookieStorage implements StorageInterface
{
    /**
     * @var Request
     */
    protected $request;

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
    }

    /**
     * {@inheritdoc}
     */
    public function removeData($key)
    {
        $this->request->cookies->remove($key);
    }
}
