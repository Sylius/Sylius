<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Test\Services;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Test\Services\DefaultCountriesFactoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class DefaultCountriesFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $countryFactory, RepositoryInterface $countryRepository)
    {
        $this->beConstructedWith($countryFactory, $countryRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Test\Services\DefaultCountriesFactory');
    }

    function it_implements_default_countries_factory_interface()
    {
        $this->shouldImplement(DefaultCountriesFactoryInterface::class);
    }

    function it_creates_default_countries(
        $countryFactory,
        $countryRepository,
        CountryInterface $australia,
        CountryInterface $china,
        CountryInterface $france,
        CountryInterface $unitedKingdom,
        CountryInterface $unitedStates
    ) {
        $countryFactory->createNew()->willReturn($australia, $china, $france, $unitedKingdom, $unitedStates);

        $australia->setCode('au')->shouldBeCalled();
        $china->setCode('cn')->shouldBeCalled();
        $france->setCode('fr')->shouldBeCalled();
        $unitedKingdom->setCode('gb')->shouldBeCalled();
        $unitedStates->setCode('us')->shouldBeCalled();

        $countryRepository->add($france)->shouldBeCalled();
        $countryRepository->add($unitedKingdom)->shouldBeCalled();
        $countryRepository->add($unitedStates)->shouldBeCalled();
        $countryRepository->add($china)->shouldBeCalled();
        $countryRepository->add($australia)->shouldBeCalled();

        $this->create(array('au', 'cn', 'fr', 'gb', 'us'));
    }
}
