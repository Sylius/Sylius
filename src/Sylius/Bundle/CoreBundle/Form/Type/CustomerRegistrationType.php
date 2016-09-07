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

use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerRegistrationType extends CustomerSimpleRegistrationType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('firstName', 'text', [
                'label' => 'sylius.form.customer.first_name',
            ])
            ->add('lastName', 'text', [
                'label' => 'sylius.form.customer.last_name',
            ])
            ->add('phoneNumber', 'text', [
                'required' => false,
                'label' => 'sylius.form.customer.phone_number',
            ])
            ->add('subscribedToNewsletter', 'checkbox', [
                'required' => false,
                'label' => 'sylius.form.customer.subscribed_to_newsletter',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_customer_registration';
    }
}
