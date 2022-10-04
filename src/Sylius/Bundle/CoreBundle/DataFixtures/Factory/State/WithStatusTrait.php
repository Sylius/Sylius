<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithStatusTrait
{
    public function withStatus(string $status): self
    {
        return $this->addState(['status' => $status]);
    }
}
