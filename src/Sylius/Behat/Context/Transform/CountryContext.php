<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class CountryContext implements Context
{
    /**
     * @var CountryNameConverterInterface
     */
    private $countryNameConverter;

    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @param CountryNameConverterInterface $countryNameConverter
     * @param RepositoryInterface $countryRepository
     */
    public function __construct(
        CountryNameConverterInterface $countryNameConverter,
        RepositoryInterface $countryRepository
    ) {
        $this->countryNameConverter = $countryNameConverter;
        $this->countryRepository = $countryRepository;
    }

    /**
     * @Transform /^country "([^"]+)"$/
     * @Transform /^"([^"]+)" country$/
     * @Transform /^"([^"]+)" as shipping country$/
     * @Transform :country
     */
    public function getCountryByName($countryName)
    {
        $countryCode = $this->countryNameConverter->convertToCode($countryName);
        $country = $this->countryRepository->findOneBy(['code' => $countryCode]);

        Assert::notNull(
            $country,
            sprintf('Country with name "%s" does not exist', $countryName)
        );

        return $country;
    }
}
