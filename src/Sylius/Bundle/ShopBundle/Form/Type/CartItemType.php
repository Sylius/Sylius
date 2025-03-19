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

namespace Sylius\Bundle\ShopBundle\Form\Type;

use Sylius\Bundle\OrderBundle\Form\Type\CartItemType as BaseCartItemType;
use Symfony\Component\Form\AbstractType;

final class CartItemType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'sylius_shop_cart_item';
    }

    public function getParent(): string
    {
        return BaseCartItemType::class;
    }
}
