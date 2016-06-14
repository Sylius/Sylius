<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Checkout;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ShippingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('shipments', 'collection', [
            'type' => 'sylius_checkout_shipment',
            'label' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => ['sylius'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_shop_checkout_shipping';
    }
}
