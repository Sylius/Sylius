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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * RecurringSubscriptionType
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class RecurringSubscriptionType extends SubscriptionType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if (!$options['simple']) {
            $builder
                ->add('interval', 'sylius_date_interval', array(
                    'label' => 'sylius.form.subscription.interval_unit'
                ))
                ->add('maxCycles', 'integer', array(
                    'required' => false,
                    'label' => 'sylius.form.subscription.max_cycles'
                ))
            ;
        } else {
            $builder
                ->add('interval', 'sylius_date_interval', array(
                    'label' => false,
                    'required' => false
                ))
                ->remove('quantity')
                ->remove('scheduledDate')
                ->remove('maxCycles')
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'simple' => false
        ));
    }
}
