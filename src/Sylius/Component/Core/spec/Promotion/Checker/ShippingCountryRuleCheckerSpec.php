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
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShippingCountryRuleCheckerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $countryRepository)
    {
        $this->beConstructedWith($countryRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Checker\ShippingCountryRuleChecker');
    }

    function it_should_be_Sylius_rule_checker()
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_should_recognize_no_shipping_address_as_not_eligible(OrderInterface $subject)
    {
        $subject->getShippingAddress()->willReturn(null);

        $this->isEligible($subject, [])->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_country_does_not_match(
        OrderInterface $subject,
        AddressInterface $address,
        CountryInterface $country,
        RepositoryInterface $countryRepository
    ) {
        $country->getCode()->willReturn('IE');
        $address->getCountryCode()->willReturn('IE');
        $subject->getShippingAddress()->willReturn($address);

        $countryRepository->findOneBy(['code' => 'IE'])->willReturn($country);
        $country->getId()->willReturn(2);

        $this->isEligible($subject, ['country' => 1])->shouldReturn(false);
    }

    function it_should_recognize_subject_as_eligible_if_country_match(
        OrderInterface $subject,
        AddressInterface $address,
        CountryInterface $country,
        RepositoryInterface $countryRepository
    ) {
        $country->getCode()->willReturn('IE');
        $address->getCountryCode()->willReturn('IE');
        $subject->getShippingAddress()->willReturn($address);

        $country->getId()->willReturn(1);
        $countryRepository->findOneBy(['code' => 'IE'])->willReturn($country);

        $this->isEligible($subject, ['country' => 1])->shouldReturn(true);
    }
}
