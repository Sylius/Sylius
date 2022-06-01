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

namespace spec\Sylius\Bundle\AddressingBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AddressingBundle\EventListener\ZoneMemberIntegrityListener;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ZoneMemberIntegrityListenerSpec extends ObjectBehavior
{
    function let(
        SessionInterface $session,
        RepositoryInterface $zoneMemberRepository,
        RepositoryInterface $provinceRepository,
    ): void {
        $this->beConstructedWith($session, $zoneMemberRepository, $provinceRepository);
    }

    function it_does_not_allow_to_remove_zone_if_it_exists_as_a_zone_member(
        SessionInterface $session,
        RepositoryInterface $zoneMemberRepository,
        GenericEvent $event,
        ZoneInterface $zone,
        ZoneMemberInterface $zoneMember,
        FlashBagInterface $flashes,
    ): void {
        $event->getSubject()->willReturn($zone);
        $zone->getCode()->willReturn('MUG');

        $zoneMemberRepository->findOneBy(['code' => 'MUG'])->willReturn($zoneMember);

        $session->getBag('flashes')->willReturn($flashes);

        $flashes
            ->add('error', [
                'message' => 'sylius.resource.delete_error',
                'parameters' => ['%resource%' => 'zone'],
            ])
            ->shouldBeCalled()
        ;

        $event->stopPropagation()->shouldBeCalled();

        $this->protectFromRemovingZone($event);
    }

    function it_does_nothing_if_zone_does_not_exist_as_a_zone_member(
        SessionInterface $session,
        RepositoryInterface $zoneMemberRepository,
        GenericEvent $event,
        ZoneInterface $zone,
    ): void {
        $event->getSubject()->willReturn($zone);
        $zone->getCode()->willReturn('MUG');

        $zoneMemberRepository->findOneBy(['code' => 'MUG'])->willReturn(null);

        $session->getBag('flashes')->shouldNotBeCalled();
        $event->stopPropagation()->shouldNotBeCalled();

        $this->protectFromRemovingZone($event);
    }

    function it_throws_an_error_if_an_event_subject_is_not_a_zone(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('wrongSubject');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('protectFromRemovingZone', [$event])
        ;
    }

    function it_does_not_allow_to_remove_province_if_it_exists_as_a_zone_member(
        SessionInterface $session,
        RepositoryInterface $zoneMemberRepository,
        RepositoryInterface $provinceRepository,
        GenericEvent $event,
        CountryInterface $country,
        ProvinceInterface $firstProvince,
        ProvinceInterface $secondProvince,
        ProvinceInterface $thirdProvince,
        ZoneMemberInterface $zoneMember,
        FlashBagInterface $flashes,
    ): void {
        $event->getSubject()->willReturn($country);

        $firstProvince->getCode()->willReturn('FIRST_PROVINCE');
        $secondProvince->getCode()->willReturn('SECOND_PROVINCE');
        $thirdProvince->getCode()->willReturn('THIRD_PROVINCE');

        $country->getProvinces()->willReturn(new ArrayCollection([$secondProvince->getWrappedObject()]));
        $provinceRepository->findBy(['country' => $country])->willReturn([
            $firstProvince->getWrappedObject(),
            $secondProvince->getWrappedObject(),
            $thirdProvince->getWrappedObject(),
        ]);

        $zoneMemberRepository
            ->findOneBy(['code' => [0 => 'FIRST_PROVINCE', 2 => 'THIRD_PROVINCE']])
            ->willReturn($zoneMember)
        ;

        $session->getBag('flashes')->willReturn($flashes);

        $flashes
            ->add('error', [
                'message' => 'sylius.resource.delete_error',
                'parameters' => ['%resource%' => 'province'],
            ])
            ->shouldBeCalled()
        ;

        $event->stopPropagation()->shouldBeCalled();

        $this->protectFromRemovingProvinceWithinCountry($event);
    }

    function it_does_nothing_if_province_does_not_exist_as_a_zone_member(
        SessionInterface $session,
        RepositoryInterface $zoneMemberRepository,
        RepositoryInterface $provinceRepository,
        GenericEvent $event,
        CountryInterface $country,
        ProvinceInterface $firstProvince,
        ProvinceInterface $secondProvince,
        ProvinceInterface $thirdProvince,
    ): void {
        $event->getSubject()->willReturn($country);

        $firstProvince->getCode()->willReturn('FIRST_PROVINCE');
        $secondProvince->getCode()->willReturn('SECOND_PROVINCE');
        $thirdProvince->getCode()->willReturn('THIRD_PROVINCE');

        $country->getProvinces()->willReturn(new ArrayCollection([$secondProvince->getWrappedObject()]));
        $provinceRepository->findBy(['country' => $country])->willReturn([
            $firstProvince->getWrappedObject(),
            $secondProvince->getWrappedObject(),
            $thirdProvince->getWrappedObject(),
        ]);

        $zoneMemberRepository
            ->findOneBy(['code' => [0 => 'FIRST_PROVINCE', 2 => 'THIRD_PROVINCE']])
            ->willReturn(null)
        ;

        $session->getBag('flashes')->shouldNotBeCalled();
        $event->stopPropagation()->shouldNotBeCalled();

        $this->protectFromRemovingProvinceWithinCountry($event);
    }

    function it_throws_an_error_if_an_event_subject_is_not_a_province(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('wrongSubject');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('protectFromRemovingProvinceWithinCountry', [$event])
        ;
    }
}
