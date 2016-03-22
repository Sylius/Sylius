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
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class GeographicalContext implements Context
{
    /**
     * @var FactoryInterface
     */
    private $countryFactory;

    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @var CountryNameConverterInterface
     */
    private $countryNameConverter;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param FactoryInterface $countryFactory
     * @param RepositoryInterface $countryRepository
     * @param CountryNameConverterInterface $countryNameConverter
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(
        FactoryInterface $countryFactory,
        RepositoryInterface $countryRepository,
        CountryNameConverterInterface $countryNameConverter,
        SharedStorageInterface $sharedStorage
    ) {
        $this->countryFactory = $countryFactory;
        $this->countryRepository = $countryRepository;
        $this->countryNameConverter = $countryNameConverter;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given /^the store ships to "([^"]+)"$/
     * @Given /^the store ships to "([^"]+)" and "([^"]+)"$/
     * @Given /^the store ships to "([^"]+)", "([^"]+)" and "([^"]+)"$/
     */
    public function storeShipsTo($country1, $country2 = null, $country3 = null)
    {
        foreach ([$country1, $country2, $country3] as $countryName) {
            if (null === $countryName) {
                continue;
            }

            $this->countryRepository->add($this->createCountryNamed(trim($countryName)));
        }
    }

    /**
     * @Given /^the store operates in "([^"]*)"$/
     * @Given /^the store has country "([^"]*)"$/
     */
    public function theStoreOperatesIn($countryName)
    {
        $country = $this->createCountryNamed(trim($countryName));
        $this->sharedStorage->set('country', $country);

        $this->countryRepository->add($country);
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
