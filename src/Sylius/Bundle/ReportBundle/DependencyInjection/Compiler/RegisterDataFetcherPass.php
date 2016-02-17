<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers all reports dataFetchers in dataFetcher registry service.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class RegisterDataFetcherPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.report.data_fetcher')) {
            return;
        }

        $registry = $container->getDefinition('sylius.registry.report.data_fetcher');
        $dataFetchers = [];

        foreach ($container->findTaggedServiceIds('sylius.report.data_fetcher') as $id => $attributes) {
            if (!isset($attributes[0]['fetcher']) || !isset($attributes[0]['label'])) {
                throw new \InvalidArgumentException(
                    'Tagged report data fetchers needs to have `fetcher` and `label` attributes.'
                );
            }

            $name = $attributes[0]['fetcher'];
            $dataFetchers[$name] = $attributes[0]['label'];

            $registry->addMethodCall('register', [$name, new Reference($id)]);
        }

        $container->setParameter('sylius.report.data_fetchers', $dataFetchers);
    }
}
