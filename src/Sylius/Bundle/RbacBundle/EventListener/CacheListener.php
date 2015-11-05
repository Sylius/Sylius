<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\EventListener;

use Doctrine\Common\Cache\ClearableCache;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Rbac\Model\RoleInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CacheListener
{
    /**
     * @var ClearableCache
     */
    private $cache;

    /**
     * @param ClearableCache $cache
     */
    public function __construct(ClearableCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->clearCache($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $this->clearCache($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->clearCache($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    protected function clearCache(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof RoleInterface || $args->getEntity() instanceof PermissionInterface) {
            $this->cache->deleteAll();
        }
    }
}
