<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RaportBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers all shipping dataFechers in dataFecher registry service.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RegisterDataFetcher implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.register.report.data_fecher')) {
            return;
        }

        $registry = $container->getDefinition('sylius.register.report.data_fecher');

        foreach ($container->findTaggedServiceIds('sylius.report.data_fetcher') as $id => $attributes) {
            if (!isset($attributes[0]['dataFecher']) || !isset($attributes[0]['label'])) {
                throw new \InvalidArgumentException('Tagged report data fechers needs to have `dataFecher` and `label` attributes.');
            }

            $name = $attributes[0]['dataFecher'];
            $dataFechers[$name] = $attributes[0]['label'];

            $registry->addMethodCall('registerdataFecher', array($name, new Reference($id)));
        }

        $container->setParameter('sylius.data_fetchers', $dataFechers);
    }
}
