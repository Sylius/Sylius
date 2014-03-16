<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Registry;

/**
 * Service registry interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ServiceRegistryInterface
{
    /**
     * Get all registered services.
     *
     * @return array
     */
    public function all();

    /**
     * Register service.
     *
     * @param string $type
     * @param object $service
     */
    public function register($type, $service);

    /**
     * Unregister service with given type.
     *
     * @param string $type
     */
    public function unregister($type);

    /**
     * @param string $type
     *
     * @return Boolean
     */
    public function has($type);

    /**
     * Get service with given type.
     *
     * @param string $type
     *
     * @return object
     */
    public function get($type);
}
