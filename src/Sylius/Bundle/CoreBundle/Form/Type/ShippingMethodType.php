<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType as BaseShippingMethodType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Shipping method form type, extended with zone selection field.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ShippingMethodType extends BaseShippingMethodType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('zone', 'sylius_zone_choice', array(
                'label' => 'sylius.form.shipping_method.zone',
            ));
    }
}
