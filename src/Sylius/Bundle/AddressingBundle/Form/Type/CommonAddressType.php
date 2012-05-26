<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Common address form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class CommonAddressType extends AddressType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', 'text', array(
                'label' => 'sylius_addressing.label.address.firstname'
            ))
            ->add('lastname', 'text', array(
                'label' => 'sylius_addressing.label.address.lastname'
            ))
            ->add('street', 'text', array(
                'label' => 'sylius_addressing.label.address.street'
            ))
            ->add('city', 'text', array(
                'label' => 'sylius_addressing.label.address.city'
            ))
            ->add('postcode', 'text', array(
                'label' => 'sylius_addressing.label.address.postcode'
            ))
        ;
    }
}
