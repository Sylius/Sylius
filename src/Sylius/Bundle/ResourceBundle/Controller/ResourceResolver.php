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
    /**
     * Get resources via repository based on the configuration.
     *
     * @param RepositoryInterface $repository
     * @param Configuration       $configuration
     */
    public function getResource(RepositoryInterface $repository, Configuration $configuration, $defaultMethod, array $defaultArguments = array())
    {
        $callable = array($repository, $configuration->getMethod($defaultMethod));
        $arguments = $configuration->getArguments($defaultArguments);

        return call_user_func_array($callable, $arguments);
    }
}
