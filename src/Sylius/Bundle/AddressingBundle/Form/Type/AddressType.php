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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Address form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class AddressType extends AbstractType
{
    const SHIPPABLE_VALIDATION_GROUP = 'shippable';

    /**
     * Data class.
     *
     * @var string
     */
    protected $dataClass;

    /**
     * Validation groups.
     *
     * @var string
     */
    protected $validationGroups;

    /**
     * @var EventSubscriberInterface
     */
    protected $eventListener;

    /**
     * Constructor.
     *
     * @param string                   $dataClass
     * @param array                    $validationGroups
     * @param EventSubscriberInterface $eventListener
     */
    public function __construct($dataClass, array $validationGroups, EventSubscriberInterface $eventListener)
    {
        $this->dataClass = $dataClass;
        $this->validationGroups = $validationGroups;
        $this->eventListener = $eventListener;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber($this->eventListener)
            ->add('firstName', 'text', array(
                'label' => 'sylius.form.address.first_name'
            ))
            ->add('lastName', 'text', array(
                'label' => 'sylius.form.address.last_name'
            ))
            ->add('company', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.address.company'
            ))
            ->add('country', 'sylius_country_choice', array(
                'label' => 'sylius.form.address.country',
                'empty_value' => 'sylius.form.country.select'
            ))
            ->add('street', 'text', array(
                'label' => 'sylius.form.address.street'
            ))
            ->add('city', 'text', array(
                'label' => 'sylius.form.address.city'
            ))
            ->add('postcode', 'text', array(
                'label' => 'sylius.form.address.postcode'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $validationGroups = $this->validationGroups;
        $shippableValidationGroup = self::SHIPPABLE_VALIDATION_GROUP;

        $resolver
            ->setDefaults(array(
                'data_class'        => $this->dataClass,
                'validation_groups' => function(Options $options) use ($validationGroups, $shippableValidationGroup) {
                    if ($options['shippable']) {
                        $validationGroups[] = $shippableValidationGroup;
                    }

                    return $validationGroups;
                },
                'shippable'         => false
            ))
            ->setAllowedTypes(array(
                'shippable' => 'bool'
            ))
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
