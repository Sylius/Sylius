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

namespace Sylius\Bundle\ShopBundle\Form\Type\Checkout;

use Sylius\Bundle\CoreBundle\Form\Type\Checkout\SelectShippingType as BaseSelectShippingType;
use Symfony\Component\Form\AbstractType;

final class SelectShippingType extends AbstractType
{
    public function getParent(): string
    {
        return BaseSelectShippingType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_shop_checkout_select_shipping';
    }
}
