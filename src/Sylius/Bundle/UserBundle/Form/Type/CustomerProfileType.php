<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerProfileType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add('firstName', 'text', array(
                'label' => 'sylius.form.customer.first_name',
            ))
            ->add('lastName', 'text', array(
                'label' => 'sylius.form.customer.last_name',
            ))
            ->add('email', 'email', array(
                'label' => 'sylius.form.customer.email',
            ))
            ->add('birthday', 'birthday', array(
                'label'    => 'sylius.form.customer.birthday',
                'widget'   => 'single_text',
                'required' => false,
            ))
            ->add('gender', 'sylius_gender')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_customer_profile';
    }
}
