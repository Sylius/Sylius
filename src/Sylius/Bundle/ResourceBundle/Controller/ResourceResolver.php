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

use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;

/**
 * Resource resolver.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ResourceResolver
{
    private $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * Get resources via repository based on the configuration.
     *
     * @param RepositoryInterface $repository
     * @param Configuration       $configuration
     */
    public function getResource(RepositoryInterface $repository, $defaultMethod, array $defaultArguments = array())
    {
        $callable = array($repository, $this->config->getMethod($defaultMethod));
        $arguments = $this->config->getArguments($defaultArguments);

        return call_user_func_array($callable, $arguments);
    }
}
