<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Modifies resource services after initialization.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ServicesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $imagineBlock = $container->getDefinition('sylius.form.type.imagine_block');
        $imagineBlock->addArgument(new Reference('liip_imagine.filter.configuration'));

        $imagineBlock = $container->getDefinition('sylius.form.type.menu');
        $imagineBlock->addArgument(new Parameter('cmf_menu.persistence.phpcr.menu_basepath'));

    }
}
