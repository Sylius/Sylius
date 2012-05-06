<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Storage;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Session storage.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SessionStorage implements StorageInterface
{
    /**
     * Session.
     *
     * @var SessionInterface
     */
    protected $session;

    /**
     * Constructor.
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return $this->session->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->session->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return $this->session->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        $this->session->remove($key);
    }
}
