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

class OrderTaxesTotalExtension extends \Twig_Extension
{
    public function getFunctions(): array
    {
        return [
            new \Twig_Function('sylius_order_tax_included', [$this, 'getIncludedTax']),
            new \Twig_Function('sylius_order_tax_excluded', [$this, 'getExcludedTax']),
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
            static function (int $total, BaseAdjustmentInterface $adjustment) use ($isNeutral) {
                return $isNeutral === $adjustment->isNeutral() ? $total + $adjustment->getAmount() : $total;
            },
            0
        );
    }
}
