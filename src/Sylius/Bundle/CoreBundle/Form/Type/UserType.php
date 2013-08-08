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

use FOS\UserBundle\Form\Type\ProfileFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class UserType extends ProfileFormType
{
    private $dataClass;

    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(FormEvents::PRE_BIND, function (FormEvent $event) {
                $data = $event->getData();

                if (!array_key_exists('differentBillingAddress', $data) || false === $data['differentBillingAddress']) {
                    $data['billingAddress'] = $data['shippingAddress'];

                    $event->setData($data);
                }
            })
            ->add('firstName', 'text', array(
                'label' => 'sylius.form.user.first_name'
            ))
            ->add('lastName', 'text', array(
                'label' => 'sylius.form.user.last_name'
            ))
        ;

        $this->buildUserForm($builder, $options);

        $builder
            ->add('plainPassword', 'password', array(
                'label' => 'sylius.form.user.password'
            ))
            ->add('enabled', 'checkbox', array(
                'label' => 'sylius.form.user.enabled'
            ))
            ->add('shippingAddress', 'sylius_address', array(
                'label' => 'sylius.form.user.shipping_address'
            ))
            ->add('differentBillingAddress', 'checkbox', array(
                'mapped' => false,
                'label'  => 'sylius.form.user.different_billing_address'
            ))
            ->add('billingAddress', 'sylius_address', array(
                'label' => 'sylius.form.user.billing_address'
            ))
            ->remove('username')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => $this->dataClass,
            'validation_groups'  => array('Profile', 'sylius'),
            'cascade_validation' => true,
            'intention'          => 'profile',
        ));
    }

    public function getName()
    {
        return 'sylius_user';
    }
}
