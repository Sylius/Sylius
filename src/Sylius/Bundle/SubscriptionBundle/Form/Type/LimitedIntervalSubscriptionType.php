<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\SubscriptionBundle\Form\Type;


use Symfony\Component\Form\FormBuilderInterface;

/**
 * LimitedIntervalSubscriptionType
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class LimitedIntervalSubscriptionType extends SubscriptionType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('interval', 'integer', array(
            'label' => 'sylius.form.subscription.interval'
        ));
        $builder->add('limit', 'integer', array(
            'required' => false,
            'label' => 'sylius.form.subscription.limit'
        ));
    }
}