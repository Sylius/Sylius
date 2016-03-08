<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ShippingContextSpec extends ObjectBehavior
{
    function let(ShippingMethodRepositoryInterface $shippingMethodRepository) {
        $this->beConstructedWith($shippingMethodRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Transform\ShippingContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_casts_shipping_method_name_to_string($shippingMethodRepository, ShippingMethodInterface $shippingMethod)
    {
        $shippingMethodRepository->findOneByName('DHL')->willReturn($shippingMethod);

        $this->getShippingMethodByName('DHL')->shouldReturn($shippingMethod);
    }

    function it_throws_exception_if_there_is_no_shipping_method_with_name_passed_to_casting($shippingMethodRepository)
    {
        $shippingMethodRepository->findOneByName('DHL')->willReturn(null);

        $this->shouldThrow(new \Exception('Shipping method with name "DHL" does not exist'))->during('getShippingMethodByName', ['DHL']);
    }
}
