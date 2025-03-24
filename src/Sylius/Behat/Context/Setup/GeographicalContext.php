<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;

final readonly class GeographicalContext implements Context
{
    /**
     * @param FactoryInterface<CountryInterface> $countryFactory
     * @param FactoryInterface<ProvinceInterface> $provinceFactory
     * @param RepositoryInterface<CountryInterface> $countryRepository
     */
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private FactoryInterface $countryFactory,
        private FactoryInterface $provinceFactory,
        private RepositoryInterface $countryRepository,
        private CountryNameConverterInterface $countryNameConverter,
        private ObjectManager $countryManager,
    ) {
    }

    /**
     * @Given /^the store ships to "([^"]+)"$/
     * @Given /^the store ships to "([^"]+)" and "([^"]+)"$/
     * @Given /^the store ships to "([^"]+)", "([^"]+)" and "([^"]+)"$/
     */
    public function storeShipsTo(string ...$countriesNames): void
    {
        foreach ($countriesNames as $countryName) {
            $this->countryRepository->add($this->createCountryNamed(trim($countryName)));
        }
    }

    /**
     * @Given /^(?:the|this) store operates in "([^"]+)"$/
     * @Given /^(?:the|this) store operates in the "([^"]+)"$/
     * @Given /^(?:the|this) store operates in "([^"]*)" and "([^"]+)"$/
     * @Given /^(?:the|this) store(?:| also) has country "([^"]+)"$/
     */
    public function theStoreOperatesInThe(string ...$countriesNames): void
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
    public function theStoreHasDisabledCountry(string $countryName): void
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
    public function theCountryHasProvinceWithCode(CountryInterface $country, string $name, string $code): void
    {
        /** @var ProvinceInterface $province */
        $province = $this->provinceFactory->createNew();

        $province->setName($name);
        $province->setCode($code);
        $country->addProvince($province);

        $this->sharedStorage->set('province', $province);
        $this->countryManager->flush();
    }

    private function createCountryNamed(string $name): CountryInterface
    {
        /** @var CountryInterface $country */
        $country = $this->countryFactory->createNew();
        $country->setCode($this->countryNameConverter->convertToCode($name));

        return $country;
    }
}
