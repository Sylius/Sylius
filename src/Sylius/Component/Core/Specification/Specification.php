<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Specification;

abstract class Specification
{
    public abstract function isSatisfiedBy(object $candidate): bool;

    public function and(self $specification): self
    {
        return new AndSpecification($this, $specification);
    }

    public function or(self $specification): self
    {
        return new OrSpecification($this, $specification);
    }
}
