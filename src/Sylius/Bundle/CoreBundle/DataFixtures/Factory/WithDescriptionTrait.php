<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithDescriptionTrait
{
    public function withDescription(string $description): self
    {
        return $this->addState(['description' => $description]);
    }
}
