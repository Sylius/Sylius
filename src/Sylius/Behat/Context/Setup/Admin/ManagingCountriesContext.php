<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup\Admin;

use Behat\Behat\Context\Context;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ManagingCountriesContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @var CountryNameConverterInterface
     */
    private $countryNameConverter;

    /**
     * @var FactoryInterface
     */
    private $countryFactory;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface $countryRepository
     * @param CountryNameConverterInterface $countryNameConverter
     * @param FactoryInterface $countryFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $countryRepository,
        CountryNameConverterInterface $countryNameConverter,
        FactoryInterface $countryFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->countryRepository = $countryRepository;
        $this->countryNameConverter = $countryNameConverter;
        $this->countryFactory = $countryFactory;
    }

    /**
     * @Given /^the store has "([^"]*)" country enabled$/
     */
    public function theStoreHasCountryEnabled($countryName)
    {
        $country = $this->createCountryNamed($countryName);
        $country->enable();
        $this->sharedStorage->set('country', $country);
        $this->countryRepository->add($country);
    }

    /**
     * @Given /^the store has "([^"]*)" country disabled$/
     */
    public function theStoreHasCountryDisabled($countryName)
    {
        $country = $this->createCountryNamed($countryName);
        $country->disable();
        $this->sharedStorage->set('country', $country);
        $this->countryRepository->add($country);
    }

    /**
     * @param string $countryName
     *
     * @return CountryInterface
     *
     * @throws \InvalidArgumentException
     */
    private function createCountryNamed($countryName)
    {
        $countryCode = $this->countryNameConverter->convertToCode($countryName);
        /** @var CountryInterface $country */
        $country = $this->countryFactory->createNew();
        $country->setCode($countryCode);

        return $country;
    }
}
