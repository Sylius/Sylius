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
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class GeographicalContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $countryFactory;

    /**
     * @var FactoryInterface
     */
    private $provinceFactory;

    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @var CountryNameConverterInterface
     */
    private $countryNameConverter;

    /**
     * @var ObjectManager
     */
    private $countryManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $countryFactory
     * @param FactoryInterface $provinceFactory
     * @param RepositoryInterface $countryRepository
     * @param CountryNameConverterInterface $countryNameConverter
     * @param ObjectManager $countryManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $countryFactory,
        FactoryInterface $provinceFactory,
        RepositoryInterface $countryRepository,
        CountryNameConverterInterface $countryNameConverter,
        ObjectManager $countryManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->countryFactory = $countryFactory;
        $this->provinceFactory = $provinceFactory;
        $this->countryRepository = $countryRepository;
        $this->countryNameConverter = $countryNameConverter;
        $this->countryManager = $countryManager;
    }

    /**
     * @Given /^the store ships to "([^"]+)"$/
     * @Given /^the store ships to "([^"]+)" and "([^"]+)"$/
     * @Given /^the store ships to "([^"]+)", "([^"]+)" and "([^"]+)"$/
     */
    public function storeShipsTo(...$countriesNames)
    {
        foreach ($countriesNames as $countryName) {
            $this->countryRepository->add($this->createCountryNamed(trim($countryName)));
        }
    }

    /**
     * @Given /^the store operates in "([^"]*)"$/
     * @Given /^the store operates in "([^"]*)" and "([^"]*)"$/
     * @Given /^the store(?:| also) has country "([^"]*)"$/
     */
    public function theStoreOperatesIn(...$countriesNames)
    {
        foreach ($countriesNames as $countryName) {
            $country = $this->createCountryNamed(trim($countryName));
            $this->sharedStorage->set('country', $country);

            $this->countryRepository->add($country);
        }
    }

    /**
     * @Given /^the store has disabled country "([^"]*)"$/
     */
    public function theStoreHasDisabledCountry($countryName)
    {
        $country = $this->createCountryNamed(trim($countryName));
        $country->disable();

        $this->sharedStorage->set('country', $country);
        $this->countryRepository->add($country);
    }

    /**
     * @Given /^(this country)(?:| also) has the "([^"]+)" province with "([^"]+)" code$/
     * @Given /^(?:|the )(country "[^"]+") has the "([^"]+)" province with "([^"]+)" code$/
     */
    public function theCountryHasProvinceWithCode(CountryInterface $country, $name, $code)
    {
        /** @var ProvinceInterface $province */
        $province = $this->provinceFactory->createNew();

        $province->setName($name);
        $province->setCode($code);
        $country->addProvince($province);

        $this->sharedStorage->set('province', $province);
        $this->countryManager->flush();
    }

    /**
     * @param string $name
     *
     * @return CountryInterface
     */
    private function createCountryNamed($name)
    {
        /** @var CountryInterface $country */
        $country = $this->countryFactory->createNew();
        $country->setCode($this->countryNameConverter->convertToCode($name));

        return $country;
    }
}
