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

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SettingsManagerSpec extends ObjectBehavior
{
    function let(
        ServiceRegistryInterface $registry,
        Cache $cache,
        ObjectManager $manager,
        RepositoryInterface $repository,
        FactoryInterface $factory,
        ValidatorInterface $validator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith($registry, $manager, $repository, $factory, $cache, $validator, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Manager\SettingsManager');
    }

    function it_should_be_a_Sylius_settings_manager()
    {
        $this->shouldImplement(SettingsManagerInterface::class);
    }

    function it_throws_exception_if_settings_do_not_exists_in_database_and_schama_is_not_pass($schemaAlias, $namespace)
    {

    }

    function it_creates_new_schema_if_it_is_not_in_database($schemaAlias, $namespace)
    {

    }

    function it_loads_settings_for_given_schema_and_namespace($schemaAlias, $namespace)
    {

    }

    function it_loads_settings_for_given_namespace($namespace)
    {

    }

}
