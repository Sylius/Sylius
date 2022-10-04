<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithTaxaTrait
{
    public function withTaxa(array $taxa): self
    {
        return $this->addState(['taxa' => $taxa]);
    }
}
