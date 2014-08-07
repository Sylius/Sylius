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

use Sylius\Component\Resource\Manager\DomainManagerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Resource resolver.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ResourceResolver
{
    /**
     * @var Configuration
     */
    private $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * Get resources via repository based on the configuration.
     *
     * @param RepositoryInterface $repository
     * @param string              $defaultMethod
     * @param array               $defaultArguments
     *
     * @return null|object
     */
    public function getResource(RepositoryInterface $repository, $defaultMethod, array $defaultArguments = array())
    {
        return call_user_func_array(array($repository, $this->config->getMethod($defaultMethod)), $this->config->getArguments($defaultArguments));
    }

    /**
     * Create resource.
     *
     * @param DomainManagerInterface $manager
     * @param string                 $defaultMethod
     * @param array                  $defaultArguments
     *
     * @return object
     */
    public function createResource(DomainManagerInterface $manager, $defaultMethod = 'create', array $defaultArguments = array())
    {
        return call_user_func_array(array($manager, $this->config->getFactoryMethod($defaultMethod)), $this->config->getFactoryArguments($defaultArguments));
    }
}
