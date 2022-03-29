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
use Sylius\Component\Core\Order\Requirements\RequiredBillingAddressSpecification;
use Sylius\Component\Core\Order\Requirements\RequiredNonEmptyCartSpecification;
use Sylius\Component\Core\Order\Requirements\RequiredPaymentSpecification;
use Sylius\Component\Core\Order\Requirements\RequiredShippingSpecification;
use Sylius\Component\Core\Specification\Specification;

class CompleteStepGuard implements OrderGuardInterface
{
    /**
     * @param iterable<Specification> $requirements
     */
    public function __construct(private iterable $requirements)
    {
    }

    public function isSatisfiedBy(OrderInterface $order): bool
    {
// Iterable approach
//        foreach ($this->requirements as $requirement) {
//            if (!$requirement->isSatisfiedBy($order)) {
//                return false;
//            }
//        }
//
//        return true;

// Dynamic Tree approach
//        $first = array_shift($this->requirements);
//
//        foreach ($this->requirements as $requirement) {
//            $first = $first->and($requirement);
//        }
//
//        return $first->isSatisfiedBy($order);

// Static approach
//        $requirements = new RequiredNonEmptyCartSpecification();
//        $requirements
//            ->and(new RequiredBillingAddressSpecification())
//            ->and(new RequiredShippingSpecification())
//            ->and(new RequiredPaymentSpecification())
//        ;
//
//        return $requirements->isSatisfiedBy($order);
    }
}
