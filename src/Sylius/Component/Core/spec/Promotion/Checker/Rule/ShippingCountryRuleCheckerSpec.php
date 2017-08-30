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

namespace spec\Sylius\Component\Core\Promotion\Checker\Rule;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class ShippingCountryRuleCheckerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $countryRepository): void
    {
        $this->beConstructedWith($countryRepository);
    }

    function it_is_a_rule_checker(): void
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_recognizes_no_shipping_address_as_not_eligible(OrderInterface $subject): void
    {
        $subject->getShippingAddress()->willReturn(null);

        $this->isEligible($subject, [])->shouldReturn(false);
    }

    function it_recognizes_a_subject_as_not_eligible_if_country_does_not_match(
        OrderInterface $subject,
        AddressInterface $address,
        CountryInterface $country,
        RepositoryInterface $countryRepository
    ): void {
        $country->getCode()->willReturn('IE');
        $address->getCountryCode()->willReturn('IE');
        $subject->getShippingAddress()->willReturn($address);

        $countryRepository->findOneBy(['code' => 'IE'])->willReturn($country);

        $this->isEligible($subject, ['country' => 'NL'])->shouldReturn(false);
    }

    function it_recognizes_a_subject_as_eligible_if_country_match(
        OrderInterface $subject,
        AddressInterface $address,
        CountryInterface $country,
        RepositoryInterface $countryRepository
    ): void {
        $country->getCode()->willReturn('IE');
        $address->getCountryCode()->willReturn('IE');
        $subject->getShippingAddress()->willReturn($address);

        $countryRepository->findOneBy(['code' => 'IE'])->willReturn($country);

        $this->isEligible($subject, ['country' => 'IE'])->shouldReturn(true);
    }
}
