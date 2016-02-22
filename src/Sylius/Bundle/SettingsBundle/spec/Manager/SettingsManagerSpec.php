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
use Sylius\Bundle\SettingsBundle\Resolver\SettingsResolverInterface;
use Sylius\Bundle\SettingsBundle\Schema\SchemaRegistryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SettingsManagerSpec extends ObjectBehavior
{
    function let(
        SchemaRegistryInterface $registry,
        ServiceRegistryInterface $resolverRegistry,
        ObjectManager $settingsManager,
        FactoryInterface $settingsfactory,
        FactoryInterface $parameterFactory,
        SettingsResolverInterface $defaultResolver,
        ValidatorInterface $validator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith(
            $registry,
            $resolverRegistry,
            $settingsManager,
            $settingsfactory,
            $parameterFactory,
            $defaultResolver,
            $validator,
            $eventDispatcher
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Manager\SettingsManager');
    }

    function it_should_be_a_Sylius_settings_manager()
    {
        $this->shouldImplement(SettingsManagerInterface::class);
    }
}
