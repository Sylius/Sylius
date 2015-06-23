<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers all columntype types in registry service.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RegisterColumnTypesPass extends AbstractRegisterServicePass
{
    /**
     * {@inheritdoc}
     */
    protected function getRegistryIdentifier()
    {
        return 'sylius.registry.grid_column_type';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTagName()
    {
        return 'sylius.grid_column_type';
    }

    /**
     * {@inheritdoc}
     */
    protected function getIdentifierAttribute()
    {
        return 'type';
    }
}
