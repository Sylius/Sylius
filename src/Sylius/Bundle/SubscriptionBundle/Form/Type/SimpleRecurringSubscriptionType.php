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

class SimpleRecurringSubscriptionType extends RecurringSubscriptionType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('interval', 'sylius_date_interval', array(
                'label' => false,
                'required' => false,
                'units' => $this->getIntervalUnits(),
            ))
            ->remove('quantity')
            ->remove('scheduledDate')
            ->remove('maxCycles')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_simple_subscription';
    }
}
