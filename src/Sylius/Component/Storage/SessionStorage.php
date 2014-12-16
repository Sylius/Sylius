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

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SessionStorage implements StorageInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function hasData($key)
    {
        return $this->session->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key, $default = null)
    {
        return $this->session->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function setData($key, $value)
    {
        $this->session->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function removeData($key)
    {
        $this->session->remove($key);
    }
}
