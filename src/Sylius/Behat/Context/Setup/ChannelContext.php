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
use Symfony\Component\Intl\Intl;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
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
    private $defaultChannelFactory;

    /**
     * @var DefaultCountriesFactoryInterface
     */
    private $defaultCountriesFactory;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param DefaultStoreDataInterface $defaultChannelFactory
     * @param DefaultCountriesFactoryInterface $defaultCountriesFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        DefaultStoreDataInterface $defaultChannelFactory,
        DefaultCountriesFactoryInterface $defaultCountriesFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->defaultChannelFactory = $defaultChannelFactory;
        $this->defaultCountriesFactory = $defaultCountriesFactory;
    }

    /**
     * @Given the store is operating on a single channel
     */
    public function thatStoreIsOperatingOnASingleChannel()
    {
        $defaultData = $this->defaultChannelFactory->create();
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

        $this->defaultCountriesFactory->create($countries);
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
