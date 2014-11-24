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

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array(
                'label'         => 'sylius.form.user.first_name',
                'property_path' => 'customer.firstName',
            ))
            ->add('lastName', 'text', array(
                'label'         => 'sylius.form.user.last_name',
                'property_path' => 'customer.lastName',
            ))
        ;

        parent::buildForm($builder, $options);

        // remove the username field
        $builder->remove('username');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_user_registration';
    }
}
