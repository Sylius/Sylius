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
 * Cannot be final, because it is proxied
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ServiceRegistry implements ServiceRegistryInterface
{
    /**
     * @var array
     */
    private $services = [];

    /**
     * Interface which is required by all services.
     *
     * @var string
     */
    private $interface;

    /**
     * Human readable context for these services, e.g. "grid field"
     *
     * @var string
     */
    private $context;

    /**
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
    public function register($identifier, $service)
    {
        if ($this->has($identifier)) {
            throw new ExistingServiceException($this->context, $identifier);
        }

        if (!is_object($service)) {
            throw new \InvalidArgumentException(sprintf('%s needs to be an object, %s given.', ucfirst($this->context), gettype($service)));
        }

        if (!in_array($this->interface, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', ucfirst($this->context), $this->interface, get_class($service))
            );
        }

        $this->services[$identifier] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function unregister($identifier)
    {
        if (!$this->has($identifier)) {
            throw new NonExistingServiceException($this->context, $identifier, array_keys($this->services));
        }

        unset($this->services[$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function has($identifier)
    {
        return isset($this->services[$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($identifier)
    {
        if (!$this->has($identifier)) {
            throw new NonExistingServiceException($this->context, $identifier, array_keys($this->services));
        }

        return $this->services[$identifier];
    }
}
