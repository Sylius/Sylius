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

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class MethodsResolverSpec extends ObjectBehavior
{
    /**
     * @param Doctrine\Common\Persistence\ObjectRepository $methodRepository
     * @param Sylius\Bundle\ShippingBundle\Checker\ShippingMethodEligibilityCheckerInterface $eligibilityChecker
     */
    function let($methodRepository, $eligibilityChecker)
    {
        $this->beConstructedWith($methodRepository, $eligibilityChecker);
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
    function it_returns_all_methods_eligible_for_given_subject(
        $methodRepository, $eligibilityChecker, $subject, $method1, $method2, $method3
    )
    {
        $methods = array($method1, $method2, $method3);
        $methodRepository->findBy(array())->shouldBeCalled()->willReturn($methods);

        $eligibilityChecker->isEligible($subject, $method1)->shouldBeCalled()->willReturn(true);
        $eligibilityChecker->isEligible($subject, $method2)->shouldBeCalled()->willReturn(true);
        $eligibilityChecker->isEligible($subject, $method3)->shouldBeCalled()->willReturn(false);

        $this->getSupportedMethods($subject)->shouldReturn(array($method1, $method2));
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface $subject
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $method1
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $method2
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $method3
     */
    function it_filters_the_methods_pool_by_given_criteria(
        $methodRepository, $eligibilityChecker, $subject, $method1, $method2, $method3
    )
    {
        $methods = array($method1, $method3);
        $methodRepository->findBy(array('enabled' => true))->shouldBeCalled()->willReturn($methods);

        $eligibilityChecker->isEligible($subject, $method1)->shouldBeCalled()->willReturn(false);
        $eligibilityChecker->isEligible($subject, $method2)->shouldNotBeCalled();
        $eligibilityChecker->isEligible($subject, $method3)->shouldBeCalled()->willReturn(true);

        $this->getSupportedMethods($subject, array('enabled' => true))->shouldReturn(array($method3));
    }
}
