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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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
            ->add('firstName', 'text', [
                'label' => 'sylius.form.address.first_name',
            ])
            ->add('lastName', 'text', [
                'label' => 'sylius.form.address.last_name',
            ])
            ->add('phoneNumber', 'text', [
                'required' => false,
                'label' => 'sylius.form.address.phone_number',
            ])
            ->add('company', 'text', [
                'required' => false,
                'label' => 'sylius.form.address.company',
            ])
            ->add('countryCode', 'sylius_country_code_choice', [
                'label' => 'sylius.form.address.country',
                'enabled' => true,
            ])
            ->add('street', 'text', [
                'label' => 'sylius.form.address.street',
            ])
            ->add('city', 'text', [
                'label' => 'sylius.form.address.city',
            ])
            ->add('postcode', 'text', [
                'label' => 'sylius.form.address.postcode',
            ])
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
            ->setDefaults([
                'validation_groups' => function (Options $options) {
                    if ($options['shippable']) {
                        $this->validationGroups[] = 'shippable';
                    }

                    return $this->validationGroups;
                },
                'shippable' => false,
            ])
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
