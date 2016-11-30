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

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 * @author Dmitrijs Balabka <dmitry.balabka@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CustomerGuestType extends AbstractResourceType
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
    public function __construct(
        $dataClass,
        array $validationGroups,
        RepositoryInterface $customerRepository
    ) {
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
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (!isset($data['email'])) {
                    return;
                }

                $customer = $this->customerRepository->findOneBy(['email' => $data['email']]);

                // assign customer only if there is no corresponding user account
                if (null !== $customer && null === $customer->getUser()) {
                    $form = $event->getForm();
                    $form->setData($customer);
                }
            })
            ->setDataLocked(false)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_customer_guest';
    }
}
