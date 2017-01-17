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

use Sylius\Bundle\CoreBundle\Form\EventSubscriber\CustomerRegistrationFormSubscriber;
use Sylius\Bundle\CoreBundle\Form\Type\User\ShopUserRegistrationType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class CustomerSimpleRegistrationType extends AbstractResourceType
{
    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * @param string $dataClass
     * @param array $validationGroups
     * @param RepositoryInterface $customerRepository
     */
    public function __construct($dataClass, array $validationGroups, RepositoryInterface $customerRepository)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'sylius.form.customer.email',
            ])
            ->add('user', ShopUserRegistrationType::class, [
                'label' => false,
                'constraints' => [new Valid()],
            ])
            ->addEventSubscriber(new CustomerRegistrationFormSubscriber($this->customerRepository))
            ->setDataLocked(false)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->dataClass,
            'validation_groups' => $this->validationGroups,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_customer_simple_registration';
    }
}
