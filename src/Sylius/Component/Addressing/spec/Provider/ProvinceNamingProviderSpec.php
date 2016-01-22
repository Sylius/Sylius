<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Addressing\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Provider\ProvinceNamingProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ProvinceNamingProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $provinceRepository)
    {
        $this->beConstructedWith($provinceRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Addressing\Provider\ProvinceNamingProvider');
    }

    function it_implements_province_name_provider_interface()
    {
        $this->shouldHaveType(ProvinceNamingProviderInterface::class);
    }

    function it_throws_invalid_argument_exception_when_province_with_given_code_is_not_found(
        RepositoryInterface $provinceRepository
    ) {
        $provinceRepository->findOneBy(array('code' => 'ZZ-TOP'))->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('getName', array('provinceCode' => 'ZZ-TOP'));
        $this->shouldThrow(\InvalidArgumentException::class)->during('getAbbreviation', array('provinceCode' => 'ZZ-TOP'));
    }

    function it_gets_province_name_by_its_code(
        RepositoryInterface $provinceRepository,
        ProvinceInterface $province
    ) {
        $province->getCode()->willReturn('IE-UL');
        $province->getName()->willReturn('Ulster');

        $provinceRepository->findOneBy(array('code' => 'IE-UL'))->willReturn($province);

        $this->getName('IE-UL')->shouldReturn('Ulster');
    }

    function it_gets_province_abbreviation_by_its_code(
        RepositoryInterface $provinceRepository,
        ProvinceInterface $province
    ) {
        $province->getCode()->willReturn('IE-UL');
        $province->getAbbreviation()->willReturn('ULS');

        $provinceRepository->findOneBy(array('code' => 'IE-UL'))->willReturn($province);

        $this->getAbbreviation('IE-UL')->shouldReturn('ULS');
    }

    function it_gets_province_code_if_its_abbreviation_is_not_set(
        RepositoryInterface $provinceRepository,
        ProvinceInterface $province
    )
    {
        $province->getCode()->willReturn('IE-UL');
        $province->getAbbreviation()->willReturn(null);

        $provinceRepository->findOneBy(array('code' => 'IE-UL'))->willReturn($province);

        $this->getAbbreviation('IE-UL')->shouldReturn('IE-UL');
    }
}
