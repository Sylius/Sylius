<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Form\EventListener;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Context\CustomerContextInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class GuestCustomerFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * @var FactoryInterface
     */
    private $customerFactory;

    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @param RepositoryInterface $customerRepository
     * @param FactoryInterface $customerFactory
     * @param CustomerContextInterface $customerContext
     */
    public function __construct(RepositoryInterface $customerRepository, FactoryInterface $customerFactory, CustomerContextInterface $customerContext)
    {
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->customerContext = $customerContext;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function preSubmit(FormEvent $event)
    {
        $rawData = $event->getData();
        $form = $event->getForm();

        if (null == $rawData) {
            $rawData = [];
        }

        $customer = $this->getCustomerFromProperSource($rawData, $form);
        $form->setData($customer);
    }

    /**
     * @param array $rawData
     * @param FormInterface $form
     *
     * @return CustomerInterface|null
     */
    protected function getCustomerFromProperSource(array $rawData, FormInterface $form)
    {
        if (null !== $customer = $this->customerContext->getCustomer()) {
            $form->remove('email');

            return $customer;
        }

        if (!isset($rawData['email'])) {
            return null;
        }

        return $this->createCustomerIfNecessary($rawData['email']);
    }

    /**
     * @param string $email
     *
     * @return CustomerInterface
     */
    protected function createCustomerIfNecessary($email)
    {
        $customer = $this->customerRepository->findOneBy(['email' => $email]);

        // if this email is already a registered user, do not assign this customer (force login)
        if (null !== $customer && null !== $customer->getUser()) {
            return null;
        }

        if (null === $customer) {
            $customer = $this->customerFactory->createNew();
            $customer->setEmail($email);
        }

        return $customer;
    }
}
