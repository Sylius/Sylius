<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

use Sylius\Component\Addressing\Model\CountryInterface;
use Zenstruck\Foundry\Proxy;

interface WithCountryInterface
{
    /**
     * @return $this
     */
    public function withCountry(Proxy|CountryInterface|string $country): self;
}
