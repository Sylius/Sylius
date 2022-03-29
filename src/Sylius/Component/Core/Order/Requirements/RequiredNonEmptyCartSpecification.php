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

namespace Sylius\Component\Core\Order\Requirements;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Specification\CompositeSpecificationInterface;

class RequiredNonEmptyCartSpecification extends CompositeSpecificationInterface
{
    public function isSatisfiedBy(object $candidate): bool
    {
        if (!$candidate instanceof OrderInterface) {
            return false;
        }

        return !$candidate->isEmpty();
    }
}
