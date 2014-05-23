<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Compiler pass which resolves interfaces into target document names during
 * compile time of container.
 *
 * @author Ivannis Suárez Jérez <ivannis.suarez@gmail.com>
 */
class ResolveDoctrineTargetDocumentsPassSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('sylius_resource', array());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(
            'Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetDocumentsPass'
        );
    }

    function it_is_a_compiler_pass()
    {
        $this->shouldImplement('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    function it_should_resolve_documents(ContainerBuilder $container, Definition $resolverDefinition)
    {
        $container->getParameter('sylius_resource.driver')
            ->shouldBeCalled()
            ->willReturn('doctrine/mongodb-odm');

        $container->hasDefinition('doctrine_mongodb.odm.listeners.resolve_target_document')
            ->shouldBeCalled()
            ->willReturn(true);

        $container->findDefinition('doctrine_mongodb.odm.listeners.resolve_target_document')
            ->shouldBeCalled()
            ->willReturn($resolverDefinition);

        $this->process($container);
    }
}
