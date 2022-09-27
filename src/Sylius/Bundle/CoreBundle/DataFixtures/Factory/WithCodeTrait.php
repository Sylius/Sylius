<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithCodeTrait
{
    public function withCode(string $code): static
    {
        return $this->addState(['code' => $code]);
    }
}
