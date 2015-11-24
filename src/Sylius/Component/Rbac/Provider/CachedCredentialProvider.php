<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Provider;

use Doctrine\Common\Cache\Cache;
use Sylius\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\Rbac\Exception\RoleNotFoundException;
use Sylius\Component\Rbac\Exception\PermissionNotFoundException;

/**
 * Cached credential provider.
 *
 * @author Christian Daguerre <christian@daguer.re>
 */
class CachedCredentialProvider implements CredentialProviderInterface
{
    const DEFAULT_TTL = 60;

    /**
     * @var CredentialProviderInterface
     */
    protected $provider;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var int
     */
    protected $ttl;

    /**
     * @param CredentialProviderInterface $provider
     * @param Cache                       $cache
     * @param int                         $ttl
     */
    public function __construct(CredentialProviderInterface $provider, Cache $cache, $ttl = self::DEFAULT_TTL)
    {
        $this->provider = $provider;
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole($code)
    {
        return $this->has('role', $code);
    }

    /**
     * {@inheritdoc}
     */
    public function getRole($code)
    {
        if (!$this->has('role', $code)) {
            throw new RoleNotFoundException($code);
        }

        return $this->get('role', $code);
    }

    /**
     * {@inheritdoc}
     */
    public function hasPermission($code)
    {
        return $this->has('permission', $code);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermission($code)
    {
        if (!$this->has('permission', $code)) {
            throw new PermissionNotFoundException($code);
        }

        return $this->get('permission', $code);
    }

    /**
     * @param string $type
     * @param string $code
     *
     * @return PermissionInterface|RoleInterface
     */
    private function get($type, $code)
    {
        if ($this->cache->contains($this->getCacheKey($type, $code))) {
            $attribute = $this->cache->fetch($this->getCacheKey($type, $code));
        } else {
            try {
                $attribute = $this->provider->{ 'get' . ucfirst($type) }($code);
            } catch (RoleNotFoundException $e) {
                $attribute = null;
            } catch (PermissionNotFoundException $e) {
                $attribute = null;
            }
            $this->cache->save($this->getCacheKey($type, $code), $attribute, $this->ttl);
        }

        return $attribute;
    }

    /**
     * @param string $type
     * @param string $code
     *
     * @return bool
     */
    private function has($type, $code)
    {
        return null !== $this->get($type, $code);
    }

    /**
     * @param string $code
     *
     * @return string
     */
    private function getCacheKey($type, $code)
    {
        return sprintf('%s.%s', substr($type, 0, 1), $code);
    }
}
