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
 * RecurringSubscriptionType
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class RecurringSubscriptionType extends SubscriptionType
{
    public static $intervalChoices = array(
        'days' => "sylius.form.subscription.interval_units.days",
        'months' => "sylius.form.subscription.interval_units.months",
        'years' => "sylius.form.subscription.interval_units.years",
    );

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('intervalUnit', 'choice', array(
            'label' => 'sylius.form.subscription.interval_unit',
            'choices' => self::$intervalChoices
        ));

        $builder->add('intervalFrequency', 'integer', array(
            'label' => 'sylius.form.subscription.interval_frequency'
        ));

        $builder->add('maxCycles', 'integer', array(
            'required' => false,
            'label' => 'sylius.form.subscription.max_cycles'
        ));
    }
}
