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
use Sylius\Component\Core\Test\Services\DefaultCountriesFactoryInterface;
use Sylius\Component\Core\Test\Services\DefaultStoreDataInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Intl\Intl;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ChannelContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var DefaultStoreDataInterface
     */
    private $defaultFranceChannelFactory;

    /**
     * @var FactoryInterface
     */
    private $countryFactory;

    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param DefaultStoreDataInterface $defaultFranceChannelFactory
     * @param FactoryInterface $countryFactory
     * @param RepositoryInterface $countryRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        DefaultStoreDataInterface $defaultFranceChannelFactory,
        FactoryInterface $countryFactory,
        RepositoryInterface $countryRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->defaultFranceChannelFactory = $defaultFranceChannelFactory;
        $this->countryFactory = $countryFactory;
        $this->countryRepository = $countryRepository;
    }

    /**
     * @Given the store is operating on a single channel
     */
    public function thatStoreIsOperatingOnASingleChannel()
    {
        $defaultData = $this->defaultFranceChannelFactory->create();
        $this->sharedStorage->setClipboard($defaultData);
    }

    /**
     * @Given /^store ships to "([^"]*)"$/
     * @Given /^store ships to "([^"]*)" and "([^"]*)"$/
     * @Given /^store ships to "([^"]*)", "([^"]*)" and "([^"]*)"$/
     */
    public function storeShipsTo($country1, $country2 = null, $country3 = null)
    {
        $countries = [$this->getCountryCodeByEnglishCountryName($country1)];

        if (null !== $country2) {
            $countries[] = $this->getCountryCodeByEnglishCountryName($country2);
        }

        if (null !== $country3) {
            $countries[] = $this->getCountryCodeByEnglishCountryName($country3);
        }

        foreach ($countries as $country) {
            $this->createCountry($country);
        }
    }

    /**
     * @param string $code
     */
    private function createCountry($code)
    {
        $country = $this->countryFactory->createNew();
        $country->setCode($code);

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
        $countryCode = array_search(trim($name), $names);

        if (null === $countryCode) {
            throw new \InvalidArgumentException(sprintf(
                'Country "%s" not found! Available names: %s.', $name, implode(', ', $names)
            ));
        }

        return $countryCode;
    }
}
