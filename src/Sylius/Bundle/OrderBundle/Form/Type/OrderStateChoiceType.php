<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Form\Type;

use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Order state choice type.
 *
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class OrderStateChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                OrderInterface::STATE_CART => 'sylius.order.state.checkout',
                OrderInterface::STATE_CART_LOCKED => 'sylius.order.state.cart_locked',
                OrderInterface::STATE_PENDING => 'sylius.order.state.ordered',
                OrderInterface::STATE_CONFIRMED => 'sylius.order.state.order_confirmed',
                OrderInterface::STATE_SHIPPED => 'sylius.order.state.shipped',
                OrderInterface::STATE_ABANDONED => 'sylius.order.state.abandoned',
                OrderInterface::STATE_CANCELLED => 'sylius.order.state.cancelled',
                OrderInterface::STATE_RETURNED => 'sylius.order.state.returned',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_order_state_choice';
    }
}
