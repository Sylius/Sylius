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
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Intl\Intl;

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
     * @param FactoryInterface $countryFactory
     * @param RepositoryInterface $countryRepository
     */
    public function __construct(
        FactoryInterface $countryFactory,
        RepositoryInterface $countryRepository
    ) {
        $this->countryFactory = $countryFactory;
        $this->countryRepository = $countryRepository;
    }

    /**
     * @Given /^store ships to "([^"]+)"$/
     * @Given /^store ships to "([^"]+)" and "([^"]+)"$/
     * @Given /^store ships to "([^"]+)", "([^"]+)" and "([^"]+)"$/
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
        $country->setCode($this->getCountryCodeByEnglishCountryName($name));

        $this->countryRepository->add($country);
    }

    /**
     * @param string $name
     *
     * @return string
     *
     * @throws \InvalidArgumentException If name is not found in country code registry.
     */
    private function getCountryCodeByEnglishCountryName($name)
    {
        $names = Intl::getRegionBundle()->getCountryNames('en');
        $countryCode = array_search($name, $names, true);

        if (null === $countryCode) {
            throw new \InvalidArgumentException(sprintf(
                'Country "%s" not found! Available names: %s.', $name, implode(', ', $names)
            ));
        }

        return $countryCode;
    }
}
