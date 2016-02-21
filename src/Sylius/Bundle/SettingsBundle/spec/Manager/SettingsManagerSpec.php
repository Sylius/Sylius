<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SettingsBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;
use Sylius\Bundle\SettingsBundle\Resolver\SettingsResolverInterface;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SchemaRegistryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SettingsManagerSpec extends ObjectBehavior
{
    function let(
        SchemaRegistryInterface $registry,
        ServiceRegistryInterface $resolverRegistry,
        ObjectManager $manager,
        FactoryInterface $factory,
        SettingsResolverInterface $defaultResolver,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith($registry, $resolverRegistry, $manager, $factory, $defaultResolver, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Manager\SettingsManager');
    }

    function it_should_be_a_Sylius_settings_manager()
    {
        $this->shouldImplement(SettingsManagerInterface::class);
    }

    function it_can_load_settings_by_schema_alias(
        $registry,
        $resolverRegistry,
        $defaultResolver,
        SchemaInterface $schema,
        SettingsInterface $settings
    ) {
        $registry->getSchema('theme')
            ->willReturn($schema)
        ;

        $resolverRegistry->has('theme')
            ->shouldBeCalled()
            ->willReturn(false)
        ;

        $defaultResolver->resolve('theme')
            ->shouldBeCalled()
            ->willReturn($settings)
        ;

        $settings->getParameters()
            ->shouldBeCalled()
            ->willReturn([
                'title' => 'Sylius',
                'description' => 'Sylius is awesome',
            ])
        ;

        $settings->setParameters(Argument::any())
            ->shouldBeCalled()
        ;

        $this->load('theme')->shouldHaveType(SettingsInterface::class);
    }
}
