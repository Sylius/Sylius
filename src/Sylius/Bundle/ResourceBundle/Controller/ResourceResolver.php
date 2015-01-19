<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use Sylius\Component\Resource\Factory\ResourceFactoryInterface;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * Resource resolver.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ResourceResolver
{
    /**
     * @var ResourceRepositoryInterface
     */
    private $repository;

    /**
     * @var ResourceFactoryInterface
     */
    private $factory;

    /**
     * @param ResourceRepositoryInterface $repository
     * @param ResourceFactoryInterface $factory
     */
    public function __construct(ResourceRepositoryInterface $repository, ResourceFactoryInterface $factory)
    {
        $this->repository = $repository;
        $this->factory = $factory;
    }

    /**
     * Get resources via repository based on the configuration.
     *
     * @param RequestConfiguration $configuration
     * @param string               $defaultMethod
     * @param array                $defaultArguments
     *
     * @return mixed
     */
    public function getResource(RequestConfiguration $configuration, $defaultMethod, array $defaultArguments = array())
    {
        $callable = array($this->repository, $configuration->getRepositoryMethod($defaultMethod));
        $arguments = $configuration->getRepositoryArguments($defaultArguments);

        return call_user_func_array($callable, $arguments);
    }

    /**
     * Create resource.
     *
     * @param RequestConfiguration $configuration
     * @param string               $defaultMethod
     * @param array                $defaultArguments
     *
     * @return mixed
     */
    public function createResource(RequestConfiguration $configuration, $defaultMethod = 'createNew', array $defaultArguments = array())
    {
        $callable = array($this->factory, $configuration->getFactoryMethod($defaultMethod));
        $arguments = $configuration->getFactoryArguments($defaultArguments);

        return call_user_func_array($callable, $arguments);
    }
}
