<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ImportExportBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class RegisterReadersPassSpec extends ObjectBehavior
{

    public function it_should_implement_compiler_pass_interface()
    {
        $this->shouldImplement('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    public function it_processes_with_given_container(
        ContainerBuilder $container, Definition $exportReaderDefinition, Definition $importReaderDefinition)
    {
        $container->hasDefinition('sylius.registry.export.reader')->willReturn(true);
        $container->getDefinition('sylius.registry.export.reader')->willReturn($exportReaderDefinition);
        
        $container->hasDefinition('sylius.registry.import.reader')->willReturn(true);
        $container->getDefinition('sylius.registry.import.reader')->willReturn($importReaderDefinition);

        $readerServices = array(
            'sylius.form.type.reader.test' => array(
            array('reader' => 'test', 'label' => 'Test reader'),
            ),
        );

        $container->findTaggedServiceIds('sylius.export.reader')->willReturn($readerServices);
        $container->findTaggedServiceIds('sylius.import.reader')->willReturn($readerServices);

        $exportReaderDefinition->addMethodCall('register', array('test', new Reference('sylius.form.type.reader.test')))->shouldBeCalled();
        $importReaderDefinition->addMethodCall('register', array('test', new Reference('sylius.form.type.reader.test')))->shouldBeCalled();

        $container->setParameter('sylius.export.readers', array('test' => 'Test reader'))->shouldBeCalled();
        $container->setParameter('sylius.import.readers', array('test' => 'Test reader'))->shouldBeCalled();

        $this->process($container);
    }

    public function it_does_not_process_if_container_has_not_proper_export_definition(ContainerBuilder $container)
    {
        $container->hasDefinition('sylius.registry.export.reader')->willReturn(false);
        $container->getDefinition('sylius.registry.export.reader')->shouldNotBeCalled();
    }

    public function it_does_not_process_if_container_has_not_proper_import_definition(ContainerBuilder $container)
    {
        $container->hasDefinition('sylius.registry.import.reader')->willReturn(false);
        $container->getDefinition('sylius.registry.import.reader')->shouldNotBeCalled();
    }

    public function it_throws_exception_if_any_export_reader_has_improper_attributes(ContainerBuilder $container, Definition $exportReaderDefinition)
    {
        $container->hasDefinition('sylius.registry.export.reader')->willReturn(true);
        $container->getDefinition('sylius.registry.export.reader')->willReturn($exportReaderDefinition);

        $readerServices = array(
            'sylius.form.type.reader.test' => array(
            array('reader' => 'test'),
            ),
        );

        $container->findTaggedServiceIds('sylius.reader')->willReturn($readerServices);

        $this->shouldThrow(new \InvalidArgumentException('Tagged readers needs to have `reader` and `label` attributes.'));

        $exportReaderDefinition->addMethodCall('register', array('test', new Reference('sylius.form.type.reader.test')))->shouldNotBeCalled();
    }

    public function it_throws_exception_if_any_import_reader_has_improper_attributes(ContainerBuilder $container, Definition $exportReaderDefinition)
    {
        $container->hasDefinition('sylius.registry.import.reader')->willReturn(true);
        $container->getDefinition('sylius.registry.import.reader')->willReturn($exportReaderDefinition);

        $readerServices = array(
            'sylius.form.type.reader.test' => array(
            array('reader' => 'test'),
            ),
        );

        $container->findTaggedServiceIds('sylius.reader')->willReturn($readerServices);

        $this->shouldThrow(new \InvalidArgumentException('Tagged readers needs to have `reader` and `label` attributes.'));

        $exportReaderDefinition->addMethodCall('register', array('test', new Reference('sylius.form.type.reader.test')))->shouldNotBeCalled();
    }
}