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

use Sylius\Bundle\UserBundle\Form\EventListener\UserRegistrationFormListener;
use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserRegistrationType extends AbstractResourceType
{
    /**
    * @param string                 $dataClass
    * @param string[]               $validationGroups
    */
    public function __construct($dataClass, array $validationGroups)
    {
        parent::__construct($dataClass, $validationGroups);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new UserRegistrationFormListener())
            ->add('customer', 'sylius_customer_registration', array(
                'label' => false,
            ))
            ->add('plainPassword', 'repeated', array(
                'type'            => 'password',
                'first_options'   => array('label' => 'sylius.form.user.password.label'),
                'second_options'  => array('label' => 'sylius.form.user.password.confirmation'),
                'invalid_message' => 'sylius.user.plainPassword.mismatch',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
            'validation_groups' => $this->validationGroups,
            'cascade_validation' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_user_registration';
    }
}
