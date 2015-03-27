<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\DependencyInjection\Driver;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
class DoctrineODMDriverSpec extends ObjectBehavior
{
    function let(ContainerBuilder $container)
    {
        $this->beConstructedWith($container, 'prefix', 'resource', 'default');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\DoctrineODMDriver');
    }

    function it_should_implement_database_interface()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\DatabaseDriverInterface');
    }

    function it_should_create_definition(ContainerBuilder $container)
    {
        $container->setDefinition(
            'prefix.controller.resource',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();

        $container->setDefinition(
            'prefix.repository.resource',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();

        $container->setAlias(
            'prefix.manager.resource',
            Argument::type('Symfony\Component\DependencyInjection\Alias')
        )->shouldBeCalled();

        $this->beConstructedWith($container, 'prefix', 'resource', 'default');

        $this->load(array(
            'model' => 'Sylius\Bundle\ResourceBundle\SyliusResourceBundle',
            'controller' => 'Sylius\Controller',
            'repository' => 'Sylius\Bundle\ResourceBundle\SyliusResourceBundle',
        ));
    }
}
