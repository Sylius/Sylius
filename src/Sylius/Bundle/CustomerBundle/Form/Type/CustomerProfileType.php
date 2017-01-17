<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CustomerBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class CustomerProfileType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'sylius.form.customer.first_name',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'sylius.form.customer.last_name',
            ])
            ->add('email', EmailType::class, [
                'label' => 'sylius.form.customer.email',
            ])
            ->add('birthday', BirthdayType::class, [
                'label' => 'sylius.form.customer.birthday',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('gender', GenderType::class, [
                'label' => 'sylius.form.customer.gender',
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => false,
                'label' => 'sylius.form.customer.phone_number',
            ])
            ->add('subscribedToNewsletter', CheckboxType::class, [
                'required' => false,
                'label' => 'sylius.form.customer.subscribed_to_newsletter',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_customer_profile';
    }
}
