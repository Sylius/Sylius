<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class AddressType extends AbstractResourceType
{
    /**
     * @var EventSubscriberInterface
     */
    protected $eventListener;

    /**
     * @param string                   $dataClass
     * @param string[]                 $validationGroups
     * @param EventSubscriberInterface $eventListener
     */
    public function __construct($dataClass, array $validationGroups, EventSubscriberInterface $eventListener)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->eventListener = $eventListener;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array(
                'label' => 'sylius.form.address.first_name',
            ))
            ->add('lastName', 'text', array(
                'label' => 'sylius.form.address.last_name',
            ))
            ->add('phoneNumber', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.address.phone_number',
            ))
            ->add('company', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.address.company',
            ))
            ->add('country', 'sylius_country_code_choice', array(
                'label' => 'sylius.form.address.country',
                'enabled' => true,
            ))
            ->add('street', 'text', array(
                'label' => 'sylius.form.address.street',
            ))
            ->add('city', 'text', array(
                'label' => 'sylius.form.address.city',
            ))
            ->add('postcode', 'text', array(
                'label' => 'sylius.form.address.postcode',
            ))
            ->addEventSubscriber($this->eventListener)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults(array(
                'validation_groups' => function (Options $options) {
                    if ($options['shippable']) {
                        $this->validationGroups[] = 'shippable';
                    }

                    return $this->validationGroups;
                },
                'shippable' => false,
            ))
            ->setAllowedTypes('shippable', 'bool')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_address';
    }
}
