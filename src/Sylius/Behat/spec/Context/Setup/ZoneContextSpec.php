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
use Prophecy\Argument;
use Sylius\Bundle\AddressingBundle\Factory\ZoneFactoryInterface;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ZoneContextSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $zoneRepository,
        SettingsManagerInterface $settingsManager,
        ZoneFactoryInterface $zoneFactory
    ) {
        $this->beConstructedWith($zoneRepository, $settingsManager, $zoneFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\ZoneContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_creates_eu_zone_with_european_zone_members($zoneRepository, $zoneFactory, ZoneInterface $zone)
    {
        $zoneFactory->createWithMembers([
            'BE', 'BG', 'CZ', 'DK', 'DE', 'EE', 'IE', 'GR', 'ES',
            'FR', 'IT', 'CY', 'LV', 'LT', 'LU', 'HU', 'MT', 'NL',
            'AT', 'PL', 'PT', 'RO', 'SI', 'SK', 'FI', 'SE', 'GB',
        ])->willReturn($zone);

        $zone->setType(ZoneInterface::TYPE_COUNTRY)->shouldBeCalled();
        $zone->setName('European Union')->shouldBeCalled();
        $zone->setCode('EU')->shouldBeCalled();

        $zoneRepository->add($zone)->shouldBeCalled();

        $this->thereIsEUZoneContainingAllMembersOfEuropeanUnion();
    }

    function it_creates_rest_of_the_world_zone($zoneRepository, $zoneFactory, ZoneInterface $zone)
    {
        $zoneFactory->createWithMembers(Argument::type('array'))->willReturn($zone);

        $zone->setType(ZoneInterface::TYPE_COUNTRY)->shouldBeCalled();
        $zone->setName('Rest of the World')->shouldBeCalled();
        $zone->setCode('RoW')->shouldBeCalled();

        $zoneRepository->add($zone)->shouldBeCalled();

        $this->thereIsRestOfTheWorldZoneContainingAllOtherCountries();
    }

    function it_sets_default_zone($zoneRepository, $settingsManager, Settings $settings, ZoneInterface $zone)
    {
        $zoneRepository->findOneBy(['code' => 'EU'])->willReturn($zone);

        $settingsManager->loadSettings('sylius_taxation')->willReturn($settings);
        $settings->set('default_tax_zone', $zone)->shouldBeCalled();
        $settingsManager->saveSettings('sylius_taxation', $settings)->shouldBeCalled();

        $this->defaultTaxZoneIs('EU');
    }

    function it_throws_exception_if_zone_with_given_code_does_not_exist_while_setting_default_zone($zoneRepository)
    {
        $zoneRepository->findOneBy(['code' => 'EU'])->willReturn(null);

        $this->shouldThrow(new \InvalidArgumentException('Zone with code "EU" does not exist.'))->during('defaultTaxZoneIs', ['EU']);
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
