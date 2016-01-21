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

use Sylius\Component\Storage\SessionStorage as BaseSessionStorage;

/**
 * Session storage.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class SessionStorage extends BaseSessionStorage implements StorageInterface
{
    /**
     * Storage domain.
     *
     * @var string
     */
    protected $domain;

    /**
     * {@inheritdoc}
     */
    public function initialize($domain)
    {
        $this->domain = $domain;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key, $default = null)
    {
        return $this->getBag()->get($this->resolveKey($key), $default);
    }

    /**
     * {@inheritdoc}
     */
    public function setData($key, $value)
    {
        $this->getBag()->set($this->resolveKey($key), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function hasData($key)
    {
        return $this->getBag()->has($this->resolveKey($key));
    }

    /**
     * {@inheritdoc}
     */
    public function removeData($key)
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
