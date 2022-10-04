<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

interface WithCurrenciesInterface
{
    /**
     * @return $this
     */
    public function withCurrencies(array $currencies): self;
}
