<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\DependencyInjection\Compiler;

use Sylius\Component\Attribute\Factory\AttributeFactory;
use Sylius\Component\Order\Factory\AdjustmentFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RegisterAdjustmentFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $oldAdjustmentFactory = $container->getDefinition('sylius.factory.adjustment');
        $adjustmentFactoryDefinition = new Definition(AdjustmentFactory::class);

        $adjustmentFactory = $container->setDefinition('sylius.factory.adjustment', $adjustmentFactoryDefinition);
        $adjustmentFactory->addArgument($oldAdjustmentFactory);
    }
}
