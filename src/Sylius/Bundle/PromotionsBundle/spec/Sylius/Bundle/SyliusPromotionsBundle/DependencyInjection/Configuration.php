<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionsBundle\DependencyInjection;

use PHPSpec2\ObjectBehavior;

/**
 * DI configuration spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Configuration extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\DependencyInjection\Configuration');
    }

    function it_should_be_dependency_injection_configuration()
    {
        $this->shouldImplement('Symfony\Component\Config\Definition\ConfigurationInterface');
    }
}
