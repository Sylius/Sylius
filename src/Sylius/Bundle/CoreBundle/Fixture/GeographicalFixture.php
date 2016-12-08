<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Addressing\Factory\ZoneFactoryInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Intl\Intl;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class GeographicalFixture extends AbstractFixture
{
    /**
     * @var FactoryInterface
     */
    private $countryFactory;

    /**
     * @var ObjectManager
     */
    private $countryManager;

    /**
     * @var FactoryInterface
     */
    private $provinceFactory;

    /**
     * @var ObjectManager
     */
    private $provinceManager;

    /**
     * @var ZoneFactoryInterface
     */
    private $zoneFactory;

    /**
     * @var ObjectManager
     */
    private $zoneManager;

    /**
     * @param FactoryInterface $countryFactory
     * @param ObjectManager $countryManager
     * @param FactoryInterface $provinceFactory
     * @param ObjectManager $provinceManager
     * @param ZoneFactoryInterface $zoneFactory
     * @param ObjectManager $zoneManager
     */
    public function __construct(
        FactoryInterface $countryFactory,
        ObjectManager $countryManager,
        FactoryInterface $provinceFactory,
        ObjectManager $provinceManager,
        ZoneFactoryInterface $zoneFactory,
        ObjectManager $zoneManager
    ) {
        $this->countryFactory = $countryFactory;
        $this->countryManager = $countryManager;
        $this->provinceFactory = $provinceFactory;
        $this->provinceManager = $provinceManager;
        $this->zoneFactory = $zoneFactory;
        $this->zoneManager = $zoneManager;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options)
    {
        $this->loadCountriesWithProvinces($options['countries'], $options['provinces']);
        $this->loadZones($options['zones'], $this->provideZoneValidator($options));

        $this->countryManager->flush();
        $this->provinceManager->flush();
        $this->zoneManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'geographical';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode)
    {
        $optionsNodeBuilder = $optionsNode->children();

        $optionsNodeBuilder
            ->arrayNode('countries')
                ->performNoDeepMerging()
                ->defaultValue(array_keys(Intl::getRegionBundle()->getCountryNames()))
                ->prototype('scalar')
        ;

        /** @var ArrayNodeDefinition $provinceNode */
        $provinceNode = $optionsNodeBuilder
            ->arrayNode('provinces')
                ->normalizeKeys(false)
                ->useAttributeAsKey('code')
                ->prototype('array')
        ;

        $provinceNode
            ->performNoDeepMerging()
            ->normalizeKeys(false)
            ->useAttributeAsKey('code')
            ->prototype('scalar')
        ;

        /** @var ArrayNodeDefinition $zoneNode */
        $zoneNode = $optionsNodeBuilder
            ->arrayNode('zones')
                ->normalizeKeys(false)
                ->useAttributeAsKey('code')
                ->prototype('array')
        ;

        $zoneNode
            ->performNoDeepMerging()
            ->children()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->arrayNode('countries')->prototype('scalar')->end()->end()
                ->arrayNode('zones')->prototype('scalar')->end()->end()
                ->arrayNode('provinces')->prototype('scalar')->end()->end()
                ->scalarNode('scope')->end()
        ;

        $zoneNode
            ->validate()
                ->ifTrue(function ($zone) {
                    $filledTypes = 0;
                    $filledTypes += empty($zone['countries']) ? 0 : 1;
                    $filledTypes += empty($zone['zones']) ? 0 : 1;
                    $filledTypes += empty($zone['provinces']) ? 0 : 1;

                    return $filledTypes !== 1;
                })
                ->thenInvalid('Zone must have only one type of members ("countries", "zones", "provinces")')
        ;
    }

    /**
     * @param array $countriesCodes
     * @param array $countriesProvinces
     */
    private function loadCountriesWithProvinces(array $countriesCodes, array $countriesProvinces)
    {
        $countries = [];
        foreach ($countriesCodes as $countryCode) {
            /** @var CountryInterface $country */
            $country = $this->countryFactory->createNew();
            $country->enable();
            $country->setCode($countryCode);

            $this->countryManager->persist($country);

            $countries[$countryCode] = $country;
        }

        foreach ($countriesProvinces as $countryCode => $provinces) {
            if (!isset($countries[$countryCode])) {
                throw new \InvalidArgumentException(sprintf(
                    'Cannot create provinces for unexisting country "%s"!',
                    $countryCode
                ));
            }

            $this->loadProvincesForCountry($provinces, $countries[$countryCode]);
        }
    }

    /**
     * @param array $zones
     * @param \Closure $zoneValidator
     */
    private function loadZones(array $zones, \Closure $zoneValidator)
    {
        foreach ($zones as $zoneCode => $zoneOptions) {
            $zoneName = $zoneOptions['name'];

            try {
                $zoneValidator($zoneOptions);

                $zoneType = $this->getZoneType($zoneOptions);
                $zoneMembers = $this->getZoneMembers($zoneOptions);

                /** @var ZoneInterface $zone */
                $zone = $this->zoneFactory->createWithMembers($zoneMembers);
                $zone->setCode($zoneCode);
                $zone->setName($zoneName);
                $zone->setType($zoneType);

                $this->zoneManager->persist($zone);
            } catch (\InvalidArgumentException $exception) {
                throw new \InvalidArgumentException(sprintf(
                    'An exception was thrown during loading zone "%s" with code "%s"!',
                    $zoneName,
                    $zoneCode
                ), 0, $exception);
            }
        }
    }

    /**
     * @param array $provinces
     * @param CountryInterface $country
     */
    private function loadProvincesForCountry(array $provinces, CountryInterface $country)
    {
        foreach ($provinces as $provinceCode => $provinceName) {
            /** @var ProvinceInterface $province */
            $province = $this->provinceFactory->createNew();

            $province->setCode($provinceCode);
            $province->setName($provinceName);

            $country->addProvince($province);

            $this->provinceManager->persist($province);
        }
    }

    /**
     * @see ZoneInterface
     *
     * @param array $zoneOptions
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function getZoneType(array $zoneOptions)
    {
        switch (true) {
            case count($zoneOptions['countries']) > 0:
                return ZoneInterface::TYPE_COUNTRY;
            case count($zoneOptions['provinces']) > 0:
                return ZoneInterface::TYPE_PROVINCE;
            case count($zoneOptions['zones']) > 0:
                return ZoneInterface::TYPE_ZONE;
            default:
                throw new \InvalidArgumentException('Cannot resolve zone type!');
        }
    }

    /**
     * @param array $zoneOptions
     *
     * @return array
     */
    private function getZoneMembers(array $zoneOptions)
    {
        $zoneType = $this->getZoneType($zoneOptions);

        switch ($zoneType) {
            case ZoneInterface::TYPE_COUNTRY:
                return $zoneOptions['countries'];
            case ZoneInterface::TYPE_PROVINCE:
                return $zoneOptions['provinces'];
            case ZoneInterface::TYPE_ZONE:
                return $zoneOptions['zones'];
            default:
                throw new \InvalidArgumentException('Cannot resolve zone members!');
        }
    }

    /**
     * @param array $options
     *
     * @return \Closure
     */
    private function provideZoneValidator(array $options)
    {
        $memberValidators = [
            ZoneInterface::TYPE_COUNTRY => function ($countryCode) use ($options) {
                if (in_array($countryCode, $options['countries'], true)) {
                    return;
                }

                throw new \InvalidArgumentException(sprintf(
                    'Could not find country "%s", defined ones are: %s!',
                    $countryCode,
                    implode(', ', $options['countries'])
                ));
            },
            ZoneInterface::TYPE_PROVINCE => function ($provinceCode) use ($options) {
                $foundProvinces = [];
                foreach ($options['provinces'] as $provinces) {
                    if (isset($provinces[$provinceCode])) {
                        return;
                    }

                    $foundProvinces = array_merge($foundProvinces, array_keys($provinces));
                }

                throw new \InvalidArgumentException(sprintf(
                    'Could not find province "%s", defined ones are: %s!',
                    $provinceCode,
                    implode(', ', $options['countries'])
                ));
            },
            ZoneInterface::TYPE_ZONE => function ($zoneCode) use ($options) {
                if (isset($options['zones'][$zoneCode])) {
                    return;
                }

                throw new \InvalidArgumentException(sprintf(
                    'Could not find zone "%s", defined ones are: %s!',
                    $zoneCode,
                    implode(', ', array_keys($options['zones']))
                ));
            }
        ];

        return function (array $zoneOptions) use ($memberValidators) {
            $zoneType = $this->getZoneType($zoneOptions);
            $zoneMembers = $this->getZoneMembers($zoneOptions);

            foreach ($zoneMembers as $zoneMember) {
                $memberValidators[$zoneType]($zoneMember);
            }
        };
    }
}
