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
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
class DatabaseDriverFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\DatabaseDriverFactory');
    }

    function it_should_create_a_orm_driver_by_default(ContainerBuilder $container)
    {
        $this::get(SyliusResourceBundle::DRIVER_DOCTRINE_ORM, $container, 'prefix', 'resource')
            ->shouldhaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\DoctrineORMDriver');
    }

    function it_should_create_a_orm_driver(ContainerBuilder $container)
    {
        $this::get(SyliusResourceBundle::DRIVER_DOCTRINE_ORM, $container, 'prefix', 'resource')
            ->shouldhaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\DoctrineORMDriver');
    }

    function it_should_create_a_odm_driver(ContainerBuilder $container)
    {
        $this::get(SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM, $container, 'prefix', 'resource')
            ->shouldhaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\DoctrineODMDriver');
    }

    function it_should_create_a_phpcr_driver(ContainerBuilder $container)
    {
        $this::get(SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM, $container, 'prefix', 'resource')
            ->shouldhaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\DoctrinePHPCRDriver');
    }
}
