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
class SessionStorage extends Storage
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
        return $this->getBag()->get($this->resolveKey($key), $default);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->getBag()->set($this->resolveKey($key), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return $this->getBag()->has($this->resolveKey($key));
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        $this->getBag()->remove($this->resolveKey($key));
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->getBag()->remove($this->domain);
    }

    /**
     * Get session flows bag.
     *
     * @return SessionFlowsBag
     */
    private function getBag()
    {
        return $this->session->getBag(SessionFlowsBag::NAME);
    }

    /**
     * Resolve key for current domain.
     *
     * @param string $key
     *
     * @return string
     */
    private function resolveKey($key)
    {
        return $this->domain.'/'.$key;
    }
}
