<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\FixturesBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class FixtureRegistryPass implements CompilerPassInterface
{
    public const FIXTURE_SERVICE_TAG = 'sylius_fixtures.fixture';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('sylius_fixtures.fixture_registry')) {
            return;
        }

        $fixtureRegistry = $container->findDefinition('sylius_fixtures.fixture_registry');

        $taggedServices = $container->findTaggedServiceIds(self::FIXTURE_SERVICE_TAG);
        foreach (array_keys($taggedServices) as $id) {
            $fixtureRegistry->addMethodCall('addFixture', [new Reference($id)]);
        }
    }
}
