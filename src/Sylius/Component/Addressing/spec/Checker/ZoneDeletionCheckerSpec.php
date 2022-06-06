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

namespace spec\Sylius\Component\Addressing\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AddressingBundle\EventListener\ZoneMemberIntegrityListener;
use Sylius\Component\Addressing\Checker\ZoneDeletionCheckerInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ZoneDeletionCheckerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $zoneMemberRepository): void
    {
        $this->beConstructedWith($zoneMemberRepository);
    }

    function it_implements_zone_deletion_checker_interface(): void
    {
        $this->shouldImplement(ZoneDeletionCheckerInterface::class);
    }

    function it_says_zone_is_not_deletable_if_the_zone_exists_as_a_zone_member(
        RepositoryInterface $zoneMemberRepository,
        ZoneInterface $zone,
        ZoneMemberInterface $zoneMember,
    ): void {
        $zone->getCode()->willReturn('US');

        $zoneMemberRepository->findOneBy(['code' => 'US'])->willReturn($zoneMember);

        $this->isDeletable($zone)->shouldReturn(false);
    }

    function it_says_zone_is_not_deletable_if_the_zone_does_not_exist_as_a_zone_member(
        RepositoryInterface $zoneMemberRepository,
        ZoneInterface $zone,
    ): void {
        $zone->getCode()->willReturn('US');

        $zoneMemberRepository->findOneBy(['code' => 'US'])->willReturn(null);

        $this->isDeletable($zone)->shouldReturn(true);
    }
}
