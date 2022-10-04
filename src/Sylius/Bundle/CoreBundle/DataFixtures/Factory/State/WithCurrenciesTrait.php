<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithCurrenciesTrait
{
    public function withCurrencies(array $currencies): self
    {
        return $this->addState(['currencies' => $currencies]);
    }
}
