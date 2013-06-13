<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

/**
 * Profile form.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array(
                'label'    => 'sylius.form.profile.firstName'
            ))
            ->add('lastName', 'text', array(
                'label'    => 'sylius.form.profile.lastName'
            ))
            ->add('email', 'email', array(
                'label'    => 'sylius.form.profile.email'
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_user_profile';
    }
}
