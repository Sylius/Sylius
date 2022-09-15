<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithFirstNameTrait
{
    public function withFirstName(string $firstName): self
    {
        return $this->addState(['first_name' => $firstName]);
    }
}
