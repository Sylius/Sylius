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

namespace Sylius\Component\Core\Resolver;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Resolver\ShippableCountriesResolverInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ShippableCountriesResolver implements ShippableCountriesResolverInterface
{
    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @param RepositoryInterface $countryRepository
     */
    public function __construct(RepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * @param ChannelInterface $channel
     *
     * @return array
     */
    public function getShippableCountries(ChannelInterface $channel): array
    {
        $countries = $channel->getShippableCountries();

        if ($countries->isEmpty()) {
            $countries = new ArrayCollection($this->countryRepository->findAll());
        }

        $countries = $countries->getValues();

        $keys = array_map(function (CountryInterface $country) {
            return $country->getName();
        }, $countries);

        $values = array_map(function (CountryInterface $country) {
            return $country->getCode();
        }, $countries);

        return array_combine($keys, $values);
    }
}
