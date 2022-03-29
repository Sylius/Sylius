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

class AndSpecification extends CompositeSpecification
{
    public function __construct(
        private SpecificationInterface $left,
        private SpecificationInterface $right,
    ) {
    }

    public function isSatisfiedBy(object $candidate): bool
    {
        return $this->left->isSatisfiedBy($candidate) && $this->right->isSatisfiedBy($candidate);
    }
}
