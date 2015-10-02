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
class GuestCustomerFormListener implements EventSubscriberInterface
{
    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @param RepositoryInterface      $customerRepository
     * @param CustomerContextInterface $customerContext
     */
    public function __construct(RepositoryInterface $customerRepository, CustomerContextInterface $customerContext)
    {
        $this->customerRepository = $customerRepository;
        $this->customerContext = $customerContext;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function preSubmit(FormEvent $event)
    {
        $rawData = $event->getData();
        $form = $event->getForm();

        $customer = $this->getCustomerFromProperSource($rawData, $form);

        $form->setData($customer);
    }

    /**
     * @param array         $rawData
     * @param FormInterface $form
     *
     * @return CustomerInterface|null
     */
    protected function getCustomerFromProperSource($rawData, FormInterface $form)
    {
        if (null !== $customer = $this->customerContext->getCustomer()) {
            $form->remove('email');

            return $customer;
        }

        if (!isset($rawData['email']{0})) {
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
        $customer = $this->customerRepository->findOneBy(array('email' => $email));

        if (null !== $customer && null !== $customer->getUser()) {
            return null;
        }

        if (null === $customer) {
            $customer = $this->customerRepository->createNew();
            $customer->setEmail($email);
        }

        return $customer;
    }
}
