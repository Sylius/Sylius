<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithPriorityTrait
{
    public function withPriority(int $priority): static
    {
        return $this->addState(['priority' => $priority]);
    }
}
