<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Addressing\Matcher;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\Scope;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;

final class ZoneMatcherSpec extends ObjectBehavior
{
    function let(ZoneRepositoryInterface $zoneRepository): void
    {
        $this->beConstructedWith($zoneRepository);
    }

    function it_returns_a_matching_zone(
        ZoneRepositoryInterface $zoneRepository,
        AddressInterface $address,
        ZoneInterface $zone,
    ): void {
        $zoneRepository->findOneByAddress($address, null)->willReturn($zone);

        $this->match($address)->shouldReturn($zone);
    }

    function it_returns_all_matching_zones(
        ZoneRepositoryInterface $zoneRepository,
        AddressInterface $address,
        ZoneInterface $zoneOne,
        ZoneInterface $zoneTwo,
        ZoneInterface $zoneThree,
    ): void {
        $zoneRepository->findByAddress($address)->willReturn([$zoneOne]);
        $zoneRepository->findByMembers([$zoneOne])->willReturn([$zoneTwo]);
        $zoneRepository->findByMembers([$zoneTwo])->willReturn([$zoneThree]);
        $zoneRepository->findByMembers([$zoneThree])->willReturn([]);

        $matchedZones = $this->matchAll($address);

        $matchedZones->shouldHaveCount(3);
        $matchedZones->shouldBe([$zoneOne, $zoneTwo, $zoneThree]);
    }

    function it_returns_all_matching_zones_withing_a_matching_scope(
        ZoneRepositoryInterface $zoneRepository,
        AddressInterface $address,
        ZoneInterface $zoneOne,
        ZoneInterface $zoneTwo,
        ZoneInterface $zoneThree,
    ): void {
        $zoneOne->getScope()->willReturn('shipping');
        $zoneTwo->getScope()->willReturn(Scope::ALL);
        $zoneThree->getScope()->willReturn('custom');

        $zoneRepository->findByAddress($address)->willReturn([$zoneOne]);
        $zoneRepository->findByMembers([$zoneOne])->willReturn([$zoneTwo]);
        $zoneRepository->findByMembers([$zoneTwo])->willReturn([$zoneThree]);
        $zoneRepository->findByMembers([$zoneThree])->willReturn([]);

        $matchedZones = $this->matchAll($address, 'shipping');

        $matchedZones->shouldHaveCount(2);
        $matchedZones->shouldBe([$zoneOne, $zoneTwo]);
    }
}
