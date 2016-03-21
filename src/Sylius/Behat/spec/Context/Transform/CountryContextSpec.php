<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CountryContextSpec extends ObjectBehavior
{
    function let(
        CountryNameConverterInterface $countryNameConverter,
        RepositoryInterface $countryRepository
    ) {
        $this->beConstructedWith($countryNameConverter, $countryRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Transform\CountryContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }
    function it_gets_country_based_on_given_country_name(
        CountryNameConverterInterface $countryNameConverter,
        RepositoryInterface $countryRepository,
        CountryInterface $country
    ) {
        $countryNameConverter->convertToCode('France')->willReturn('FR');
        $countryRepository->findOneBy(['code' => 'FR'])->willReturn($country);
        $country->getCode()->willReturn('FR');

        $this->getCountryByName('France')->shouldReturn($country);
    }

    function it_throws_invalid_argument_exception_when_country_is_missing(
        CountryNameConverterInterface $countryNameConverter,
        RepositoryInterface $countryRepository
    ) {
        $countryNameConverter->convertToCode('France')->willReturn('FR');
        $countryRepository->findOneBy(['code' => 'FR'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('getCountryByName', ['France']);
    }

    function it_throws_invalid_argument_exception_when_cannot_convert_name_to_code(CountryNameConverterInterface $countryNameConverter)
    {
        $countryNameConverter->convertToCode('France')->willThrow(\InvalidArgumentException::class);

        $this->shouldThrow(\InvalidArgumentException::class)->during('getCountryByName', ['France']);
    }
}
