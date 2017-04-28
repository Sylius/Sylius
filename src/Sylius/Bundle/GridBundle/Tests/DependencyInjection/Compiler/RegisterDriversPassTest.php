<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\Container;
use Sylius\Bundle\GridBundle\DependencyInjection\Compiler\RegisterDriversPass;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterDriversPassTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $pass;

    public function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->container->register('sylius.registry.grid_driver', '\stdClass');
        $this->pass = new RegisterDriversPass();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage needs to have `alias` attribute
     */
    public function it_should_throw_an_exception_if_the_driver_has_no_alias_attribute()
    {
        $definition = $this->container->register('driver.1', '\stdClass');
        $definition->addTag(
            'sylius.grid_driver'
        );

        $this->pass->process($this->container);
    }

    /**
     * @test
     */
    public function it_should_register_drivers()
    {
        $definition = $this->container->register('driver.1', '\stdClass');
        $definition->addTag(
            'sylius.grid_driver',
            [
                'alias' => 'foobar',
            ]
        );

        $this->pass->process($this->container);

        $definition = $this->container->getDefinition('sylius.registry.grid_driver');
        $calls = $definition->getMethodCalls();
        $this->assertCount(1, $calls);
        $this->assertEquals(
            [
                'register',
                [
                    'foobar',
                    new Reference('driver.1')
                ]
            ],
            $calls[0]
        );
    }

    /**
     * @test
     */
    public function it_should_remove_drivers_with_unavailable_dependencies()
    {
        $definition = $this->container->register('driver.1', '\stdClass');
        $definition->addArgument(new Reference('unavailable.service'));
        $definition->addTag(
            'sylius.grid_driver',
            [
                'alias' => 'foobar',
            ]
        );

        $this->pass->process($this->container);

        $definition = $this->container->getDefinition('sylius.registry.grid_driver');
        $calls = $definition->getMethodCalls();
        $this->assertCount(0, $calls);
    }
}
