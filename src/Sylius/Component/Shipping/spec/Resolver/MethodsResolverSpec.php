<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Shipping\Resolver;

use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Shipping\Resolver\MethodsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class MethodsResolverSpec extends ObjectBehavior
{
    function let(
        ObjectRepository $methodRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker
    ) {
        $this->beConstructedWith($methodRepository, $eligibilityChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Shipping\Resolver\MethodsResolver');
    }

    function it_implements_Sylius_shipping_methods_resolver_interface()
    {
        $this->shouldImplement(MethodsResolverInterface::class);
    }

    function it_returns_all_methods_eligible_for_given_subject(
        $methodRepository,
        $eligibilityChecker,
        ShippingSubjectInterface $subject,
        ShippingMethodInterface $method1,
        ShippingMethodInterface $method2,
        ShippingMethodInterface $method3
    ) {
        $methods = [$method1, $method2, $method3];
        $methodRepository->findBy([])->shouldBeCalled()->willReturn($methods);

        $eligibilityChecker->isEligible($subject, $method1)->shouldBeCalled()->willReturn(true);
        $eligibilityChecker->isEligible($subject, $method2)->shouldBeCalled()->willReturn(true);
        $eligibilityChecker->isEligible($subject, $method3)->shouldBeCalled()->willReturn(false);

        $this->getSupportedMethods($subject)->shouldReturn([$method1, $method2]);
    }

    function it_filters_the_methods_pool_by_given_criteria(
        $methodRepository,
        $eligibilityChecker,
        ShippingSubjectInterface $subject,
        ShippingMethodInterface $method1,
        ShippingMethodInterface $method2,
        ShippingMethodInterface $method3
    ) {
        $methods = [$method1, $method3];
        $methodRepository->findBy(['enabled' => true])->shouldBeCalled()->willReturn($methods);

        $eligibilityChecker->isEligible($subject, $method1)->shouldBeCalled()->willReturn(false);
        $eligibilityChecker->isEligible($subject, $method2)->shouldNotBeCalled();
        $eligibilityChecker->isEligible($subject, $method3)->shouldBeCalled()->willReturn(true);

        $this->getSupportedMethods($subject, ['enabled' => true])->shouldReturn([$method3]);
    }
}
