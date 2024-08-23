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

use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType as BaseAddToCartType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;

final class AddToCartType extends AbstractResourceType
{
    public function getBlockPrefix(): string
    {
        return 'sylius_shop_add_to_cart';
    }

    public function getParent(): string
    {
        return BaseAddToCartType::class;
    }
}
