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

use Sylius\Bundle\OrderBundle\Form\Type\CartType as BaseCartType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

final class CartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('items', CollectionType::class, [
                'entry_type' => CartItemType::class,
                'allow_add' => false,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'sylius.form.cart.items',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_shop_cart';
    }

    public function getParent(): string
    {
        return BaseCartType::class;
    }
}
