<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShippingCountryRuleCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Checker\ShippingCountryRuleChecker');
    }

    function it_should_be_Sylius_rule_checker()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Checker\RuleCheckerInterface');
    }

    function it_should_recognize_no_shipping_address_as_not_eligible(OrderInterface $subject)
    {
        $subject->getShippingAddress()->shouldBeCalled()->willReturn(null);

        $this->isEligible($subject, array())->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_country_does_not_match(
        OrderInterface $subject,
        AddressInterface $address,
        CountryInterface $country
    ) {
        $subject->getShippingAddress()->shouldBeCalled()->willReturn($address);
        $address->getCountry()->shouldBeCalled()->willReturn($country);
        $country->getId()->shouldBeCalled()->willReturn(2);

        $this->isEligible($subject, array('country' => 1))->shouldReturn(false);
    }

    function it_should_recognize_subject_as_eligible_if_country_match(
        OrderInterface $subject,
        AddressInterface $address,
        CountryInterface $country
    ) {
        $subject->getShippingAddress()->shouldBeCalled()->willReturn($address);
        $address->getCountry()->shouldBeCalled()->willReturn($country);
        $country->getId()->shouldBeCalled()->willReturn(1);

        $this->isEligible($subject, array('country' => 1))->shouldReturn(true);
    }
}
