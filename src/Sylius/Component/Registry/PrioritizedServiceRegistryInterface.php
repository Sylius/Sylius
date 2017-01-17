<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Registry;

use Zend\Stdlib\PriorityQueue;

/**
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
interface PrioritizedServiceRegistryInterface
{
    /**
     * @return PriorityQueue
     */
    public function all();

    /**
     * @param object $service
     * @param int $priority
     *
     * @throws ExistingServiceException
     * @throws \InvalidArgumentException
     */
    public function register($service, $priority = 0);

    /**
     * @param object $service
     *
     * @throws NonExistingServiceException
     */
    public function unregister($service);

    /**
     * @param object $service
     *
     * @return bool
     */
    public function has($service);
}
