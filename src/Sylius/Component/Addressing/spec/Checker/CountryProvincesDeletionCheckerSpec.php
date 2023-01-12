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
use Sylius\Component\Addressing\Checker\CountryProvincesDeletionCheckerInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CountryProvincesDeletionCheckerSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $zoneMemberRepository,
        RepositoryInterface $provinceRepository,
    ): void {
        $this->beConstructedWith($zoneMemberRepository, $provinceRepository);
    }

    function it_implements_country_provinces_deletion_checker_interface(): void
    {
        $this->shouldImplement(CountryProvincesDeletionCheckerInterface::class);
    }

    function it_says_provinces_within_a_country_are_not_deletable_if_there_is_a_province_that_exists_as_a_zone_member(
        RepositoryInterface $zoneMemberRepository,
        RepositoryInterface $provinceRepository,
        CountryInterface $country,
        ProvinceInterface $firstProvince,
        ProvinceInterface $secondProvince,
        ProvinceInterface $thirdProvince,
        ZoneMemberInterface $zoneMember,
    ): void {
        $firstProvince->getCode()->willReturn('US-AK');
        $secondProvince->getCode()->willReturn('US-TX');
        $thirdProvince->getCode()->willReturn('US-KY');

        $country->getProvinces()->willReturn(new ArrayCollection([$secondProvince->getWrappedObject()]));
        $provinceRepository->findBy(['country' => $country])->willReturn([
            $firstProvince->getWrappedObject(),
            $secondProvince->getWrappedObject(),
            $thirdProvince->getWrappedObject(),
        ]);

        $zoneMemberRepository
            ->findOneBy(['code' => [0 => 'US-AK', 2 => 'US-KY']])
            ->willReturn($zoneMember)
        ;

        $this->isDeletable($country)->shouldReturn(false);
    }

    function it_says_provinces_within_a_country_are_deletable_if_there_is_not_a_province_that_exists_as_a_zone_member(
        RepositoryInterface $zoneMemberRepository,
        RepositoryInterface $provinceRepository,
        CountryInterface $country,
        ProvinceInterface $firstProvince,
        ProvinceInterface $secondProvince,
        ProvinceInterface $thirdProvince,
    ): void {
        $firstProvince->getCode()->willReturn('US-AK');
        $secondProvince->getCode()->willReturn('US-TX');
        $thirdProvince->getCode()->willReturn('US-KY');

        $country->getProvinces()->willReturn(new ArrayCollection([$secondProvince->getWrappedObject()]));
        $provinceRepository->findBy(['country' => $country])->willReturn([
            $firstProvince->getWrappedObject(),
            $secondProvince->getWrappedObject(),
            $thirdProvince->getWrappedObject(),
        ]);

        $zoneMemberRepository
            ->findOneBy(['code' => [0 => 'US-AK', 2 => 'US-KY']])
            ->willReturn(null)
        ;

        $this->isDeletable($country)->shouldReturn(true);
    }
}
