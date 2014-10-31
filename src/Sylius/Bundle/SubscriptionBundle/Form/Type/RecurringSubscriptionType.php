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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('interval', 'sylius_date_interval', array(
                'label' => 'sylius.form.subscription.interval',
                'units' => $this->getIntervalUnits(),
            ))
            ->add('maxCycles', 'integer', array(
                'required' => false,
                'label' => 'sylius.form.subscription.max_cycles'
            ))
        ;
    }

    protected function getIntervalUnits()
    {
        return array(
            'd' => 'sylius.form.subscription.interval_units.days',
            'm' => 'sylius.form.subscription.interval_units.months',
            'y' => 'sylius.form.subscription.interval_units.years',
        );
    }
}
