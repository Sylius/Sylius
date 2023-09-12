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
use Sylius\Bundle\AddressingBundle\Repository\ZoneRepositoryInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;

final class ZoneMatcherSpec extends ObjectBehavior
{
    public function let(ZoneRepositoryInterface $zoneRepository): void
    {
        $this->beConstructedWith($zoneRepository);
    }

    public function it_returns_all_matching_zones(
        ZoneRepositoryInterface $zoneRepository,
        AddressInterface $address,
        ZoneInterface $zoneOne,
        ZoneInterface $zoneTwo,
        ZoneInterface $zoneThree,
    ): void {
        $zoneRepository->findAllByAddress($address)->willReturn([$zoneOne]);
        $zoneRepository->findAllByZones([$zoneOne])->willReturn([$zoneTwo]);
        $zoneRepository->findAllByZones([$zoneTwo])->willReturn([$zoneThree]);
        $zoneRepository->findAllByZones([$zoneThree])->willReturn([]);

        $matchedZones = $this->matchAll($address);

        $matchedZones->shouldHaveCount(3);
        $matchedZones->shouldBe([$zoneOne, $zoneTwo, $zoneThree]);
    }

    public function it_returns_all_matching_zones_withing_a_matching_scope(
        ZoneRepositoryInterface $zoneRepository,
        AddressInterface $address,
        ZoneInterface $zoneOne,
        ZoneInterface $zoneTwo,
        ZoneInterface $zoneThree,
    ): void {
        $zoneOne->getScope()->willReturn('nato');
        $zoneTwo->getScope()->willReturn('all');
        $zoneThree->getScope()->willReturn('all');

        $zoneRepository->findAllByAddress($address)->willReturn([$zoneOne]);
        $zoneRepository->findAllByZones([$zoneOne])->willReturn([$zoneTwo]);
        $zoneRepository->findAllByZones([$zoneTwo])->willReturn([$zoneThree]);
        $zoneRepository->findAllByZones([$zoneThree])->willReturn([]);

        $matchedZones = $this->matchAll($address, 'nato');

        $matchedZones->shouldHaveCount(1);
        $matchedZones->shouldBe([$zoneOne]);
    }
}
