<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithPriorityTrait
{
    public function withPriority(int $priority): self
    {
        return $this->addState(['priority' => $priority]);
    }
}
