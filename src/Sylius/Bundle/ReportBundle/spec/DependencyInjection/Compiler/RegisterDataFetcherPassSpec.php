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
class RegisterDataFetcherPassSpec extends ObjectBehavior
{
    function it_should_implement_compiler_pass_interface()
    {
        $this->shouldImplement('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    function it_processes_with_given_container(ContainerBuilder $container, Definition $dataFetcherDefinition)
    {
        $container->hasDefinition('sylius.registry.report.data_fetcher')->willReturn(true);
        $container->getDefinition('sylius.registry.report.data_fetcher')->willReturn($dataFetcherDefinition);

        $dataFetcherServices = array(
            'sylius.form.type.data_fetcher.test' => array(
                array('fetcher' => 'test', 'label' => 'Test data fetcher'),
            ),
        );
        $container->findTaggedServiceIds('sylius.report.data_fetcher')->willReturn($dataFetcherServices);

        $dataFetcherDefinition->addMethodCall('register', array('test', new Reference('sylius.form.type.data_fetcher.test')))->shouldBeCalled();
        $container->setParameter('sylius.report.data_fetchers', array('test' => 'Test data fetcher'))->shouldBeCalled();

        $this->process($container);
    }

    function it_does_not_process_if_container_has_not_proper_definition(ContainerBuilder $container)
    {
        $container->hasDefinition('sylius.registry.report.data_fetcher')->willReturn(false);
        $container->getDefinition('sylius.registry.report.data_fetcher')->shouldNotBeCalled();
    }

    function it_throws_exception_if_any_data_fetcher_has_improper_attributes(ContainerBuilder $container, Definition $dataFetcherDefinition)
    {
        $container->hasDefinition('sylius.registry.report.data_fetcher')->willReturn(true);
        $container->getDefinition('sylius.registry.report.data_fetcher')->willReturn($dataFetcherDefinition);

        $dataFetcherServices = array(
            'sylius.form.type.data_fetcher.test' => array(
                array('data_fetcher' => 'test'),
            ),
        );
        $container->findTaggedServiceIds('sylius.report.data_fetcher')->willReturn($dataFetcherServices);
        $this->shouldThrow(new \InvalidArgumentException('Tagged report data fetchers needs to have `fetcher` and `label` attributes.'));
        $dataFetcherDefinition->addMethodCall('register', array('test', new Reference('sylius.form.type.data_fetcher.test')))->shouldNotBeCalled();
    }
}
