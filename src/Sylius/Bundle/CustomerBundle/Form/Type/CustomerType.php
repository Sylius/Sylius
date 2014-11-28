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
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\Form\FormBuilderInterface;

class CustomerType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'text', array(
                'label'    => 'sylius.form.customer.email',
                'disabled' => true,
            ))
            ->add('firstName', 'text', array(
                'label' => 'sylius.form.customer.first_name',
            ))
            ->add('lastName', 'text', array(
                'label' => 'sylius.form.customer.last_name',
            ))
            ->add('gender', 'choice', array(
                'label'   => 'sylius.form.customer.gender.header',
                'choices' => array(
                    CustomerInterface::GENDER_FEMALE => 'sylius.form.customer.gender.female',
                    CustomerInterface::GENDER_MALE   => 'sylius.form.customer.gender.male',
                ),
                'empty_value' => 'sylius.form.customer.gender.not_selected'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_customer';
    }
}
