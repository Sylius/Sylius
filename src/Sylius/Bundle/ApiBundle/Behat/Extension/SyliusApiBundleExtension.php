<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Behat\Extension;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Behat\Behat\Tester\ServiceContainer\TesterExtension;
use Sylius\Bundle\ApiBundle\Behat\Tester\ApiScenarioEventDispatchingScenarioTester;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

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
            new Reference(TesterExtension::EXAMPLE_TESTER_ID)
        ]);
        $definition->addTag(TesterExtension::SCENARIO_TESTER_WRAPPER_TAG, array('priority' => -100000));

        $container->setDefinition(ApiScenarioEventDispatchingScenarioTester::class, $definition);
    }
}
