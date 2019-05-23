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

class TaxesExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_Function('sylius_tax', [$this, 'getAmount']),
        ];
    }

    public function getAmount(iterable $taxes, bool $returnIncluded = false): int
    {
        $taxesArray = (array) $taxes->toArray();
        $totalAmount = (int) 0;

        foreach ($taxesArray as $item) {
            if ($item->isNeutral() == $returnIncluded) {
                $totalAmount = $totalAmount + $item->getAmount();
            }
        }

        return $totalAmount;
    }
}
