<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Twig;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Model\AdjustmentInterface as BaseAdjustmentInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class OrderUnitTaxesExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_admin_order_unit_tax_included', [$this, 'getIncludedTax']),
            new TwigFunction('sylius_admin_order_unit_tax_excluded', [$this, 'getExcludedTax']),
        ];
    }

    public function getIncludedTax(OrderItemInterface $orderItemUnit): int
    {
        return $this->getAmount($orderItemUnit, true);
    }

    public function getExcludedTax(OrderItemInterface $orderItemUnit): int
    {
        return $this->getAmount($orderItemUnit, false);
    }

    private function getAmount(OrderItemInterface $orderItem, bool $neutral): int
    {
        $total = array_reduce(
            $orderItem->getAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->toArray(),
            static fn (int $total, BaseAdjustmentInterface $adjustment) => $neutral === $adjustment->isNeutral() ? $total + $adjustment->getAmount() : $total,
            0,
        );

        return $total;
    }
}
