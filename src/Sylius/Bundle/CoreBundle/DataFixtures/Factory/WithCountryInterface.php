<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Component\Addressing\Model\CountryInterface;
use Zenstruck\Foundry\Proxy;

interface WithCountryInterface
{
    public function withCountry(Proxy|CountryInterface|string $country): static;
}
