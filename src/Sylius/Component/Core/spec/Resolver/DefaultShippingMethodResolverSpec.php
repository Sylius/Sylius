<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Resolver\DefaultShippingMethodResolverInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class DefaultShippingMethodResolverSpec extends ObjectBehavior
{
    function let(ShippingMethodRepositoryInterface $shippingMethodRepository)
    {
        $this->beConstructedWith($shippingMethodRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Resolver\DefaultShippingMethodResolver');
    }

    function it_implements_default_shipping_method_resolver_interface()
    {
        $this->shouldImplement(DefaultShippingMethodResolverInterface::class);
    }

    function it_returns_first_shipping_method_as_default(
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        ShippingMethodRepositoryInterface $shippingMethodRepository
    ) {
        $shippingMethodRepository->findAll()->willReturn([$firstShippingMethod, $secondShippingMethod]);

        $this->getDefaultShippingMethod()->shouldReturn($firstShippingMethod);
    }

    function it_returns_null_if_there_is_not_shipping_methods(
        ShippingMethodRepositoryInterface $shippingMethodRepository
    ) {
        $shippingMethodRepository->findAll()->willReturn([]);

        $this->getDefaultShippingMethod()->shouldReturn(null);
    }
}
