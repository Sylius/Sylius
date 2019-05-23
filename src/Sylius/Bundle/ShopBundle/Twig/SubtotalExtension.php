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

class SubtotalExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_Function('sylius_subtotal', [$this, 'getSubtotal']),
        ];
    }

    public function getSubtotal(iterable $items): int
    {
        $itemsArray = (array) $items->toArray();
        $subtotal = (int) 0;

        foreach ($itemsArray as $item) {
            $subtotal = $subtotal + $item->getSubtotal();
        }

        return $subtotal;
    }
}
