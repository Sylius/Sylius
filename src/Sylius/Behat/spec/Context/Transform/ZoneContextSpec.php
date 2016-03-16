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
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ZoneContextSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $zoneRepository
    ) {
        $this->beConstructedWith($zoneRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Transform\ZoneContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_returns_zone_by_its_code($zoneRepository, ZoneInterface $zone)
    {
        $zoneRepository->findOneBy(['code' => 'EU'])->willReturn($zone);

        $this->getZoneByCode('EU')->shouldReturn($zone);
    }

    function it_throws_exception_if_zone_with_given_code_does_not_exist($zoneRepository)
    {
        $zoneRepository->findOneBy(['code' => 'EU'])->willReturn(null);

        $this->shouldThrow(new \Exception('Zone with code "EU" does not exist.'))->during('getZoneByCode', ['EU']);
    }

    function it_returns_the_rest_of_the_world_zone($zoneRepository, ZoneInterface $zone)
    {
        $zoneRepository->findOneBy(['code' => 'RoW'])->willReturn($zone);

        $this->getRestOfTheWorldZone()->shouldReturn($zone);
    }

    function it_throws_exception_if_there_is_no_rest_of_the_world_zone($zoneRepository)
    {
        $zoneRepository->findOneBy(['code' => 'RoW'])->willReturn(null);

        $this->shouldThrow(new \Exception('Rest of the world zone does not exist.'))->during('getRestOfTheWorldZone');
    }
}
