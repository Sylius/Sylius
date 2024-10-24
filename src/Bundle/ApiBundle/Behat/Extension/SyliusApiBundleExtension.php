<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Behat\Extension;

use Behat\Behat\Tester\ServiceContainer\TesterExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Sylius\Bundle\ApiBundle\Behat\Tester\ApiScenarioEventDispatchingScenarioTester;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This extension disables javascript session when running api scenarios
 */
final class SyliusApiBundleExtension implements Extension
{
    public function process(ContainerBuilder $container): void
    {
    }

    public function getConfigKey(): string
    {
        return 'sylius_api';
    }

    public function initialize(ExtensionManager $extensionManager): void
    {
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
    }

    public function load(ContainerBuilder $container, array $config): void
    {
        $definition = new Definition(ApiScenarioEventDispatchingScenarioTester::class, [
            new Reference(TesterExtension::EXAMPLE_TESTER_ID),
        ]);
        $definition->addTag(TesterExtension::SCENARIO_TESTER_WRAPPER_TAG, ['priority' => -100000]);

        $container->setDefinition(ApiScenarioEventDispatchingScenarioTester::class, $definition);
    }
}
