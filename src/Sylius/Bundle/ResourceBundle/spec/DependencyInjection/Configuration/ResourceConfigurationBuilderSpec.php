<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration\ResourceConfigurationBuilder;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration\ResourceConfigurationBuilderInterface;

/**
 * @mixin ResourceConfigurationBuilder
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ResourceConfigurationBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration\ResourceConfigurationBuilder');
    }

    function it_implements_resource_configuration_builder_interface()
    {
        $this->shouldImplement(ResourceConfigurationBuilderInterface::class);
    }
}
