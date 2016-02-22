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
     * @param FactoryInterface $countryFactory
     * @param RepositoryInterface $countryRepository
     * @param CountryNameConverterInterface $countryNameConverter
     */
    public function __construct(
        FactoryInterface $countryFactory,
        RepositoryInterface $countryRepository,
        CountryNameConverterInterface $countryNameConverter
    ) {
        $this->countryFactory = $countryFactory;
        $this->countryRepository = $countryRepository;
        $this->countryNameConverter = $countryNameConverter;
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

            $this->createCountryNamed(trim($countryName));
        }
    }

    /**
     * @param string $name
     */
    private function createCountryNamed($name)
    {
        /** @var CountryInterface $country */
        $country = $this->countryFactory->createNew();
        $country->setCode($this->countryNameConverter->convertToCode($name));

        $this->countryRepository->add($country);
    }
}
