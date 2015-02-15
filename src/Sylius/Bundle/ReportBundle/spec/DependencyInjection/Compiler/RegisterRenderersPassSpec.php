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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use PhpSpec\ObjectBehavior;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RegisterRenderersPassSpec extends ObjectBehavior
{
    function it_should_implement_compiler_pass_interface()
    {
        $this->shouldImplement('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    function it_processes_with_given_container(ContainerBuilder $container, Definition $rendererDefinition)
    {
        $container->hasDefinition('sylius.registry.report.renderer')->willReturn(true);
        $container->getDefinition('sylius.registry.report.renderer')->willReturn($rendererDefinition);

        $rendererServices = array(
            'sylius.form.type.renderer.test' => array(
                array('renderer' => 'test', 'label' => 'Test renderer'),
            ),
        );
        $container->findTaggedServiceIds('sylius.report.renderer')->willReturn($rendererServices);

        $rendererDefinition->addMethodCall('register', array('test', new Reference('sylius.form.type.renderer.test')))->shouldBeCalled();
        $container->setParameter('sylius.report.renderers', array('test' => 'Test renderer'))->shouldBeCalled();

        $this->process($container);
    }

    function it_does_not_process_if_container_has_not_proper_definition(ContainerBuilder $container)
    {
        $container->hasDefinition('sylius.registry.report.renderer')->willReturn(false);
        $container->getDefinition('sylius.registry.report.renderer')->shouldNotBeCalled();
    }

    function it_throws_exception_if_any_renderer_has_improper_attributes(ContainerBuilder $container, Definition $rendererDefinition)
    {
        $container->hasDefinition('sylius.registry.report.renderer')->willReturn(true);
        $container->getDefinition('sylius.registry.report.renderer')->willReturn($rendererDefinition);

        $rendererServices = array(
            'sylius.form.type.renderer.test' => array(
                array('renderer' => 'test'),
            ),
        );
        $container->findTaggedServiceIds('sylius.report.renderer')->willReturn($rendererServices);
        $this->shouldThrow(new \InvalidArgumentException('Tagged renderers needs to have `renderer` and `label` attributes.'));
        $rendererDefinition->addMethodCall('register', array('test', new Reference('sylius.form.type.renderer.test')))->shouldNotBeCalled();
    }
}
