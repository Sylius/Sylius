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

namespace Sylius\Component\Core\StateGuard;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Specification\Specification;

class ProcessStepGuard implements OrderGuardInterface
{
    /**
     * @param iterable<Specification> $requirements
     */
    public function __construct(private iterable $requirements)
    {
    }

    public function isSatisfiedBy(OrderInterface $order): bool
    {
        foreach ($this->requirements as $requirement) {
            if (!$requirement->isSatisfiedBy($order)) {
                return false;
            }
        }

        return true;
    }
}
