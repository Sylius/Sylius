<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Component\Addressing\Model\CountryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @mixin ModelFactory
 */
trait WithCountryTrait
{
    public function withCountry(Proxy|CountryInterface|string $country): static
    {
        return $this->addState(['country' => $country]);
    }
}
