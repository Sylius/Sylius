<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Shipping\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\ShippableInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

final class ShippingMethodEligibilityCheckerSpec extends ObjectBehavior
{
    function it_implements_Sylius_shipping_method_eligibility_checker_interface(): void
    {
        $this->shouldImplement(ShippingMethodEligibilityCheckerInterface::class);
    }

    function it_approves_category_requirement_if_categories_match(
        ShippingSubjectInterface $subject,
        ShippingMethodInterface $shippingMethod,
        ShippingCategoryInterface $shippingCategory,
        ShippableInterface $shippable
    ): void {
        $shippingMethod->getCategory()->willReturn($shippingCategory);
        $shippingMethod->getCategoryRequirement()->willReturn(ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY);

        $shippable->getShippingCategory()->willReturn($shippingCategory);
        $subject->getShippables()->willReturn(new ArrayCollection([$shippable->getWrappedObject()]));

        $this->isEligible($subject, $shippingMethod)->shouldReturn(true);
    }

    function it_approves_category_requirement_if_no_category_is_required(
        ShippingSubjectInterface $subject,
        ShippingMethodInterface $shippingMethod
    ): void {
        $shippingMethod->getCategory()->willReturn(null);

        $this->isEligible($subject, $shippingMethod)->shouldReturn(true);
    }

    function it_denies_category_requirement_if_categories_do_not_match(
        ShippingSubjectInterface $subject,
        ShippingMethodInterface $shippingMethod,
        ShippingCategoryInterface $shippingCategory,
        ShippingCategoryInterface $shippingCategory2,
        ShippableInterface $shippable
    ): void {
        $shippingMethod->getCategory()->willReturn($shippingCategory);
        $shippingMethod->getCategoryRequirement()->willReturn(ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY);

        $shippable->getShippingCategory()->willReturn($shippingCategory2);
        $subject->getShippables()->willReturn(new ArrayCollection([$shippable->getWrappedObject()]));

        $this->isEligible($subject, $shippingMethod)->shouldReturn(false);
    }
}
