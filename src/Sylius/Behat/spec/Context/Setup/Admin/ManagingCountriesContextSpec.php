<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace spec\Sylius\Behat\Context\Setup\Admin;
 
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
class ManagingCountriesContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $countryRepository,
        CountryNameConverterInterface $countryNameConverter,
        FactoryInterface $countryFactory
    ) {
        $this->beConstructedWith(
            $sharedStorage,
            $countryRepository,
            $countryNameConverter,
            $countryFactory
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\Admin\ManagingCountriesContext');
    }

    function it_is_context()
    {
        $this->shouldImplement(Context::class);
    }

    function it_creates_enabled_country(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $countryRepository,
        CountryNameConverterInterface $countryNameConverter,
        FactoryInterface $countryFactory,
        CountryInterface $country
    ) {
        $countryFactory->createNew()->willReturn($country);
        $countryNameConverter->convertToCode('France')->willReturn('FR');
        $country->setCode('FR')->shouldBeCalled();
        $country->enable()->shouldBeCalled();
        $sharedStorage->set('country', $country)->shouldBeCalled();
        $countryRepository->add($country)->shouldBeCalled();

        $this->theStoreHasCountryEnabled('France');
    }

    function it_creates_disabled_country(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $countryRepository,
        CountryNameConverterInterface $countryNameConverter,
        FactoryInterface $countryFactory,
        CountryInterface $country
    ) {
        $countryFactory->createNew()->willReturn($country);
        $countryNameConverter->convertToCode('France')->willReturn('FR');
        $country->setCode('FR')->shouldBeCalled();
        $country->disable()->shouldBeCalled();
        $sharedStorage->set('country', $country)->shouldBeCalled();
        $countryRepository->add($country)->shouldBeCalled();

        $this->theStoreHasCountryDisabled('France');
    }

    function it_throws_invalid_argument_exception_when_cannot_convert_name_to_code(CountryNameConverterInterface $countryNameConverter)
    {
        $countryNameConverter->convertToCode('France')->willThrow(\InvalidArgumentException::class);

        $this->shouldThrow(\InvalidArgumentException::class)->during('theStoreHasCountryEnabled', ['France']);
        $this->shouldThrow(\InvalidArgumentException::class)->during('theStoreHasCountryDisabled', ['France']);
    }
}
