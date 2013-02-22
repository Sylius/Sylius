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
 * Promotions extension spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class SyliusPromotionsExtension extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\DependencyInjection\SyliusPromotionsExtension');
    }

    function it_should_be_dependency_injection_extension()
    {
        $this->shouldHaveType('Symfony\Component\HttpKernel\DependencyInjection\Extension');
    }
}
