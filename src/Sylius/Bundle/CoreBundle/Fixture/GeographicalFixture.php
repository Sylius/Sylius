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

namespace Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Addressing\Factory\ZoneFactoryInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Intl\Intl;
use Webmozart\Assert\Assert;

class GeographicalFixture extends AbstractFixture
{
    /** @var FactoryInterface */
    private $countryFactory;

    /** @var ObjectManager */
    private $countryManager;

    /** @var FactoryInterface */
    private $provinceFactory;

    /** @var ObjectManager */
    private $provinceManager;

    /** @var ZoneFactoryInterface */
    private $zoneFactory;

    /** @var ObjectManager */
    private $zoneManager;

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
    public function load(array $options): void
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
    public function getName(): string
    {
        return 'geographical';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNodeBuilder = $optionsNode->children();

        $optionsNodeBuilder
            ->arrayNode('countries')
                ->performNoDeepMerging()
                ->defaultValue(array_keys(Intl::getRegionBundle()->getCountryNames()))
                ->scalarPrototype()
        ;

        /** @var ArrayNodeDefinition $provinceNode */
        $provinceNode = $optionsNodeBuilder
            ->arrayNode('provinces')
                ->normalizeKeys(false)
                ->useAttributeAsKey('code')
                ->arrayPrototype()
        ;

        $provinceNode
            ->performNoDeepMerging()
            ->normalizeKeys(false)
            ->useAttributeAsKey('code')
            ->scalarPrototype()
        ;

        /** @var ArrayNodeDefinition $zoneNode */
        $zoneNode = $optionsNodeBuilder
            ->arrayNode('zones')
                ->normalizeKeys(false)
                ->useAttributeAsKey('code')
                ->arrayPrototype()
        ;

        $zoneNode
            ->performNoDeepMerging()
            ->children()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->arrayNode('countries')->scalarPrototype()->end()->end()
                ->arrayNode('zones')->scalarPrototype()->end()->end()
                ->arrayNode('provinces')->scalarPrototype()->end()->end()
                ->scalarNode('scope')->end()
        ;

        $zoneNode
            ->validate()
                ->ifTrue(function (array $zone): bool {
                    $filledTypes = 0;
                    $filledTypes += empty($zone['countries']) ? 0 : 1;
                    $filledTypes += empty($zone['zones']) ? 0 : 1;
                    $filledTypes += empty($zone['provinces']) ? 0 : 1;

                    return $filledTypes !== 1;
                })
                ->thenInvalid('Zone must have only one type of members ("countries", "zones", "provinces")')
        ;
    }

    private function loadCountriesWithProvinces(array $countriesCodes, array $countriesProvinces): void
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
            Assert::keyExists($countries, $countryCode, sprintf('Cannot create provinces for unexisting country "%s"!', $countryCode));

            $this->loadProvincesForCountry($provinces, $countries[$countryCode]);
        }
    }

    private function loadZones(array $zones, \Closure $zoneValidator): void
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

                if (isset($zoneOptions['scope'])) {
                    $zone->setScope($zoneOptions['scope']);
                }

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

    private function loadProvincesForCountry(array $provinces, CountryInterface $country): void
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
     * @throws \InvalidArgumentException
     */
    private function getZoneType(array $zoneOptions): string
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

    private function getZoneMembers(array $zoneOptions): array
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

    private function provideZoneValidator(array $options): \Closure
    {
        $memberValidators = [
            ZoneInterface::TYPE_COUNTRY => function (string $countryCode) use ($options): void {
                if (in_array($countryCode, $options['countries'], true)) {
                    return;
                }

                throw new \InvalidArgumentException(sprintf(
                    'Could not find country "%s", defined ones are: %s!',
                    $countryCode,
                    implode(', ', $options['countries'])
                ));
            },
            ZoneInterface::TYPE_PROVINCE => function (string $provinceCode) use ($options): void {
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
                    implode(', ', $options['provinces'])
                ));
            },
            ZoneInterface::TYPE_ZONE => function (string $zoneCode) use ($options): void {
                if (isset($options['zones'][$zoneCode])) {
                    return;
                }

                throw new \InvalidArgumentException(sprintf(
                    'Could not find zone "%s", defined ones are: %s!',
                    $zoneCode,
                    implode(', ', array_keys($options['zones']))
                ));
            },
        ];

        return function (array $zoneOptions) use ($memberValidators): void {
            $zoneType = $this->getZoneType($zoneOptions);
            $zoneMembers = $this->getZoneMembers($zoneOptions);

            foreach ($zoneMembers as $zoneMember) {
                $memberValidators[$zoneType]($zoneMember);
            }
        };
    }
}
