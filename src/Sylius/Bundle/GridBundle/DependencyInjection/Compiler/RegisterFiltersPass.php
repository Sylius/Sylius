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
 * Registers all filter types in registry service.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RegisterFiltersPass extends AbstractRegisterServicePass
{
    /**
     * {@inheritdoc}
     */
    protected function getRegistryIdentifier()
    {
        return 'sylius.registry.grid_filter';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTagName()
    {
        return 'sylius.grid_filter';
    }

    /**
     * {@inheritdoc}
     */
    protected function getIdentifierAttribute()
    {
        return 'type';
    }
}
