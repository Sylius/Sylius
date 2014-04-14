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
 * Service registry.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ServiceRegistry implements ServiceRegistryInterface
{
    /**
     * Services.
     *
     * @var array
     */
    protected $services = array();

    /**
     * Interface which is required by all services.
     *
     * @var string
     */
    protected $interface;

    /**
     * Constructor.
     *
     * @param string $interface
     */
    public function __construct($interface)
    {
        $this->interface = $interface;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->services;
    }

    /**
     * {@inheritdoc}
     */
    public function register($type, $service)
    {
        if ($this->has($type)) {
            throw new ExistingServiceException($type);
        }

        if (!is_object($service)) {
            throw new \InvalidArgumentException(sprintf('Service needs to be an object, %s given.', gettype($service)));
        }

        if (!in_array($this->interface, class_implements($service))) {
            throw new \InvalidArgumentException(sprintf('Service for this registry needs to implement "%s".', $this->interface));
        }

        $this->services[$type] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function unregister($type)
    {
        if (!$this->has($type)) {
            throw new NonExistingServiceException($type);
        }

        unset($this->services[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function has($type)
    {
        return isset($this->services[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($type)
    {
        if (!$this->has($type)) {
            throw new NonExistingServiceException($type);
        }

        return $this->services[$type];
    }
}
