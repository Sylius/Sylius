<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithNameTrait
{
    public function withName(string $name): self
    {
        return $this->addState(['name' => $name]);
    }
}
