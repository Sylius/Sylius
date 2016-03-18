<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class GeographicalContextSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $countryFactory,
        RepositoryInterface $countryRepository,
        CountryNameConverterInterface $nameToCodeConverter,
        SharedStorageInterface $sharedStorage
    ) {
        $this->beConstructedWith($countryFactory, $countryRepository, $nameToCodeConverter, $sharedStorage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\GeographicalContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_configures_shipping_destination_countries(
        FactoryInterface $countryFactory,
        RepositoryInterface $countryRepository,
        CountryInterface $australia,
        CountryInterface $china,
        CountryInterface $france,
        CountryNameConverterInterface $nameToCodeConverter
    ) {
        $countryFactory->createNew()->willReturn($australia, $china, $france);

        $australia->setCode('AU')->shouldBeCalled();
        $china->setCode('CN')->shouldBeCalled();
        $france->setCode('FR')->shouldBeCalled();

        $countryRepository->add($australia)->shouldBeCalled();
        $countryRepository->add($china)->shouldBeCalled();
        $countryRepository->add($france)->shouldBeCalled();

        $nameToCodeConverter->convertToCode('Australia')->willReturn('AU');
        $nameToCodeConverter->convertToCode('China')->willReturn('CN');
        $nameToCodeConverter->convertToCode('France')->willReturn('FR');

        $this->storeShipsTo('Australia', 'China', 'France');
    }

    function it_throws_invalid_argument_exception_when_cannot_convert_name_to_code(
        CountryInterface $country,
        FactoryInterface $countryFactory,
        CountryNameConverterInterface $nameToCodeConverter
    ) {
        $countryFactory->createNew()->willReturn($country);
        $nameToCodeConverter->convertToCode('France')->willThrow(\InvalidArgumentException::class);

        $this->shouldThrow(\InvalidArgumentException::class)->during('storeShipsTo', ['France']);
        $this->shouldThrow(\InvalidArgumentException::class)->during('theStoreOperatesIn', ['France']);
        $this->shouldThrow(\InvalidArgumentException::class)->during('theStoreHasDisabledCountry', ['France']);
    }

    function it_configures_that_store_operates_in_given_country(
        CountryInterface $country,
        CountryNameConverterInterface $nameToCodeConverter,
        FactoryInterface $countryFactory,
        RepositoryInterface $countryRepository,
        SharedStorageInterface $sharedStorage
    ) {
        $countryFactory->createNew()->willReturn($country);
        $nameToCodeConverter->convertToCode('France')->willReturn('FR');

        $country->setCode('FR')->shouldBeCalled();
        $sharedStorage->set('country', $country)->shouldBeCalled();
        $countryRepository->add($country)->shouldBeCalled();

        $this->theStoreOperatesIn('France');
    }

    function it_configures_that_store_operates_in_given_country_disabled_by_default(
        CountryInterface $country,
        CountryNameConverterInterface $nameToCodeConverter,
        FactoryInterface $countryFactory,
        RepositoryInterface $countryRepository,
        SharedStorageInterface $sharedStorage
    ) {
        $countryFactory->createNew()->willReturn($country);
        $nameToCodeConverter->convertToCode('France')->willReturn('FR');

        $country->setCode('FR')->shouldBeCalled();
        $country->disable()->shouldBeCalled();
        $sharedStorage->set('country', $country)->shouldBeCalled();
        $countryRepository->add($country)->shouldBeCalled();

        $this->theStoreHasDisabledCountry('France');
    }
}
