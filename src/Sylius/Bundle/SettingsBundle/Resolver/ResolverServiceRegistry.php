<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Resolver;

use Sylius\Component\Registry\NonExistingServiceException;
use Sylius\Component\Registry\ServiceRegistry;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
/* final */class ResolverServiceRegistry implements ServiceRegistryInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $decoratedRegistry;

    /**
     * @var SettingsResolverInterface
     */
    private $defaultResolver;

    /**
     * @param ServiceRegistryInterface $decoratedRegistry
     * @param SettingsResolverInterface $defaultResolver
     */
    public function __construct(ServiceRegistryInterface $decoratedRegistry, SettingsResolverInterface $defaultResolver)
    {
        $this->decoratedRegistry = $decoratedRegistry;
        $this->defaultResolver = $defaultResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->decoratedRegistry->all();
    }

    /**
     * {@inheritdoc}
     */
    public function register($type, $service)
    {
        $this->decoratedRegistry->register($type, $service);
    }

    /**
     * {@inheritdoc}
     */
    public function unregister($type)
    {
        $this->decoratedRegistry->unregister($type);
    }

    /**
     * {@inheritdoc}
     */
    public function has($type)
    {
        return $this->decoratedRegistry->has($type);
    }

    /**
     * {@inheritdoc}
     */
    public function get($type)
    {
        if (!$this->decoratedRegistry->has($type)) {
            return $this->defaultResolver;
        }

        return $this->decoratedRegistry->get($type);
    }
}
