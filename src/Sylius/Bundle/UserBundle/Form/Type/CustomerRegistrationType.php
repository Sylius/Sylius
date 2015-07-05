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
use Sylius\Bundle\UserBundle\Form\EventListener\CustomerRegistrationFormListener;
use Sylius\Bundle\UserBundle\Form\EventListener\UserRegistrationFormListener;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerRegistrationType extends AbstractResourceType
{
    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * @param string              $dataClass
     * @param array               $validationGroups
     * @param RepositoryInterface $customerRepository
     */
    public function __construct($dataClass, array $validationGroups = array(), RepositoryInterface $customerRepository)
    {
        parent::__construct($dataClass, $validationGroups);
        $this->customerRepository = $customerRepository;
    }

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
            ->add('user', 'sylius_user_registration', array(
                'label' => false,
            ))
            ->addEventSubscriber(new CustomerRegistrationFormListener($this->customerRepository))
            ->addEventSubscriber(new UserRegistrationFormListener())
            ->setDataLocked(false)
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
            'cascade_validation' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_customer_registration';
    }
}
