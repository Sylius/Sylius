<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

interface WithCurrenciesInterface
{
    public function withCurrencies(array $currencies): static;
}
