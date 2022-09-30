<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithStatusTrait
{
    public function withStatus(string $status): static
    {
        return $this->addState(['status' => $status]);
    }
}
