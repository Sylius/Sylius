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
    protected $services = [];

    /**
     * Interface which is required by all services.
     *
     * @var string
     */
    protected $interface;

    /**
     * Human readable context for these services, e.g. "grid field"
     *
     * @var string
     */
    protected $context;

    /**
     * Constructor.
     *
     * @param string $interface
     * @param string $context
     */
    public function __construct($interface, $context = 'service')
    {
        $this->interface = $interface;
        $this->context = $context;
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
            throw new ExistingServiceException($this->context, $type);
        }

        if (!is_object($service)) {
            throw new \InvalidArgumentException(sprintf('%s needs to be an object, %s given.', $this->context, gettype($service)));
        }

        if (!in_array($this->interface, class_implements($service))) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', $this->context, $this->interface, get_class($service))
            );
        }

        $this->services[$type] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function unregister($type)
    {
        if (!$this->has($type)) {
            throw new NonExistingServiceException($this->context, $type, array_keys($this->services));
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
            throw new NonExistingServiceException($this->context, $type, array_keys($this->services));
        }

        return $this->services[$type];
    }
}
