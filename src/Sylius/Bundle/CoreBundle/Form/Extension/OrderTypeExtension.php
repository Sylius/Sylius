<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\AddressingBundle\Form\Type\AddressType;
use Sylius\Bundle\OrderBundle\Form\Type\OrderType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class OrderTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shippingAddress', AddressType::class)
            ->add('billingAddress', AddressType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return OrderType::class;
    }
}
