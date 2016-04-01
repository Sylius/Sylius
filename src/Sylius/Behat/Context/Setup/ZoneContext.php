<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Bundle\AddressingBundle\Factory\ZoneFactoryInterface;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;
use Symfony\Component\Intl\Intl;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ZoneContext implements Context
{
    /**
     * @var array
     */
    private $euMembers = [
        'BE', 'BG', 'CZ', 'DK', 'DE', 'EE', 'IE', 'GR', 'ES',
        'FR', 'IT', 'CY', 'LV', 'LT', 'LU', 'HU', 'MT', 'NL',
        'AT', 'PL', 'PT', 'RO', 'SI', 'SK', 'FI', 'SE', 'GB',
    ];

    /**
     * @var ZoneRepositoryInterface
     */
    private $zoneRepository;

    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * @var ZoneFactoryInterface
     */
    private $zoneFactory;

    /**
     * @param ZoneRepositoryInterface $zoneRepository
     * @param SettingsManagerInterface $settingsManager
     * @param ZoneFactoryInterface $zoneFactory
     */
    public function __construct(
        ZoneRepositoryInterface $zoneRepository,
        SettingsManagerInterface $settingsManager,
        ZoneFactoryInterface $zoneFactory
    ) {
        $this->zoneRepository = $zoneRepository;
        $this->settingsManager = $settingsManager;
        $this->zoneFactory = $zoneFactory;
    }

    /**
     * @Given /^there is a zone "EU" containing all members of the European Union$/
     */
    public function thereIsAZoneEUContainingAllMembersOfEuropeanUnion()
    {
        $zone = $this->zoneFactory->createWithMembers($this->euMembers);
        $zone->setType(ZoneInterface::TYPE_COUNTRY);
        $zone->setCode('EU');
        $zone->setName('European Union');

        $this->zoneRepository->add($zone);
    }

    /**
     * @Given /^there is a zone "The Rest of the World" containing all other countries$/
     */
    public function thereIsAZoneTheRestOfTheWorldContainingAllOtherCountries()
    {
        $restOfWorldCountries = array_diff(
            array_keys(Intl::getRegionBundle()->getCountryNames('en')),
            array_merge($this->euMembers, ['US'])
        );

        $zone = $this->zoneFactory->createWithMembers($restOfWorldCountries);
        $zone->setType(ZoneInterface::TYPE_COUNTRY);
        $zone->setCode('RoW');
        $zone->setName('The Rest of the World');

        $this->zoneRepository->add($zone);
    }

    /**
     * @Given default tax zone is :zone
     */
    public function defaultTaxZoneIs(ZoneInterface $zone)
    {
        $settings = $this->settingsManager->load('sylius_taxation');
        $settings->set('default_tax_zone', $zone);
        $this->settingsManager->save($settings);
    }

    /**
     * @Given the store does not have any zones defined
     */
    public function theStoreDoesNotHaveAnyZonesDefined()
    {
        $zones = $this->zoneRepository->findAll();

        foreach ($zones as $zone) {
            $this->zoneRepository->remove($zone);
        }
    }
}
