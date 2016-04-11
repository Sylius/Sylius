<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReportBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RegisterRenderersPassSpec extends ObjectBehavior
{
    function it_should_implement_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_processes_with_given_container(ContainerBuilder $container, Definition $rendererDefinition)
    {
        $container->hasDefinition('sylius.registry.report.renderer')->willReturn(true);
        $container->getDefinition('sylius.registry.report.renderer')->willReturn($rendererDefinition);

        $rendererServices = [
            'sylius.form.type.renderer.test' => [
                ['renderer' => 'test', 'label' => 'Test renderer'],
            ],
        ];
        $container->findTaggedServiceIds('sylius.report.renderer')->willReturn($rendererServices);

        $rendererDefinition->addMethodCall('register', ['test', new Reference('sylius.form.type.renderer.test')])->shouldBeCalled();
        $container->setParameter('sylius.report.renderers', ['test' => 'Test renderer'])->shouldBeCalled();

        $this->process($container);
    }

    function it_does_not_process_if_container_has_not_proper_definition(ContainerBuilder $container)
    {
        $container->hasDefinition('sylius.registry.report.renderer')->willReturn(false);
        $container->getDefinition('sylius.registry.report.renderer')->shouldNotBeCalled();

        $this->process($container);
    }

    function it_throws_exception_if_any_renderer_has_improper_attributes(ContainerBuilder $container, Definition $rendererDefinition)
    {
        $container->hasDefinition('sylius.registry.report.renderer')->willReturn(true);
        $container->getDefinition('sylius.registry.report.renderer')->willReturn($rendererDefinition);

        $rendererServices = [
            'sylius.form.type.renderer.test' => [
                ['renderer' => 'test'],
            ],
        ];
        $container->findTaggedServiceIds('sylius.report.renderer')->willReturn($rendererServices);
        $rendererDefinition->addMethodCall('register', ['test', new Reference('sylius.form.type.renderer.test')])->shouldNotBeCalled();

        $this->shouldThrow(new \InvalidArgumentException('Tagged renderers needs to have `renderer` and `label` attributes.'))
            ->during('process', [$container]);
    }
}
