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
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class MethodsResolverSpec extends ObjectBehavior
{
    /**
     * @param Doctrine\Common\Persistence\ObjectRepository $methodRepository
     * @param Sylius\Bundle\ShippingBundle\Checker\ShippingMethodEliglibilityCheckerInterface $eliglibilityChecker
     */
    function let($methodRepository, $eliglibilityChecker)
    {
        $this->beConstructedWith($methodRepository, $eliglibilityChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Resolver\MethodsResolver');
    }

    function it_implements_Sylius_shipping_methods_resolver_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Resolver\MethodsResolverInterface');
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface $subject
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $method1
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $method2
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $method3
     */
    function it_returns_all_methods_eliglible_for_given_subject(
        $methodRepository, $eliglibilityChecker, $subject, $method1, $method2, $method3
    )
    {
        $methods = array($method1, $method2, $method3);
        $methodRepository->findBy(array())->shouldBeCalled()->willReturn($methods);

        $eliglibilityChecker->isEliglible($subject, $method1)->shouldBeCalled()->willReturn(true);
        $eliglibilityChecker->isEliglible($subject, $method2)->shouldBeCalled()->willReturn(true);
        $eliglibilityChecker->isEliglible($subject, $method3)->shouldBeCalled()->willReturn(false);

        $this->getSupportedMethods($subject)->shouldReturn(array($method1, $method2));
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface $subject
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $method1
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $method2
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $method3
     */
    function it_filters_the_methods_pool_by_given_criteria(
        $methodRepository, $eliglibilityChecker, $subject, $method1, $method2, $method3
    )
    {
        $methods = array($method1, $method3);
        $methodRepository->findBy(array('enabled' => true))->shouldBeCalled()->willReturn($methods);

        $eliglibilityChecker->isEliglible($subject, $method1)->shouldBeCalled()->willReturn(false);
        $eliglibilityChecker->isEliglible($subject, $method2)->shouldNotBeCalled();
        $eliglibilityChecker->isEliglible($subject, $method3)->shouldBeCalled()->willReturn(true);

        $this->getSupportedMethods($subject, array('enabled' => true))->shouldReturn(array($method1, $method3));
    }
}
