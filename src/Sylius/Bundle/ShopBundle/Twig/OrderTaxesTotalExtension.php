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

namespace Sylius\Bundle\ShopBundle\Twig;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\AdjustmentInterface as BaseAdjustmentInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

@trigger_error(
    'The "Sylius\Bundle\ShopBundle\Twig\OrderTaxesTotalExtension" class is deprecated since Sylius 1.12 and will be removed in 2.0. Use methods "getTaxExcludedTotal" and "getTaxIncludedTotal" from "Sylius\Component\Core\Model\Order" instead.',
    \E_USER_DEPRECATED,
);

class OrderTaxesTotalExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_order_tax_included', [$this, 'getIncludedTax']),
            new TwigFunction('sylius_order_tax_excluded', [$this, 'getExcludedTax']),
        ];
    }

    public function getIncludedTax(OrderInterface $order): int
    {
        return $this->getAmount($order, true);
    }

    public function getExcludedTax(OrderInterface $order): int
    {
        return $this->getAmount($order, false);
    }

    private function getAmount(OrderInterface $order, bool $isNeutral): int
    {
        return array_reduce(
            $order->getAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->toArray(),
            static fn (int $total, BaseAdjustmentInterface $adjustment) => $isNeutral === $adjustment->isNeutral() ? $total + $adjustment->getAmount() : $total,
            0,
        );
    }
}
