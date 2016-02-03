<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Test\Services;

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class DefaultCountriesFactory implements DefaultCountriesFactoryInterface
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
    public function __construct(FactoryInterface $countryFactory, RepositoryInterface $countryRepository)
    {
        $this->countryFactory = $countryFactory;
        $this->countryRepository = $countryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $countryCodes)
    {
        foreach ($countryCodes as $countryCode) {
            $this->countryRepository->add($this->createCountry($countryCode));
        }
    }

    /**
     * @param string $code
     *
     * @return CountryInterface
     */
    private function createCountry($code)
    {
        $country = $this->countryFactory->createNew();
        $country->setCode($code);

        return $country;
    }
}
