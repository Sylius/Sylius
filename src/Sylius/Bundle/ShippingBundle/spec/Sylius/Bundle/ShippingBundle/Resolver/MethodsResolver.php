<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Resolver;

use Doctrine\Common\Collections\ArrayCollection;
use PHPSpec2\ObjectBehavior;

/**
 * Shipping methods resolver spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class MethodsResolver extends ObjectBehavior
{
    /**
     * @param Doctrine\Common\Persistence\ObjectRepository $methodRepository
     */
    function let($methodRepository)
    {
        $this->beConstructedWith($methodRepository);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Resolver\MethodsResolver');
    }

    function it_should_implement_Sylius_shipping_methods_resolver_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Resolver\MethodsResolver');
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippablesAwareInterface $shippablesAware
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $method1
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $method2
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $method3
     */
    function it_should_return_all_methods_supporting_given_shippable_aware($methodRepository, $shippablesAware, $method1, $method2, $method3)
    {
        $methods = array($method1, $method2, $method3);
        $methodRepository->findBy(array('enabled' => true))->shouldBeCalled()->willReturn($methods);

        $method1->supports($shippablesAware)->shouldBeCalled()->willReturn(true);
        $method2->supports($shippablesAware)->shouldBeCalled()->willReturn(true);
        $method3->supports($shippablesAware)->shouldBeCalled()->willReturn(false);

        $this->getSupportedMethods($shippablesAware)->shouldReturn(array($method1, $method2));
    }
}
