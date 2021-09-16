<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class CountryContext implements Context
{
    private CountryNameConverterInterface $countryNameConverter;

    private RepositoryInterface $countryRepository;

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
     * @Transform /^"([^"]+)" as billing country$/
     * @Transform :country
     * @Transform :otherCountry
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

    /**
     * @Transform :countryCode
     */
    public function getCountryCodeByName(string $countryName): string
    {
        return $this->countryNameConverter->convertToCode($countryName);
    }
}
