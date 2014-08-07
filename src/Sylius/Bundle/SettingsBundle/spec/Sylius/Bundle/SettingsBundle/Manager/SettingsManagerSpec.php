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
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SettingsBundle\Schema\SchemaRegistryInterface;
use Sylius\Component\Resource\Manager\DomainManagerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SettingsManagerSpec extends ObjectBehavior
{
    function let(
        SchemaRegistryInterface $registry,
        DomainManagerInterface $parameterManager,
        RepositoryInterface $repository,
        Cache $cache,
        ValidatorInterface $validator
    )
    {
        $this->beConstructedWith($registry, $parameterManager, $repository, $cache, $validator);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Manager\SettingsManager');
    }

    function it_should_be_a_Sylius_settings_manager()
    {
        $this->shouldImplement('Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface');
    }
}
