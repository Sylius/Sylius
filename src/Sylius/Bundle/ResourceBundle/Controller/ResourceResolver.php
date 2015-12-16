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

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceResolver
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @param RepositoryInterface $repository
     * @param FactoryInterface $factory
     */
    public function __construct(RepositoryInterface $repository, FactoryInterface $factory)
    {
        $this->repository = $repository;
        $this->factory = $factory;
    }

    /**
     * @param RequestConfiguration $configuration
     * @param string $defaultMethod
     * @param array $defaultArguments
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
     * @param RequestConfiguration $configuration
     * @param string $defaultMethod
     * @param array $defaultArguments
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
