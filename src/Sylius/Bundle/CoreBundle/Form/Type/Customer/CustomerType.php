<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Customer;

use Sylius\Bundle\CustomerBundle\Form\Type\CustomerType as BaseCustomerType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerType extends BaseCustomerType
{
    /**
     * @var EventSubscriberInterface
     */
    private $addUserFormSubscriber;

    /**
     * @param string $dataClass
     * @param string[] $validationGroups
     * @param EventSubscriberInterface $addUserFormSubscriber
     */
    public function __construct(
        $dataClass,
        array $validationGroups = [],
        EventSubscriberInterface $addUserFormSubscriber
    ) {
        parent::__construct($dataClass, $validationGroups);
        $this->addUserFormSubscriber = $addUserFormSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->addEventSubscriber($this->addUserFormSubscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->dataClass,
            'validation_groups' => $this->validationGroups,
            'cascade_validation' => true,
        ]);
    }
}
