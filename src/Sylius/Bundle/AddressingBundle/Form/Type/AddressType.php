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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class AddressType extends AbstractResourceType
{
    /**
     * @var EventSubscriberInterface
     */
    private $buildAddressFormSubscriber;

    /**
     * @param string $dataClass
     * @param string[] $validationGroups
     * @param EventSubscriberInterface $buildAddressFormSubscriber
     */
    public function __construct($dataClass, array $validationGroups, EventSubscriberInterface $buildAddressFormSubscriber)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->buildAddressFormSubscriber = $buildAddressFormSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'sylius.form.address.first_name',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'sylius.form.address.last_name',
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => false,
                'label' => 'sylius.form.address.phone_number',
            ])
            ->add('company', TextType::class, [
                'required' => false,
                'label' => 'sylius.form.address.company',
            ])
            ->add('countryCode', CountryCodeChoiceType::class, [
                'label' => 'sylius.form.address.country',
                'enabled' => true,
            ])
            ->add('street', TextType::class, [
                'label' => 'sylius.form.address.street',
            ])
            ->add('city', TextType::class, [
                'label' => 'sylius.form.address.city',
            ])
            ->add('postcode', TextType::class, [
                'label' => 'sylius.form.address.postcode',
            ])
            ->addEventSubscriber($this->buildAddressFormSubscriber)
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
    public function getBlockPrefix()
    {
        return 'sylius_address';
    }
}
